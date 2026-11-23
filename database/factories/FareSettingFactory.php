<?php

namespace Database\Factories;

use App\Models\FareSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FareSetting>
 */
class FareSettingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'setting_name' => 'Tarif '.fake()->unique()->words(2, true),
            'base_fare' => 5000,
            'fare_per_km' => 3500,
            'min_distance_km' => 1,
            'is_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => true]);
    }
}
