<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'category' => fake()->randomElement(Location::CATEGORIES),
            'latitude' => fake()->latitude(-6.4, -6.2),
            'longitude' => fake()->longitude(107.2, 107.4),
            'address_detail' => fake()->streetAddress(),
        ];
    }
}
