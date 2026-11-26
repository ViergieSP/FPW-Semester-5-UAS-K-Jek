<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'npm' => fake()->unique()->numerify('############'),
            'phone_number' => fake()->numerify('08##########'),
            'role' => 'customer',
            'verification_status' => 'approved',
            'is_active' => true,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin']);
    }

    public function driver(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'driver']);
    }

    public function customer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'customer']);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'pending',
            'rejection_reason' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'approved',
            'rejection_reason' => null,
        ]);
    }

    public function rejected(string $reason = 'Foto tidak jelas.'): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
