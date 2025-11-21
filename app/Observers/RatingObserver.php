<?php

namespace App\Observers;

use App\Models\Rating;
use App\Services\CollaborativeFilteringService;

class RatingObserver
{
    protected $cfService;

    public function __construct(CollaborativeFilteringService $cfService)
    {
        $this->cfService = $cfService;
    }

    /**
     * Handle the Rating "created" event.
     */
    public function created(Rating $rating)
    {
        // Clear cache when new rating is added
        $this->cfService->clearUserCache($rating->user_id);
    }

    /**
     * Handle the Rating "updated" event.
     */
    public function updated(Rating $rating)
    {
        // Clear cache when rating is updated
        $this->cfService->clearUserCache($rating->user_id);
    }
}