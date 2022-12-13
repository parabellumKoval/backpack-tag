<?php

namespace Backpack\Reviews;

use Backpack\Reviews\app\Observers\ReviewObserver;
use Backpack\Reviews\app\Models\Review;

class ReviewsServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/reviews.php';
    
    public function boot()
    {
        Review::observe(ReviewObserver::class);

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'review');
    
	    // Migrations
	    $this->loadMigrationsFrom(__DIR__.'/database/migrations');
	    
	    // Routes
    	$this->loadRoutesFrom(__DIR__.'/routes/backpack/routes.php');
    	$this->loadRoutesFrom(__DIR__.'/routes/api/review.php');
    
		  // Config

      $this->publishes([
          self::CONFIG_PATH => config_path('backpack/reviews.php'),
      ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'reviews'
        );

        $this->app->bind('review', function () {
            return new Reviews();
        });
    }
}
