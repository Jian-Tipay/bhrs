<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Landlord;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Rating;
use Carbon\Carbon;

class LandlordController extends Controller
{
    /**
     * Setup landlord profile
     */
    public function setup(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        $user->landlord()->create([
            'phone' => $request->phone,
            'company_name' => $request->company_name,
        ]);

        return redirect()->route('dashboard.landlord')->with('success', 'Landlord profile created!');
    }

    /**
     * List all bookings for landlord's properties
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Get all property IDs belonging to the landlord
        $propertyIds = $landlord->properties()->pluck('id');

        // Build query for bookings
        $query = Booking::with(['user', 'property'])
            ->whereIn('property_id', $propertyIds);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by property if provided
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Get bookings with pagination
        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get properties for filter dropdown
        $properties = $landlord->properties;

        return view('content.landlord.bookings.index', compact('bookings', 'properties'));
    }

    /**
     * View booking details
     */
    public function viewBooking($bookingId)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Get booking with related data and verify it belongs to landlord's property
        $booking = Booking::with(['user', 'property.propertyAmenities.amenity'])
            ->whereHas('property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
            ->findOrFail($bookingId);

        return view('content.landlord.bookings.booking-details', compact('booking'));
    }

    /**
     * Approve a booking
     */
    public function approveBooking($bookingId)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Find booking and verify it belongs to landlord's property
        $booking = Booking::whereHas('property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->findOrFail($bookingId);

        // Check if booking is in pending status
        if ($booking->status !== 'Pending') {
            return back()->with('error', 'Only pending bookings can be approved.');
        }

        // Update booking status
        $booking->status = 'Approved';
        $booking->save();

        // Decrease available slots for the property
        $property = $booking->property;
        if ($property->available_slots > 0) {
            $property->available_slots -= 1;
            $property->save();
        }

        // Check if property is fully booked
        if ($property->available_slots <= 0) {
            $property->available = false;
            $property->save();
        }

        return back()->with('success', 'Booking approved successfully!');
    }

    /**
     * Reject a booking
     */
    public function rejectBooking($bookingId)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Find booking and verify it belongs to landlord's property
        $booking = Booking::whereHas('property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->findOrFail($bookingId);

        // Check if booking is in pending status
        if ($booking->status !== 'Pending') {
            return back()->with('error', 'Only pending bookings can be rejected.');
        }

        // Update booking status
        $booking->status = 'Cancelled';
        $booking->save();

        return back()->with('success', 'Booking rejected successfully!');
    }

    /**
     * Cancel an active booking
     */
    public function cancelBooking($bookingId)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Find booking and verify it belongs to landlord's property
        $booking = Booking::whereHas('property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->findOrFail($bookingId);

        // Check if booking can be cancelled
        if (!in_array($booking->status, ['Approved', 'Active'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        // Update booking status
        $booking->status = 'Cancelled';
        $booking->save();

        // Increase available slots for the property
        $property = $booking->property;
        $property->available_slots += 1;
        
        // Make property available again
        if (!$property->available) {
            $property->available = true;
        }
        
        $property->save();

        return redirect()->route('landlord.tenants.index')->with('success', 'Booking cancelled successfully! Property slot has been freed up.');
    }

    /**
     * Complete a booking
     */
    public function completeBooking($bookingId)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Find booking and verify it belongs to landlord's property
        $booking = Booking::whereHas('property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->findOrFail($bookingId);

        // Check if booking is active
        if ($booking->status !== 'Active') {
            return back()->with('error', 'Only active bookings can be completed.');
        }

        // Update booking status
        $booking->status = 'Completed';
        $booking->save();

        // Increase available slots for the property
        $property = $booking->property;
        $property->available_slots += 1;
        
        // Make property available again
        if (!$property->available) {
            $property->available = true;
        }
        
        $property->save();

        return redirect()->route('landlord.tenants.index')->with('success', 'Lease completed successfully! Property slot has been freed up.');
    }

    /**
     * Display tenants page
     */
    public function tenants(Request $request)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Get all property IDs belonging to the landlord
        $propertyIds = $landlord->properties()->pluck('id');

        // Build query for active tenants (Approved or Active bookings)
        $query = Booking::with(['user', 'property'])
            ->whereIn('property_id', $propertyIds)
            ->whereIn('status', ['Approved', 'Active']);

        // Filter by status if provided
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 'Active');
            } elseif ($request->status === 'ending_soon') {
                // Leases ending in the next 30 days
                $query->where('status', 'Active')
                      ->whereNotNull('move_out_date')
                      ->whereBetween('move_out_date', [Carbon::now(), Carbon::now()->addDays(30)]);
            } elseif ($request->status === 'expired') {
                $query->where('status', 'Completed');
            }
        }

