<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Rating;
use App\Observers\RatingObserver;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(CollaborativeFilteringService::class, function ($app) {
        return new CollaborativeFilteringService();
    });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot()
  {
      Rating::observe(RatingObserver::class);
  }
}
