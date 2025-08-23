<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ids = User::pluck('id')->toArray();

        return [
            'content' => fake()->text(),
            'user_id' => fake()->randomElement($ids),
            'is_suggestion' => fake()->boolean(),
        ];
    }
}