        // Filter by property if provided
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Search by tenant name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Get tenants with pagination
        $tenants = $query->orderBy('move_in_date', 'desc')->paginate(15);

        // Get properties for filter dropdown
        $properties = $landlord->properties;

        // Calculate stats
        $totalTenants = Booking::whereIn('property_id', $propertyIds)
            ->whereIn('status', ['Approved', 'Active'])
            ->count();

        $activeLeases = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'Active')
            ->count();

        $moveInsThisMonth = Booking::whereIn('property_id', $propertyIds)
            ->whereIn('status', ['Approved', 'Active'])
            ->whereMonth('move_in_date', Carbon::now()->month)
            ->whereYear('move_in_date', Carbon::now()->year)
            ->count();

        // Leases ending in the next 30 days
        $endingSoon = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'Active')
            ->whereNotNull('move_out_date')
            ->whereBetween('move_out_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();

        return view('content.landlord.tenants.index', compact(
            'tenants',
            'properties',
            'totalTenants',
            'activeLeases',
            'moveInsThisMonth',
            'endingSoon'
        ));
    }

    /**
     * View tenant details
     */
    public function viewTenant($bookingId)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Get booking with related data
        $tenant = Booking::with(['user', 'property.propertyAmenities.amenity'])
            ->whereHas('property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
            ->findOrFail($bookingId);

        return view('content.landlord.tenants.tenant-details', compact('tenant'));
    }

    /**
     * Display reviews page
     */
    public function reviews(Request $request)
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Get all property IDs belonging to the landlord
        $propertyIds = $landlord->properties()->pluck('id');

        // Build query for reviews
        $query = Rating::with(['user', 'property'])
            ->whereIn('property_id', $propertyIds);

        // Filter by property if provided
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by rating if provided
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort by specified order
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Get reviews with pagination
        $reviews = $query->paginate(15);

        // Get properties for filter dropdown
        $properties = $landlord->properties;

        // Calculate rating statistics
        $allRatings = Rating::whereIn('property_id', $propertyIds)->get();
        $averageRating = $allRatings->avg('rating') ?? 0;
        $totalReviews = $allRatings->count();

        // Rating distribution
        $ratingDistribution = [
            5 => $allRatings->where('rating', 5)->count(),
            4 => $allRatings->where('rating', 4)->count(),
            3 => $allRatings->where('rating', 3)->count(),
            2 => $allRatings->where('rating', 2)->count(),
            1 => $allRatings->where('rating', 1)->count(),
        ];

        return view('content.landlord.ratings.index', compact(
            'reviews',
            'properties',
            'averageRating',
            'totalReviews',
            'ratingDistribution'
        ));
    }

    /**
     * Reply to a review
     */
    public function replyReview(Request $request, $reviewId)
    {
        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return back()->with('error', 'Landlord profile not found.');
        }

        // Find review and verify it belongs to landlord's property
        $review = Rating::whereHas('property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->findOrFail($reviewId);

        // Add reply to review (assuming you have a reply column)
        $review->landlord_reply = $request->reply;
        $review->replied_at = Carbon::now();
        $review->save();

        return back()->with('success', 'Reply posted successfully!');
    }

    /**
     * Display reports page
     */
    /**
 * Display reports page
 */
