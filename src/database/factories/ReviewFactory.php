<?php

namespace Backpack\Reviews\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Backpack\Reviews\app\Models\Review;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        'is_moderated' => $this->faker->randomElement([1,0]),
        'name' => $this->faker->name(),
        'email' => 'ex@ex.com',
        'likes' => $this->faker->randomDigit(),
        'dislikes' => $this->faker->randomDigit(),
        'text' => $this->faker->paragraph(2),
      ];
    }

}
