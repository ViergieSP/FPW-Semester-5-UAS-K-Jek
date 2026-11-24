<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_customer_can_register_without_vehicle_details(): void
    {
        Storage::fake('s3');

        $response = $this->post('/register', [
            'name' => 'Siti Customer',
            'email' => 'siti@example.com',
            'npm' => '2410631170001',
            'phone_number' => '081234567890',
            'role' => 'customer',
            'profile_photo' => UploadedFile::fake()->image('photo.jpg'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'siti@example.com',
            'role' => 'customer',
            'vehicle_name' => null,
            'license_plate' => null,
        ]);
    }

    public function test_driver_can_register_with_vehicle_details_and_normalized_license_plate(): void
    {
        Storage::fake('s3');

        $response = $this->post('/register', [
            'name' => 'Budi Driver',
            'email' => 'budi@example.com',
            'npm' => '2410631170002',
            'phone_number' => '081234567891',
            'role' => 'driver',
            'vehicle_name' => 'Honda   Vario 160',
            'license_plate' => 't1234ab',
            'profile_photo' => UploadedFile::fake()->image('photo.jpg'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'driver',
            'vehicle_name' => 'Honda Vario 160',
            'license_plate' => 'T 1234 AB',
        ]);
    }

    public function test_driver_registration_requires_vehicle_name_and_license_plate(): void
    {
        Storage::fake('s3');

        $response = $this->from('/register')->post('/register', [
            'name' => 'Budi Driver',
            'email' => 'budi@example.com',
            'npm' => '2410631170002',
            'phone_number' => '081234567891',
            'role' => 'driver',
            'profile_photo' => UploadedFile::fake()->image('photo.jpg'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/register')
            ->assertSessionHasErrors(['vehicle_name', 'license_plate']);
        $this->assertNull(User::where('email', 'budi@example.com')->first());
    }

    public function test_driver_registration_rejects_invalid_license_plate_format(): void
    {
        Storage::fake('s3');

        $response = $this->from('/register')->post('/register', [
            'name' => 'Budi Driver',
            'email' => 'budi@example.com',
            'npm' => '2410631170002',
            'phone_number' => '081234567891',
            'role' => 'driver',
            'vehicle_name' => 'Honda Beat',
            'license_plate' => 'INVALID-123',
            'profile_photo' => UploadedFile::fake()->image('photo.jpg'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/register')
            ->assertSessionHasErrors(['license_plate']);
    }
}