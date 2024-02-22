<?php

namespace Backpack\Reviews\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

use function Orchestra\Testbench\artisan;
use function Orchestra\Testbench\workbench_path;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithMigration;
use Illuminate\Contracts\Config\Repository;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Backpack\Reviews\ServiceProvider as ReviewsServiceProvider;

use \Backpack\Reviews\app\FakeUser;
use \Backpack\Reviews\app\FakeAdmin;
use Backpack\Reviews\database\seeders\ReviewSeeder;

// #[WithMigration]
class TestCase extends Orchestra
{

  use RefreshDatabase;

  /**
   * Automatically enables package discoveries.
   *
   * @var bool
   */
  protected $enablesPackageDiscoveries = true;

  protected $admin;
  protected $user;

  protected function setUp(): void
  {
      parent::setUp();

      // Create fake admin
      $this->admin = FakeAdmin::factory()->create();

      // Create fake user
      $this->user = FakeUser::factory()->create();

      // Enter via backpack login system
      backpack_auth()->login($this->admin);
      
      $this->seed(ReviewSeeder::class);

      // xz
      Factory::guessFactoryNamesUsing(
        fn (string $modelName) => 'Backpack\\Reviews\\Database\\Factories\\'.class_basename($modelName).'Factory'
      );
  }

  protected function getPackageProviders($app)
  {
      return [
        ReviewsServiceProvider::class,
      ];
  }

    /**
   * Define database migrations.
   *
   * @return void
   */
  protected function defineDatabaseMigrations()
  {
    $this->loadMigrationsFrom(__DIR__.'/database/migrations');
  }

   /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {

      $app['config']->set('auth.guards', [
        'web' => [
          'driver' => 'session',
          'provider' => 'users',
        ],
        'profile' => [
          'driver' => 'session',
          'provider' => 'fakeUser',
        ]
      ]);

      $app['config']->set('auth.providers', [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'fakeUser' => [
            'driver' => 'eloquent',
            'model' => FakeUser::class,
        ],
      ]);

      // Setup default database to use sqlite :memory:
      tap($app['config'], function (Repository $config) {
          $config->set('database.default', 'testbench');
          $config->set('database.connections.testbench', [
              'driver'   => 'sqlite',
              'database' => ':memory:',
              'prefix'   => '',
          ]);
      });
    }
}
