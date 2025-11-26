<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Rating;
use App\Models\PropertyView;
use App\Models\Amenity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class PropertyController extends Controller
{
    /**
     * Show all properties owned by the landlord
     */
    public function index()
    {
        $landlord = Auth::user()->landlord;
        $properties = $landlord ? $landlord->properties()->latest()->get() : collect();

        return view('content.landlord.properties.index', compact('properties'));
    }

    /**
     * Landlord preview their own property (no booking)
     */
    public function landlordShow(Property $property)
{
    // Check if landlord owns this property
    if (!$this->isOwner($property)) {
        abort(403, 'This action is unauthorized.');
    }

    // Load relationships
    $property->load(['landlord.user', 'propertyAmenities.amenity', 'ratings.user', 'bookings']);

    // Get ratings statistics
    $ratings = $property->ratings;
    $averageRating = $ratings->avg('rating');
    $totalReviews = $ratings->count();

    // Get bookings statistics
    $totalBookings = $property->bookings()->count();
    $activeBookings = $property->bookings()->where('status', 'Active')->count();
    $pendingBookings = $property->bookings()->where('status', 'Pending')->count();

    // Get property views count
    $totalViews = $property->views()->count();
    $uniqueViewers = $property->views()->distinct('user_id')->count('user_id');
    
    // Get recent views (last 30 days)
    $recentViews = $property->views()
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->count();

    // Get recent bookings
    $recentBookings = $property->bookings()
        ->with('user')
        ->latest()
        ->take(5)
        ->get();

    return view('content.landlord.properties.view', compact(
        'property',
        'ratings',
        'averageRating',
        'totalReviews',
        'totalBookings',
        'activeBookings',
        'pendingBookings',
        'recentBookings',
        'totalViews',
        'uniqueViewers',
        'recentViews'
    ));
}
    /**
     * Show the form for creating a new property
     */
    public function create()
    {
        $landlord = Auth::user()->landlord;
        
        if (!$landlord) {
            return redirect()->route('dashboard.landlord')->with('error', 'You do not have a landlord profile.');
        }

        $amenities = Amenity::all();

        return view('content.landlord.properties.create', compact('amenities'));
    }

    /**
     * Store new property
     */
    public function store(Request $request)
    {
        // Log incoming request data for debugging
        Log::info('Property Store Request', [
            'user_id' => Auth::id(),
            'request_data' => $request->except(['image'])
        ]);

        try {
            // Validate the request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'address' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'rooms' => 'required|integer|min:1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'distance_from_campus' => 'nullable|numeric|min:0',
                'capacity' => 'nullable|integer|min:1',
                'available_slots' => 'nullable|integer|min:0',
                'house_rules' => 'nullable|string',
                'owner_name' => 'nullable|string|max:100',
                'owner_contact' => 'nullable|string|max:15',
                'amenities' => 'nullable|array',
                'amenities.*' => 'integer|exists:amenities,amenity_id',
            ]);

            Log::info('Validation passed');

            // Check landlord profile
            $landlord = Auth::user()->landlord;
            if (!$landlord) {
                Log::error('Landlord profile not found', ['user_id' => Auth::id()]);
                return back()->with('error', 'You do not have a landlord profile.')->withInput();
            }

            Log::info('Landlord found', ['landlord_id' => $landlord->id]);

            // Use database transaction for data integrity
            DB::beginTransaction();

            try {
                // Prepare data for property creation
                $data = [
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'address' => $validated['address'],
                    'price' => $validated['price'],
                    'rooms' => $validated['rooms'],
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'distance_from_campus' => $validated['distance_from_campus'] ?? null,
                    'capacity' => $validated['capacity'] ?? $validated['rooms'],
                    'available_slots' => $validated['available_slots'] ?? ($validated['capacity'] ?? $validated['rooms']),
                    'house_rules' => $validated['house_rules'] ?? null,
                    'owner_name' => $validated['owner_name'] ?? Auth::user()->name,
                    'owner_contact' => $validated['owner_contact'] ?? null,
                    'available' => $request->has('available') ? 1 : 0,
                    'is_active' => 1,
                ];

                Log::info('Property data prepared', $data);

                // Handle image upload
                if ($request->hasFile('image')) {
                    Log::info('Image file detected');
                    
                    $image = $request->file('image');
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('assets/img/boarding');
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    
                    // Move the image
                    $image->move($destinationPath, $imageName);
                    $data['image'] = $imageName;
                    
                    Log::info('Image uploaded', ['filename' => $imageName]);
                }

                // Create the property
                $property = $landlord->properties()->create($data);
                Log::info('Property created', ['property_id' => $property->id]);

                // Save amenities if provided
                if ($request->filled('amenities') && is_array($request->amenities)) {
                    Log::info('Processing amenities', ['amenities' => $request->amenities]);
                    
                    foreach ($request->amenities as $amenityId) {
                        // Verify amenity exists
                        $amenityExists = Amenity::where('amenity_id', $amenityId)->exists();
                        
                        if ($amenityExists) {
                            $propertyAmenity = $property->propertyAmenities()->create([
                                'property_id' => $property->id,
                                'amenity_id' => $amenityId
                            ]);
                            Log::info('Amenity added', [
                                'property_amenity_id' => $propertyAmenity->property_amenity_id,
                                'amenity_id' => $amenityId
                            ]);
                        } else {
                            Log::warning('Amenity not found', ['amenity_id' => $amenityId]);
                        }
                    }
                }

                // Commit the transaction
                DB::commit();
                Log::info('Transaction committed successfully');

                return redirect()->route('landlord.properties.index')
                    ->with('success', 'Property added successfully.');

            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollBack();
                Log::error('Transaction rolled back', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Property creation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to create property: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $property = Property::with(['landlord.user', 'propertyAmenities.amenity', 'ratings', 'bookings'])->findOrFail($id);

        // Track property view if user is logged in
        if (Auth::check()) {
            PropertyView::firstOrCreate([
                'user_id' => Auth::id(),
                'property_id' => $property->id,
            ]);
        }

        // Get ratings
        $ratings = Rating::where('property_id', $id)->with('user')->latest()->get();
        $averageRating = $ratings->avg('rating');
        $totalReviews = $ratings->count();

        $userRating = Auth::check()
            ? Rating::where('user_id', Auth::id())->where('property_id', $id)->first()
            : null;

        // Get similar properties
        $similarProperties = Property::where('id', '!=', $id)
            ->where('is_active', 1)
            ->whereBetween('price', [$property->price * 0.8, $property->price * 1.2])
            ->limit(3)
            ->get();

        return view('content.properties.show', compact(
            'property',
            'ratings',
            'averageRating',
            'totalReviews',
            'userRating',
            'similarProperties'
        ));
    }

    /**
     * Edit property form
     */
    public function edit(Property $property)
    {
        if (!$this->isOwner($property)) {
            abort(403, 'This action is unauthorized.');
        }

        $amenities = Amenity::all();
        $propertyAmenities = $property->propertyAmenities()->pluck('amenity_id')->toArray();

        return view('content.landlord.properties.edit', compact('property', 'amenities', 'propertyAmenities'));
    }

    /**
     * Update property
     */
    public function update(Request $request, Property $property)
    {
        if (!$this->isOwner($property)) {
            abort(403, 'This action is unauthorized.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'address' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'rooms' => 'required|integer|min:1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'distance_from_campus' => 'nullable|numeric|min:0',
                'capacity' => 'nullable|integer|min:1',
                'available_slots' => 'nullable|integer|min:0',
                'house_rules' => 'nullable|string',
                'owner_name' => 'nullable|string|max:100',
                'owner_contact' => 'nullable|string|max:15',
                'amenities' => 'nullable|array',
                'amenities.*' => 'integer|exists:amenities,amenity_id',
            ]);

            DB::beginTransaction();

            try {
                $data = [
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'address' => $validated['address'],
                    'price' => $validated['price'],
                    'rooms' => $validated['rooms'],
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'distance_from_campus' => $validated['distance_from_campus'] ?? null,
                    'capacity' => $validated['capacity'] ?? null,
                    'available_slots' => $validated['available_slots'] ?? null,
                    'house_rules' => $validated['house_rules'] ?? null,
                    'owner_name' => $validated['owner_name'] ?? null,
                    'owner_contact' => $validated['owner_contact'] ?? null,
                    'available' => $request->has('available') ? 1 : 0,
                ];

                // Handle image upload
                if ($request->hasFile('image')) {
                    // Delete old image if exists
                    if ($property->image) {
                        $oldImagePath = public_path('assets/img/boarding/' . $property->image);
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    $image = $request->file('image');
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('assets/img/boarding');
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    
                    // Move the image
                    $image->move($destinationPath, $imageName);
                    $data['image'] = $imageName;
                }

                $property->update($data);

                // Update amenities
                $property->propertyAmenities()->delete();
                if ($request->filled('amenities') && is_array($request->amenities)) {
                    foreach ($request->amenities as $amenityId) {
                        $property->propertyAmenities()->create([
                            'property_id' => $property->id,
                            'amenity_id' => $amenityId
                        ]);
                    }
                }

                DB::commit();

                return redirect()->route('landlord.properties.index')
                    ->with('success', 'Property updated successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Property update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update property: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete property
     */
    public function destroy(Property $property)
    {
        if (!$this->isOwner($property)) {
            abort(403, 'This action is unauthorized.');
        }

        try {
            DB::beginTransaction();

            // Delete image from public folder
            if ($property->image) {
                $imagePath = public_path('assets/img/boarding/' . $property->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Delete related data
            $property->propertyAmenities()->delete();
            $property->views()->delete();
            $property->ratings()->delete();

            $property->delete();

            DB::commit();

            return redirect()->route('landlord.properties.index')
                ->with('success', 'Property deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Check if current user is the owner of the property
     */
    private function isOwner(Property $property)
    {
        $user = Auth::user();
        return $user && $property->landlord && $property->landlord->user_id === $user->id;
    }
}