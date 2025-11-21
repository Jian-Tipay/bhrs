<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display user's bookings
     */
    public function index()
    {
        $bookings = Booking::with(['property.landlord.user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('content.bookings.index', compact('bookings'));
    }

    /**
     * Show booking form
     */
    public function create(Property $property)
    {
        // Check if property is available
        if (!$property->available || $property->available_slots <= 0) {
            return redirect()->back()->with('error', 'This property is not available for booking.');
        }

        // Check if user already has a pending/approved booking for this property
        $existingBooking = Booking::where('user_id', Auth::id())
            ->where('property_id', $property->id)
            ->whereIn('status', ['Pending', 'Approved', 'Active'])
            ->first();

        if ($existingBooking) {
            return redirect()->back()->with('error', 'You already have an existing booking for this property.');
        }

        return view('content.bookings.create', compact('property'));
    }

    /**
     * Store new booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'move_in_date' => 'required|date|after_or_equal:today',
            'move_out_date' => 'nullable|date|after:move_in_date',
            'message' => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        // Check availability
        if (!$property->available || $property->available_slots <= 0) {
            return redirect()->back()->with('error', 'This property is no longer available.');
        }

        // Check for existing booking
        $existingBooking = Booking::where('user_id', Auth::id())
            ->where('property_id', $property->id)
            ->whereIn('status', ['Pending', 'Approved', 'Active'])
            ->first();

        if ($existingBooking) {
            return redirect()->back()->with('error', 'You already have an existing booking for this property.');
        }

        // Create booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'move_in_date' => $validated['move_in_date'],
            'move_out_date' => $validated['move_out_date'] ?? null,
            'status' => 'Pending',
        ]);

        return redirect()->route('bookings.show', $booking->booking_id)
            ->with('success', 'Booking request submitted successfully! Waiting for landlord approval.');
    }

    /**
     * Show specific booking
     */
    public function show(Booking $booking)
    {
        // Authorization check
        if ($booking->user_id !== Auth::id() && $booking->property->landlord->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access to booking');
        }

        $booking->load(['property.landlord.user', 'user']);

        return view('content.bookings.show', compact('booking'));
    }

    /**
     * Cancel booking (by user)
     */
    public function cancel(Booking $booking)
    {
        // Only owner can cancel
        if ($booking->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Can only cancel pending or approved bookings
        if (!in_array($booking->status, ['Pending', 'Approved'])) {
            return redirect()->back()->with('error', 'Cannot cancel this booking.');
        }

        $booking->update(['status' => 'Cancelled']);

        // Restore available slot
        if ($booking->status === 'Approved') {
            $booking->property->increment('available_slots');
        }

        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Landlord: View all bookings for their properties
     */
    public function landlordBookings()
    {
        $user = Auth::user();
        
        if ($user->role !== 'landlord') {
            abort(403, 'Unauthorized access');
        }

        $bookings = Booking::with(['property', 'user'])
            ->whereHas('property.landlord', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('bookings.landlord-index', compact('bookings'));
    }

    /**
     * Landlord: Approve booking
     */
    public function approve(Booking $booking)
    {
        $user = Auth::user();
        
        // Check if user is the landlord
        if ($booking->property->landlord->user_id !== $user->id && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== 'Pending') {
            return redirect()->back()->with('error', 'Can only approve pending bookings.');
        }

        // Check if property still has available slots
        if ($booking->property->available_slots <= 0) {
            return redirect()->back()->with('error', 'No available slots remaining.');
        }

        $booking->update(['status' => 'Approved']);
        
        // Decrease available slots
        $booking->property->decrement('available_slots');

        // Update property availability if no slots left
        if ($booking->property->available_slots <= 0) {
            $booking->property->update(['available' => false]);
        }

        return redirect()->back()->with('success', 'Booking approved successfully.');
    }

    /**
     * Landlord: Reject booking
     */
    public function reject(Booking $booking)
    {
        $user = Auth::user();
        
        // Check if user is the landlord
        if ($booking->property->landlord->user_id !== $user->id && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== 'Pending') {
            return redirect()->back()->with('error', 'Can only reject pending bookings.');
        }

        $booking->update(['status' => 'Cancelled']);

        return redirect()->back()->with('success', 'Booking rejected.');
    }

    /**
     * Landlord: Mark booking as active (tenant moved in)
     */
    public function activate(Booking $booking)
    {
        $user = Auth::user();
        
        if ($booking->property->landlord->user_id !== $user->id && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== 'Approved') {
            return redirect()->back()->with('error', 'Can only activate approved bookings.');
        }

        $booking->update(['status' => 'Active']);

        return redirect()->back()->with('success', 'Booking is now active. Tenant has moved in.');
    }

    /**
     * Landlord: Complete booking (tenant moved out)
     */
    public function complete(Booking $booking)
    {
        $user = Auth::user();
        
        if ($booking->property->landlord->user_id !== $user->id && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== 'Active') {
            return redirect()->back()->with('error', 'Can only complete active bookings.');
        }

        $booking->update(['status' => 'Completed']);
        
        // Restore available slot
        $booking->property->increment('available_slots');
        
        // Update property availability
        if ($booking->property->available_slots > 0) {
            $booking->property->update(['available' => true]);
        }

        return redirect()->back()->with('success', 'Booking completed. Slot is now available.');
    }
}