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
      $OWNER_MODEL = config('backpack.reviews.owner_model', 'Backpack\Profile\app\Models\Profile');
      $REVIEWABLE_MODEL = config('backpack.reviews.reviewable_model', 'Backpack\Store\app\Models\Product');

      $user = $OWNER_MODEL::inRandomOrder()->first();
      $reviewable_list = $REVIEWABLE_MODEL::inRandomOrder()->limit(30)->get();

      foreach($reviewable_list as $item){
        Review::factory()
            ->count(10)
            ->for($user, 'owner')
            ->for($item, 'reviewable')
            ->hasChildren(3, [
              'owner_id' => $user->id,
            ])
            ->create();
      }
    }
}
