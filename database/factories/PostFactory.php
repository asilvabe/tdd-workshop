<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ];
    }

    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'approved_at' => $this->faker->dateTime(),
            ];
        });
    }

    public function unapproved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'approved_at' => null,
            ];
        });
    }
}
