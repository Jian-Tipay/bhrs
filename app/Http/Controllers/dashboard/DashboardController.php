<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\CollaborativeFilteringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Landlord;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\StudentPreference;
use App\Models\Booking;
use App\Models\Rating;

class DashboardController extends Controller
{
    protected $cfService;

    public function __construct(CollaborativeFilteringService $cfService)
    {
        $this->cfService = $cfService;
    }

    public function landlord()
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        if (!$landlord) {
            // If no landlord record exists, show modal to create one
            $amenities = Amenity::all();
            return view('content.landlord.landlord_dashboard', compact(
                'user',
                'amenities'
            ))->with('showLandlordModal', true);
        }

        // Get landlord's properties
        $myProperties = $landlord->properties()
            ->with(['images', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->get();

        // Calculate statistics
        $totalProperties = $myProperties->count();
        $activeProperties = $myProperties->where('is_active', 1)->count();
        
        // Get all bookings for landlord's properties
        $propertyIds = $myProperties->pluck('id')->toArray();
        
        $bookings = Booking::whereIn('property_id', $propertyIds)
            ->with(['user', 'property.images'])
            ->get();
        
        $totalBookings = $bookings->count();
        $pendingBookings = $bookings->where('status', 'Pending')->count();
        
        // Calculate monthly revenue (current month)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $monthlyRevenue = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'Active')
            ->whereMonth('move_in_date', $currentMonth)
            ->whereYear('move_in_date', $currentYear)
            ->join('properties', 'bookings.property_id', '=', 'properties.id')
            ->sum('properties.price');
        
        // Calculate revenue growth (compare to last month)
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;
        
        $lastMonthRevenue = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'Active')
            ->whereMonth('move_in_date', $lastMonth)
            ->whereYear('move_in_date', $lastMonthYear)
            ->join('properties', 'bookings.property_id', '=', 'properties.id')
            ->sum('properties.price');
        
        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;
        
        // Calculate average rating
        $averageRating = Rating::whereIn('property_id', $propertyIds)->avg('rating') ?? 0;
        $totalReviews = Rating::whereIn('property_id', $propertyIds)->count();
        
        // Get recent bookings (last 10)
        $recentBookings = Booking::whereIn('property_id', $propertyIds)
            ->with(['user', 'property.images'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get top performing properties (by revenue this month)
        $topProperties = Property::whereIn('id', $propertyIds)
            ->with(['images', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->withCount('bookings')
            ->get()
            ->map(function ($property) use ($currentMonth, $currentYear) {
                $property->monthly_revenue = Booking::where('property_id', $property->id)
                    ->where('status', 'Active')
                    ->whereMonth('move_in_date', $currentMonth)
                    ->whereYear('move_in_date', $currentYear)
                    ->count() * $property->price;
                return $property;
            })
            ->sortByDesc('monthly_revenue')
            ->take(3);
        
        // Prepare revenue chart data (last 6 months)
        $revenueChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');
            $revenue = Booking::whereIn('property_id', $propertyIds)
                ->where('status', 'Active')
                ->whereMonth('move_in_date', $date->month)
                ->whereYear('move_in_date', $date->year)
                ->join('properties', 'bookings.property_id', '=', 'properties.id')
                ->sum('properties.price');
            
            $revenueChartData[$month] = $revenue;
        }
        
        // Prepare booking chart data (by status)
        $bookingChartData = [
            'Pending' => $bookings->where('status', 'Pending')->count(),
            'Approved' => $bookings->where('status', 'Approved')->count(),
            'Active' => $bookings->where('status', 'Active')->count(),
            'Completed' => $bookings->where('status', 'Completed')->count(),
        ];
        
        $amenities = Amenity::all();

        return view('content.landlord.landlord_dashboard', compact(
            'user',
            'landlord',
            'myProperties',
            'totalProperties',
            'activeProperties',
            'totalBookings',
            'pendingBookings',
            'monthlyRevenue',
            'revenueGrowth',
            'averageRating',
            'totalReviews',
            'recentBookings',
            'topProperties',
            'revenueChartData',
            'bookingChartData',
            'amenities'
        ));
    }

    public function user(Request $request)
    {
        $user = Auth::user();
        
      

        $userId = $user->id;

        // Get user preference
        $preference = StudentPreference::where('user_id', $userId)->first();
        $amenities = Amenity::all();
        
        // Get user stats
        $bookingsCount = Booking::where('user_id', $userId)->count();
        $ratingsCount = Rating::where('user_id', $userId)->count();
        $favoritesCount = 0; // TODO: Implement favorites

        // Check if Python CF service is available
        $isPythonServiceAvailable = $this->cfService->isPythonServiceAvailable();
        
        // Initialize recommendations
        $contentBasedRecs = [];
        $cfRecommendations = [];
        $allProperties = [];
        $cfServiceStatus = [
            'available' => $isPythonServiceAvailable,
            'message' => $isPythonServiceAvailable 
                ? 'Using AI-powered collaborative filtering' 
                : 'Using content-based filtering (Python service offline)'
        ];

        // 1. Get Content-Based Recommendations (Always Available - PHP Service)
        if ($preference) {
            try {
                // Get content-based recommendations
                $contentQuery = $this->getContentBasedQuery($userId, $preference);
                
                // Apply search filters to content-based recommendations
                if ($request->filled('query')) {
                    $searchTerm = $request->input('query');
                    $contentQuery->where(function($q) use ($searchTerm) {
                        $q->where('title', 'like', "%{$searchTerm}%")
                          ->orWhere('address', 'like', "%{$searchTerm}%");
                    });
                }

                // Apply price filter
                if ($request->filled('price_range')) {
                    $priceRange = $request->input('price_range');
                    switch ($priceRange) {
                        case '0-2000':
                            $contentQuery->whereBetween('price', [0, 2000]);
                            break;
                        case '2001-4000':
                            $contentQuery->whereBetween('price', [2001, 4000]);
                            break;
                        case '4001-6000':
                            $contentQuery->whereBetween('price', [4001, 6000]);
                            break;
                        case '6001+':
                            $contentQuery->where('price', '>', 6001);
                            break;
                    }
                }

                // Apply distance filter
                if ($request->filled('distance')) {
                    $distance = $request->input('distance');
                    switch ($distance) {
                        case '0-500':
                            $contentQuery->where('distance_from_campus', '<=', 0.5);
                            break;
                        case '501-1000':
                            $contentQuery->whereBetween('distance_from_campus', [0.51, 1.0]);
                            break;
                        case '1001-2000':
                            $contentQuery->whereBetween('distance_from_campus', [1.01, 2.0]);
                            break;
                        case '2001+':
                            $contentQuery->where('distance_from_campus', '>', 2.0);
                            break;
                    }
                }

                $contentBasedRecs = $contentQuery->limit(6)->get();
                
                // Add ratings and images to content-based recommendations
                foreach ($contentBasedRecs as $property) {
                    $property->load('images');
                    $property->ratings_avg_rating = Rating::where('property_id', $property->id)->avg('rating');
                    $property->ratings_count = Rating::where('property_id', $property->id)->count();
                }
            } catch (\Exception $e) {
                Log::error('Content-Based Recommendations Error: ' . $e->getMessage());
            }
        }

        // 2. Get Enhanced Collaborative Filtering Recommendations
        if ($isPythonServiceAvailable && $ratingsCount >= 2) {
            try {
                // Use the ENHANCED CF service (Hybrid: User-Based + Item-Based + Content)
                $cfProperties = $this->cfService->getRecommendations($userId, 6);

                // Format CF recommendations with enhanced data
                foreach ($cfProperties as $property) {
                    // Get first image from images relationship
                    $firstImage = null;
                    if ($property->images && $property->images->count() > 0) {
                        $firstImage = $property->images->first()->image_path;
                    }

                    $cfRecommendations[] = [
                        'property_id' => $property->id,
                        'title' => $property->title,
                        'address' => $property->address,
                        'price' => $property->price,
                        'latitude' => $property->latitude,
                        'longitude' => $property->longitude,
                        'distance_from_campus' => $property->distance_from_campus,
                        'image' => $firstImage ? asset('storage/' . $firstImage) : null,
                        'ratings_avg' => Rating::where('property_id', $property->id)->avg('rating') ?? 0,
                        'ratings_count' => Rating::where('property_id', $property->id)->count(),
                        
                        // Enhanced CF data
                        'cf_predicted_rating' => $property->cf_predicted_rating ?? null,
                        'cf_confidence' => $property->cf_confidence ?? null,
                        'cf_user_based_score' => $property->cf_user_based_score ?? null,
                        'cf_item_based_score' => $property->cf_item_based_score ?? null,
                        'cf_content_score' => $property->cf_content_score ?? null,
                        'cf_explanation' => $property->cf_explanation ?? null,
                    ];
                }

                Log::info("Enhanced CF Recommendations loaded for user {$userId}: " . count($cfRecommendations) . " properties");

            } catch (\Exception $e) {
                Log::error('CF Recommendations Error: ' . $e->getMessage());
                $cfServiceStatus['message'] = 'CF service temporarily unavailable';
            }
        } elseif (!$isPythonServiceAvailable) {
            // Python service is down - log it
            Log::info("Python CF service not available for user {$userId}. Using content-based only.");
        } elseif ($ratingsCount < 2) {
            // Not enough ratings for CF - show cold start
            Log::info("User {$userId} has insufficient ratings ({$ratingsCount}) for CF. Need at least 2.");
            
            try {
                $coldStartProps = $this->cfService->getColdStartRecommendations($userId, 6);
                
                foreach ($coldStartProps as $property) {
                    // Get first image from images relationship
                    $firstImage = null;
                    if ($property->images && $property->images->count() > 0) {
                        $firstImage = $property->images->first()->image_path;
                    }

                    $cfRecommendations[] = [
                        'property_id' => $property->id,
                        'title' => $property->title,
                        'address' => $property->address,
                        'price' => $property->price,
                        'latitude' => $property->latitude,
                        'longitude' => $property->longitude,
                        'distance_from_campus' => $property->distance_from_campus,
                        'image' => $firstImage ? asset('storage/' . $firstImage) : null,
                        'ratings_avg' => Rating::where('property_id', $property->id)->avg('rating') ?? 0,
                        'ratings_count' => Rating::where('property_id', $property->id)->count(),
                    ];
                }
                
                $cfServiceStatus['message'] = 'Showing popular properties (rate more properties to get personalized recommendations)';
            } catch (\Exception $e) {
                Log::error('Cold Start Recommendations Error: ' . $e->getMessage());
            }
        }

        // 3. Get All Properties (with search filters)
        try {
            $query = Property::where('is_active', 1);

            // FIX: Apply search filter correctly
            if ($request->filled('query')) {
                $searchTerm = $request->input('query');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('address', 'like', "%{$searchTerm}%");
                });
            }

            // Apply price filter
            if ($request->filled('price_range')) {
                $priceRange = $request->input('price_range');
                switch ($priceRange) {
                    case '0-2000':
                        $query->whereBetween('price', [0, 2000]);
                        break;
                    case '2001-4000':
                        $query->whereBetween('price', [2001, 4000]);
                        break;
                    case '4001-6000':
                        $query->whereBetween('price', [4001, 6000]);
                        break;
                    case '6001+':
                        $query->where('price', '>', 6001);
                        break;
                }
            }

            // Apply distance filter
            if ($request->filled('distance')) {
                $distance = $request->input('distance');
                switch ($distance) {
                    case '0-500':
                        $query->where('distance_from_campus', '<=', 0.5);
                        break;
                    case '501-1000':
                        $query->whereBetween('distance_from_campus', [0.51, 1.0]);
                        break;
                    case '1001-2000':
                        $query->whereBetween('distance_from_campus', [1.01, 2.0]);
                        break;
                    case '2001+':
                        $query->where('distance_from_campus', '>', 2.0);
                        break;
                }
            }

            // Get properties with ratings
            $properties = $query->with(['images'])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get();

            // Format all properties
            foreach ($properties as $property) {
                // Get first image from images relationship
                $firstImage = null;
                if ($property->images && $property->images->count() > 0) {
                    $firstImage = $property->images->first()->image_path;
                }
                
                $allProperties[] = [
                    'id' => $property->id,
                    'title' => $property->title,
                    'address' => $property->address,
                    'price' => $property->price,
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude,
                    'distance_from_campus' => $property->distance_from_campus,
                    'image' => $firstImage ? asset('storage/' . $firstImage) : null,
                    'ratings_avg' => $property->ratings_avg_rating ?? 0,
                    'ratings_count' => $property->ratings_count ?? 0,
                ];
            }
        } catch (\Exception $e) {
            Log::error('All Properties Error: ' . $e->getMessage());
        }

        return view('content.dashboard.dashboards-analytics', compact(
            'user',
            'preference',
            'amenities',
            'bookingsCount',
            'ratingsCount',
            'favoritesCount',
            'contentBasedRecs',
            'cfRecommendations',
            'allProperties',
            'cfServiceStatus'
        ));
    }

    /**
     * Refresh recommendations (clear cache and reload)
     */
    public function refreshRecommendations()
    {
        $userId = Auth::id();
        
        try {
            // Clear user cache
            $this->cfService->clearUserCache($userId);
            
            // Trigger Python service to retrain if available
            if ($this->cfService->isPythonServiceAvailable()) {
                $this->cfService->retrainModel();
            }
            
            return redirect()->route('dashboard.user')->with('success', 'Recommendations refreshed successfully!');
        } catch (\Exception $e) {
            Log::error('Refresh Recommendations Error: ' . $e->getMessage());
            return redirect()->route('dashboard.user')->with('error', 'Failed to refresh recommendations.');
        }
    }

    /**
     * Track property click for analytics
     */
    public function trackClick(Request $request, $propertyId)
    {
        try {
            $userId = Auth::id();
            
            // Log the click
            Log::info("User {$userId} clicked property {$propertyId}");
            
            // TODO: Store in database for analytics
            // PropertyClick::create([
            //     'user_id' => $userId,
            //     'property_id' => $propertyId,
            //     'clicked_at' => now()
            // ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Track Click Error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get content-based query with scoring
     * Helper method to apply content-based filtering logic
     */
    private function getContentBasedQuery($userId, $preference)
    {
        $ratedPropertyIds = Rating::where('user_id', $userId)
            ->pluck('property_id')
            ->toArray();
        
        $query = Property::select('properties.*')
            ->selectRaw('
                (CASE 
                    WHEN price BETWEEN ? AND ? THEN 30
                    WHEN price < ? THEN 15
                    ELSE 0
                END +
                CASE 
                    WHEN distance_from_campus <= ? THEN 25
                    WHEN distance_from_campus <= ? * 1.5 THEN 15
                    ELSE 0
                END +
                (SELECT COUNT(*) * 5
                    FROM property_amenities pa
                    JOIN preferred_amenities prf ON pa.amenity_id = prf.amenity_id
                    WHERE pa.property_id = properties.id 
                    AND prf.preference_id = ?
                ) +
                COALESCE((SELECT AVG(rating) * 10 FROM ratings WHERE property_id = properties.id), 0)
                ) as match_score
            ', [
                $preference->budget_min,
                $preference->budget_max,
                $preference->budget_min,
                $preference->preferred_distance,
                $preference->preferred_distance,
                $preference->preference_id
            ])
            ->where('is_active', 1)
            ->whereNotIn('id', $ratedPropertyIds);
        
        // Apply gender preference filter
        if ($preference->gender_preference !== 'Any') {
            $query->where('gender_restriction', $preference->gender_preference);
        }
        
        // Apply room type preference
        if ($preference->room_type !== 'Any') {
            $query->where('room_type', $preference->room_type);
        }
        
        return $query->orderByDesc('match_score');
    }
}