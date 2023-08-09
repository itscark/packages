<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Str::title(str_replace('-', ' ', fake()->unique()->slug(2))),
            'technical_name' => 'iwaves/' . fake()->unique()->slug(3),
            'url' => 'git@bitbucket.org:iwaves/' . fake()->unique()->slug(3) . '.git',
            'download_count' => fake()->numberBetween(0, 1000),
        ];
    }
}
