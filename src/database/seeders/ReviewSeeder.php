<?php

namespace Backpack\Reviews\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

use Backpack\Reviews\app\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      $USER_MODEL = config('backpack.reviews.user_model', 'App\Models\User');
      $PRODUCT_MODEL = config('backpack.reviews.user_model', 'App\Models\User');

      $user = $USER_MODEL::inRandomOrder()->first();
      $product = $PRODUCT_MODEL::inRandomOrder()->first();

      Review::factory()
          ->count(10)
          ->for($user)
          ->for($product)
          ->create();
    }
}
