<?php

namespace Backpack\Reviews;

use Backpack\Reviews\app\Observers\ReviewObserver;
use Backpack\Reviews\app\Models\Review;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/config/reviews.php';
    
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
        self::CONFIG_PATH => config_path('/backpack/reviews.php'),
      ], 'config');
      
      $this->publishes([
          __DIR__.'/resources/views' => resource_path('views'),
      ], 'views');

      $this->publishes([
          __DIR__.'/database/migrations' => resource_path('database/migrations'),
      ], 'migrations');

      $this->publishes([
          __DIR__.'/routes/backpack/routes.php' => resource_path('/routes/backpack/reviews/routes.php'),
          __DIR__.'/routes/api/review.php' => resource_path('/routes/backpack/reviews/api.php'),
      ], 'routes');
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
