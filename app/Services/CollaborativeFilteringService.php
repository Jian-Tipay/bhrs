<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Rating;
use App\Models\Property;
use App\Models\User;
use App\Models\StudentPreference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CollaborativeFilteringService
{
    protected $pythonServiceUrl;
    protected $pythonServiceTimeout;

    public function __construct()
    {
        $this->pythonServiceUrl = env('PYTHON_CF_URL', 'https://bhrs-slsu.onrender.com/');
        $this->pythonServiceTimeout = env('PYTHON_CF_TIMEOUT', 5);
    }

    /**
     * Check if Python CF service is running
     */
    public function isPythonServiceAvailable()
    {
        return true;
    }

    /**
     * Get ENHANCED HYBRID recommendations
     * Combines: User-Based CF + Item-Based CF + Content Features
     * This is your MAIN recommendation method
     */
    public function getRecommendations($userId, $limit = 10)
    {
        // Check if Python service is available
        if (!$this->isPythonServiceAvailable()) {
            Log::info("Python CF service not available, falling back to content-based for user {$userId}");
            return $this->getContentBasedRecommendations($userId, $limit);
        }

        try {
            // Call Enhanced Hybrid CF endpoint
            $response = Http::timeout($this->pythonServiceTimeout)
                ->get("{$this->pythonServiceUrl}/recommendations", [
                    'user_id' => $userId,
                    'limit' => $limit
                ]);

            if ($response->failed()) {
                Log::warning("Python CF request failed for user {$userId}, falling back");
                return $this->getContentBasedRecommendations($userId, $limit);
            }

            $data = $response->json();
            $recommendations = collect($data['recommendations'] ?? []);
            
            if ($recommendations->isEmpty()) {
                Log::info("No CF recommendations for user {$userId}, using content-based");
                return $this->getContentBasedRecommendations($userId, $limit);
            }

            // Get property IDs
            $propertyIds = $recommendations->pluck('property_id')->toArray();
            
            // Fetch full property details with relationships
            $properties = Property::with(['amenities', 'ratings'])
                ->whereIn('id', $propertyIds)
                ->get();
            
            // Enrich properties with CF data
            $propertiesWithScores = $properties->map(function($property) use ($recommendations) {
                $cfData = $recommendations->firstWhere('property_id', $property->id);
                
                if ($cfData) {
                    // Main CF metrics
                    $property->cf_predicted_rating = $cfData['predicted_rating'];
                    $property->cf_confidence = $cfData['confidence'];
                    $property->cf_algorithm = $cfData['algorithm'];
                    
                    // Score breakdown (40% user-based, 30% item-based, 30% content)
                    if (isset($cfData['score_breakdown'])) {
                        $breakdown = $cfData['score_breakdown'];
                        $property->cf_user_based_score = $breakdown['user_based'];
                        $property->cf_item_based_score = $breakdown['item_based'];
                        $property->cf_content_score = $breakdown['content'];
                        
                        // Generate human-readable explanation
                        $property->cf_explanation = $this->generateExplanation($breakdown);
                    }
                }
                
                return $property;
            });

            // Sort by CF predicted rating (highest first)
            $sortedProperties = $propertiesWithScores->sortByDesc('cf_predicted_rating')->values();

            Log::info("Enhanced CF recommendations for user {$userId}: " . $sortedProperties->count() . " properties");

            return $sortedProperties;

        } catch (\Exception $e) {
            Log::error("Error calling Python CF service: " . $e->getMessage());
            return $this->getContentBasedRecommendations($userId, $limit);
        }
    }

    /**
     * Generate human-readable explanation from score breakdown
     */
    private function generateExplanation($breakdown)
    {
        $explanations = [];
        
        // User-based (40%) - similar students' preferences
        if ($breakdown['user_based'] > 0.1) {
            $explanations[] = "Students with similar preferences rated this highly";
        }
        
        // Item-based (30%) - similar to what you liked
        if ($breakdown['item_based'] > 0.1) {
            $explanations[] = "Similar to properties you've rated highly before";
        }
        
        // Content (30%) - matches your preferred amenities
        if ($breakdown['content'] > 0.1) {
            $explanations[] = "Has amenities from properties you liked";
        }
        
        return implode('. ', $explanations);
    }

    /**
     * Get detailed recommendation explanation for a specific property
     */
    public function getRecommendationExplanation($userId, $propertyId)
    {
        if (!$this->isPythonServiceAvailable()) {
            return null;
        }

        try {
            // Get recommendations including this property
            $response = Http::timeout($this->pythonServiceTimeout)
                ->get("{$this->pythonServiceUrl}/recommendations", [
                    'user_id' => $userId,
                    'limit' => 50
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $recommendations = collect($data['recommendations'] ?? []);
                
                $recommendation = $recommendations->firstWhere('property_id', $propertyId);
                
                if ($recommendation && isset($recommendation['score_breakdown'])) {
                    $breakdown = $recommendation['score_breakdown'];
                    
                    return [
                        'predicted_rating' => $recommendation['predicted_rating'],
                        'confidence' => $recommendation['confidence'],
                        'breakdown' => [
                            'user_based' => [
                                'score' => $breakdown['user_based'],
                                'weight' => '40%',
                                'explanation' => 'Based on students with similar preferences'
                            ],
                            'item_based' => [
                                'score' => $breakdown['item_based'],
                                'weight' => '30%',
                                'explanation' => 'Based on similarity to properties you liked'
                            ],
                            'content' => [
                                'score' => $breakdown['content'],
                                'weight' => '30%',
                                'explanation' => 'Based on matching your preferred amenities'
                            ]
                        ],
                        'explanation' => $this->generateExplanation($breakdown)
                    ];
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Error getting recommendation explanation: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get PURE User-Based CF recommendations
     * Only uses similar students' preferences
     */
    public function getUserBasedRecommendations($userId, $limit = 10)
    {
        if (!$this->isPythonServiceAvailable()) {
            return collect([]);
        }

        try {
            $response = Http::timeout($this->pythonServiceTimeout)
                ->get("{$this->pythonServiceUrl}/recommendations/collaborative", [
                    'user_id' => $userId,
                    'limit' => $limit
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $recommendations = collect($data['recommendations'] ?? []);
                
                if ($recommendations->isNotEmpty()) {
                    $propertyIds = $recommendations->pluck('property_id')->toArray();
                    $properties = Property::whereIn('id', $propertyIds)->get();
                    
                    return $properties->map(function($property) use ($recommendations) {
                        $cfData = $recommendations->firstWhere('property_id', $property->id);
                        if ($cfData) {
                            $property->cf_predicted_rating = $cfData['predicted_rating'];
                            $property->cf_confidence = $cfData['confidence'];
                            $property->cf_algorithm = 'user_based_cf';
                        }
                        return $property;
                    });
                }
            }

            return collect([]);
        } catch (\Exception $e) {
            Log::error("Error getting user-based recommendations: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get PURE Item-Based CF recommendations
     * Only uses property similarities
     */
    public function getItemBasedRecommendations($userId, $limit = 10)
    {
        if (!$this->isPythonServiceAvailable()) {
            return collect([]);
        }

        try {
            $response = Http::timeout($this->pythonServiceTimeout)
                ->get("{$this->pythonServiceUrl}/recommendations/item-based", [
                    'user_id' => $userId,
                    'limit' => $limit
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $recommendations = collect($data['recommendations'] ?? []);
                
                if ($recommendations->isNotEmpty()) {
                    $propertyIds = $recommendations->pluck('property_id')->toArray();
                    $properties = Property::whereIn('id', $propertyIds)->get();
                    
                    return $properties->map(function($property) use ($recommendations) {
                        $cfData = $recommendations->firstWhere('property_id', $property->id);
                        if ($cfData) {
                            $property->cf_predicted_rating = $cfData['predicted_rating'];
                            $property->cf_confidence = $cfData['confidence'];
                            $property->cf_algorithm = 'item_based_cf';
                        }
                        return $property;
                    });
                }
            }

            return collect([]);
        } catch (\Exception $e) {
            Log::error("Error getting item-based recommendations: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get similar users (students with similar preferences)
     */
    public function getSimilarUsers($userId, $limit = 10)
    {
        if (!$this->isPythonServiceAvailable()) {
            return collect([]);
        }

        try {
            $response = Http::timeout($this->pythonServiceTimeout)
                ->get("{$this->pythonServiceUrl}/similar-users/{$userId}", [
                    'k' => $limit
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $similarUsers = collect($data['similar_users'] ?? []);
                
                // Enrich with user details
                $userIds = $similarUsers->pluck('user_id')->toArray();
                $users = User::whereIn('id', $userIds)->get()->keyBy('id');
                
                return $similarUsers->map(function($similar) use ($users) {
                    $user = $users->get($similar['user_id']);
                    return [
                        'user_id' => $similar['user_id'],
                        'name' => $user ? $user->name : 'Unknown',
                        'similarity' => $similar['similarity'],
                        'common_ratings' => $similar['common_ratings']
                    ];
                });
            }

            return collect([]);
        } catch (\Exception $e) {
            Log::error("Error getting similar users: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get similar properties (based on user ratings)
     */
    public function getSimilarProperties($propertyId, $limit = 10)
    {
        if (!$this->isPythonServiceAvailable()) {
            return collect([]);
        }

        try {
            $response = Http::timeout($this->pythonServiceTimeout)
                ->get("{$this->pythonServiceUrl}/similar-properties/{$propertyId}", [
                    'k' => $limit
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $similarProps = collect($data['similar_properties'] ?? []);
                
                $propertyIds = $similarProps->pluck('property_id')->toArray();
                $properties = Property::whereIn('id', $propertyIds)->get()->keyBy('id');
                
                return $similarProps->map(function($similar) use ($properties) {
                    $property = $properties->get($similar['property_id']);
                    return [
                        'property_id' => $similar['property_id'],
                        'title' => $property ? $property->title : 'Unknown',
                        'similarity' => $similar['similarity'],
                        'common_users' => $similar['common_users'],
                        'property' => $property
                    ];
                });
            }

            return collect([]);
        } catch (\Exception $e) {
            Log::error("Error getting similar properties: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Predict rating for a user-property pair
     */
    public function predictRating($userId, $propertyId)
    {
        if (!$this->isPythonServiceAvailable()) {
            return null;
        }

        try {
            $response = Http::timeout($this->pythonServiceTimeout)
                ->post("{$this->pythonServiceUrl}/predict-rating", [
                    'user_id' => $userId,
                    'property_id' => $propertyId
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'predicted_rating' => $data['predicted_rating'],
                    'confidence' => $data['confidence']
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Error predicting rating: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Content-Based Filtering (fallback)
     */
    public function getContentBasedRecommendations($userId, $limit = 10)
    {
        $preference = StudentPreference::where('user_id', $userId)->first();
        
        if (!$preference) {
            return $this->getColdStartRecommendations($userId, $limit);
        }
        
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
                ) as content_score
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
        
        if ($preference->gender_preference !== 'Any') {
            $query->where('gender_restriction', $preference->gender_preference);
        }
        
        if ($preference->room_type !== 'Any') {
            $query->where('room_type', $preference->room_type);
        }
        
        return $query->orderByDesc('content_score')
            ->limit($limit)
            ->get();
    }

    /**
     * Cold start recommendations
     */
    public function getColdStartRecommendations($userId, $limit = 10)
    {
        $preference = StudentPreference::where('user_id', $userId)->first();
        
        $query = Property::select('properties.*')
            ->selectRaw('
                COALESCE(AVG(ratings.rating), 0) as avg_rating,
                COUNT(DISTINCT ratings.user_id) as rating_count,
                (
                    COALESCE(AVG(ratings.rating), 0) * 0.6 +
                    COUNT(DISTINCT ratings.user_id) * 0.4
                ) as popularity_score
            ')
            ->leftJoin('ratings', 'properties.id', '=', 'ratings.property_id')
            ->where('properties.is_active', 1);
        
        if ($preference) {
            $query->whereBetween('price', [$preference->budget_min, $preference->budget_max])
                  ->where('distance_from_campus', '<=', $preference->preferred_distance);
            
            if ($preference->gender_preference !== 'Any') {
                $query->where('gender_restriction', $preference->gender_preference);
            }
        }
        
        return $query->groupBy('properties.id')
            ->orderByDesc('popularity_score')
            ->limit($limit)
            ->get();
    }

    /**
     * Trigger model retraining
     */
    public function retrainModel()
    {
        if (!$this->isPythonServiceAvailable()) {
            return false;
        }

        try {
            $response = Http::timeout($this->pythonServiceTimeout)
                ->post("{$this->pythonServiceUrl}/retrain");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Error retraining model: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear user cache
     */
    public function clearUserCache($userId)
    {
        Cache::forget("recommendations_user_{$userId}");
    }
}