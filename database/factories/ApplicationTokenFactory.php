<?php

namespace Database\Factories;

use App\Models\ApplicationToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationToken>
 */
class ApplicationTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => null, // Will be set during seeding
            'token' => ApplicationToken::generateToken(),
            'expires_at' => now()->addDays($this->faker->numberBetween(30, 60)),
            'universal' => fake()->boolean(10),
        ];
    }
}
