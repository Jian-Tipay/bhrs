<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Services\CollaborativeFilteringService;
use App\Models\Property;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $cfService;
    protected $pythonApiUrl = 'http://127.0.0.1:8001/recommendations';

    public function __construct(CollaborativeFilteringService $cfService)
    {
        $this->cfService = $cfService;
    }

    /**
     * Display personalized recommendations
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $limit = $request->get('limit', 10);

        try {
            $response = Http::post($this->pythonApiUrl, [
                'user_id' => $userId,
                'limit' => $limit,
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Failed to connect to recommendation engine.');
            }

            $data = $response->json();
            $recommendations = $data['recommendations'] ?? [];
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }

        return view('content.recommendations.index', compact('recommendations'));
    }

    /**
     * Get recommendations via API
     */
    public function getRecommendationsApi(Request $request)
    {
        $userId = Auth::id();
        $limit = $request->get('limit', 10);

        $recommendations = $this->cfService->getRecommendations($userId, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations,
            'count' => $recommendations->count()
        ]);
    }

    /**
     * Predict rating for a property
     */
    public function predictRating($propertyId)
    {
        $userId = Auth::id();
        $predictedRating = $this->cfService->predictRating($userId, $propertyId);

        return response()->json([
            'success' => true,
            'predicted_rating' => round($predictedRating, 2)
        ]);
    }

    /**
     * Track recommendation click
     */
    public function trackClick(Request $request, $propertyId)
    {
        $userId = Auth::id();

        // Update recommendation log
        \DB::table('recommendation_logs')
            ->where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->update(['was_clicked' => true]);

        // Track property view
        \DB::table('property_views')->insert([
            'user_id' => $userId,
            'property_id' => $propertyId,
            'created_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Refresh recommendations (clear cache)
     */
    public function refresh()
    {
        $userId = Auth::id();
        $this->cfService->clearUserCache($userId);

        return redirect()->back()->with('success', 'Recommendations refreshed!');
    }
}
