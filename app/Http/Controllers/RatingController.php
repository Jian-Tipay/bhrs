<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\CollaborativeFilteringService;

class RatingController extends Controller
{
    protected $cfService;

    public function __construct(CollaborativeFilteringService $cfService)
    {
        $this->middleware('auth');
        $this->cfService = $cfService;
    }

    /**
     * Store a new rating
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'rating' => 'required|numeric|min:1|max:5',
            'review_text' => 'nullable|string|max:1000'
        ]);

        $userId = Auth::id();
        $propertyId = $request->property_id;

        // Check if property exists and is active
        $property = Property::where('id', $propertyId)
            ->where('is_active', 1)
            ->first();

        if (!$property) {
            return back()->with('error', 'Property not found or is inactive.');
        }

        // Check if user already rated this property
        $existingRating = Rating::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->first();

        if ($existingRating) {
            return back()->with('error', 'You have already rated this property. You can update your rating instead.');
        }

        try {
            // Create new rating
            $rating = Rating::create([
                'user_id' => $userId,
                'property_id' => $propertyId,
                'rating' => $request->rating,
                'review_text' => $request->review_text
            ]);

            // Clear cache and trigger CF retrain if service is available
            $this->cfService->clearUserCache($userId);
            
            if ($this->cfService->isPythonServiceAvailable()) {
                $this->cfService->retrainModel();
            }

            // Check if this is user's second rating (CF activation threshold)
            $userRatingsCount = Rating::where('user_id', $userId)->count();
            
            $message = 'Thank you for your rating!';
            if ($userRatingsCount == 2) {
                $message = '🎉 Congratulations! You\'ve unlocked AI-powered recommendations! Check your dashboard for personalized property suggestions.';
            } elseif ($userRatingsCount == 1) {
                $message = 'Thank you for your rating! Rate one more property to unlock AI-powered recommendations.';
            }

            Log::info("User {$userId} rated property {$propertyId} with {$request->rating} stars");

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Rating creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit rating. Please try again.');
        }
    }

    /**
     * Update existing rating
     */
    public function update(Request $request, $ratingId)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review_text' => 'nullable|string|max:1000'
        ]);

        $userId = Auth::id();

        $rating = Rating::where('rating_id', $ratingId)
            ->where('user_id', $userId)
            ->first();

        if (!$rating) {
            return back()->with('error', 'Rating not found or you do not have permission to update it.');
        }

        try {
            $rating->update([
                'rating' => $request->rating,
                'review_text' => $request->review_text
            ]);

            // Clear cache and trigger CF retrain
            $this->cfService->clearUserCache($userId);
            
            if ($this->cfService->isPythonServiceAvailable()) {
                $this->cfService->retrainModel();
            }

            Log::info("User {$userId} updated rating {$ratingId}");

            return back()->with('success', 'Your rating has been updated successfully!');

        } catch (\Exception $e) {
            Log::error('Rating update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update rating. Please try again.');
        }
    }

    /**
     * Delete rating
     */
    public function destroy($ratingId)
    {
        $userId = Auth::id();

        $rating = Rating::where('rating_id', $ratingId)
            ->where('user_id', $userId)
            ->first();

        if (!$rating) {
            return back()->with('error', 'Rating not found or you do not have permission to delete it.');
        }

        try {
            $rating->delete();

            // Clear cache and trigger CF retrain
            $this->cfService->clearUserCache($userId);
            
            if ($this->cfService->isPythonServiceAvailable()) {
                $this->cfService->retrainModel();
            }

            Log::info("User {$userId} deleted rating {$ratingId}");

            return back()->with('success', 'Your rating has been deleted.');

        } catch (\Exception $e) {
            Log::error('Rating deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete rating. Please try again.');
        }
    }

    /**
     * Get user's ratings
     */
    public function getUserRatings()
    {
        $userId = Auth::id();
        
        $ratings = Rating::where('user_id', $userId)
            ->with('property')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('content.ratings.index', compact('ratings'));
    }
}