public function reports(Request $request)
{
    $user = Auth::user();
    $landlord = $user->landlord;

    if (!$landlord) {
        return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
    }

    // Get period (default 30 days)
    $period = $request->get('period', 30);
    $startDate = Carbon::now()->subDays($period);

    // Get all property IDs
    $propertyIds = $landlord->properties()->pluck('id');

    // Calculate revenue - based on monthly price * number of active/completed bookings
    $bookingsForRevenue = Booking::whereIn('property_id', $propertyIds)
        ->whereIn('status', ['Active', 'Completed'])
        ->where('created_at', '>=', $startDate)
        ->with('property')
        ->get();

    $totalRevenue = $bookingsForRevenue->sum(function($booking) {
        return $booking->property->price;
    });

    $totalBookings = Booking::whereIn('property_id', $propertyIds)
        ->where('created_at', '>=', $startDate)
        ->count();

    // Occupancy rate
    $totalCapacity = $landlord->properties()->sum('capacity');
    $availableSlots = $landlord->properties()->sum('available_slots');
    $occupiedSlots = $totalCapacity - $availableSlots;
    $occupancyRate = $totalCapacity > 0 ? ($occupiedSlots / $totalCapacity) * 100 : 0;

    // Average rating
    $averageRating = Rating::whereIn('property_id', $propertyIds)->avg('rating') ?? 0;
    $totalReviews = Rating::whereIn('property_id', $propertyIds)->count();

    // Property performance
    $propertyPerformance = Property::whereIn('id', $propertyIds)
        ->withCount('bookings')
        ->withAvg('ratings', 'rating')
        ->get();

    // Booking status counts
    $activeBookings = Booking::whereIn('property_id', $propertyIds)
        ->where('status', 'Active')
        ->count();

    $pendingBookings = Booking::whereIn('property_id', $propertyIds)
        ->where('status', 'Pending')
        ->count();

    $approvedBookings = Booking::whereIn('property_id', $propertyIds)
        ->where('status', 'Approved')
        ->count();

    $completedBookings = Booking::whereIn('property_id', $propertyIds)
        ->where('status', 'Completed')
        ->count();

    return view('content.landlord.reports.index', compact(
        'totalRevenue',
        'occupancyRate',
        'totalBookings',
        'averageRating',
        'totalReviews',
        'propertyPerformance',
        'period',
        'totalCapacity',
        'occupiedSlots',
        'activeBookings',
        'pendingBookings',
        'approvedBookings',
        'completedBookings'
    ));
}

    /**
     * Export reports as PDF
     */
    public function exportPDF(Request $request)
    {
        // Implementation for PDF export
        return back()->with('info', 'PDF export feature coming soon!');
    }

    /**
     * Export reports as Excel
     */
    public function exportExcel(Request $request)
    {
        // Implementation for Excel export
        return back()->with('info', 'Excel export feature coming soon!');
    }

    /**
     * Display profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'Landlord profile not found.');
        }

        // Get statistics
        $totalProperties = $landlord->properties()->count();
        $totalBookings = Booking::whereIn('property_id', $landlord->properties()->pluck('id'))->count();
        $averageRating = Rating::whereIn('property_id', $landlord->properties()->pluck('id'))->avg('rating') ?? 0;

        return view('content.landlord.profile.index', compact(
            'user',
            'landlord',
            'totalProperties',
            'totalBookings',
            'averageRating'
        ));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:15',
        ]);

        $user = Auth::user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;
        $user->save();

        // Update landlord profile
        if ($user->landlord) {
            $user->landlord->update([
                'phone' => $request->phone,
            ]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Update business information
     */
    public function updateBusiness(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        if ($user->landlord) {
            $user->landlord->update([
                'company_name' => $request->company_name,
            ]);
        }

        return back()->with('success', 'Business information updated successfully!');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        // Store notification preferences in user meta or separate table
        // For now, just return success
        
        return back()->with('success', 'Notification preferences updated successfully!');
    }
}