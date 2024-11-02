<?php

namespace Backpack\Tag;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/config/tag.php';
    
    public function boot()
    {

      $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'tag');
    
	    // Migrations
	    $this->loadMigrationsFrom(__DIR__.'/database/migrations');
	    
	    // Routes
    	$this->loadRoutesFrom(__DIR__.'/routes/backpack/routes.php');
    
		  // Config

      $this->publishes([
        self::CONFIG_PATH => config_path('/backpack/tag.php'),
      ], 'config');
      
      $this->publishes([
          __DIR__.'/resources/views' => resource_path('views'),
      ], 'views');

      $this->publishes([
          __DIR__.'/database/migrations' => resource_path('database/migrations'),
      ], 'migrations');

      $this->publishes([
          __DIR__.'/routes/backpack/routes.php' => resource_path('/routes/backpack/tag/routes.php')
      ], 'routes');


      $this->publishes([
          __DIR__.'/public' => public_path('packages/backpack/tag'),
      ], 'public');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'tag'
        );
    }
}
