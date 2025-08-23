<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tunebook>
 */
class TunebookFactory extends Factory
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
            'name' => fake()->unique()->words(rand(2, 4), true),
            'user_id' => fake()->randomElement($ids),
        ];
    }
}
