<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_code' => 'KJ-'.Str::upper(Str::random(8)),
            'customer_id' => User::factory(),
            'driver_id' => null,
            'pickup_address' => 'Kampus UNSIKA 1',
            'pickup_latitude' => -6.3,
            'pickup_longitude' => 107.3,
            'destination_address' => 'Galuh Mas',
            'destination_latitude' => -6.31,
            'destination_longitude' => 107.31,
            'distance_km' => 2.5,
            'total_fare' => 13750,
            'status' => 'open',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'driver_id' => User::factory()->driver(),
            'status' => 'completed',
            'taken_at' => now(),
            'completed_at' => now(),
        ]);
    }
}
