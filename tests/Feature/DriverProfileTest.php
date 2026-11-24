<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_update_vehicle_details_from_profile(): void
    {
        $driver = User::create([
            'name' => 'Driver Lama',
            'email' => 'driver-profile@example.test',
            'npm' => '2410631170101',
            'phone_number' => '081234567890',
            'role' => 'driver',
            'vehicle_name' => 'Honda Beat',
            'license_plate' => 'T 1111 AA',
            'verification_status' => 'approved',
            'password' => 'password',
        ]);

        $response = $this->actingAs($driver)->patch(route('profile.update'), [
            'name' => 'Driver Baru',
            'email' => 'driver-profile@example.test',
            'vehicle_name' => 'Yamaha NMAX',
            'license_plate' => 'b 9876 xyz',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $driver->id,
            'name' => 'Driver Baru',
            'vehicle_name' => 'Yamaha NMAX',
            'license_plate' => 'B 9876 XYZ',
        ]);
    }

    public function test_driver_cannot_clear_vehicle_details_on_profile_update(): void
    {
        $driver = User::create([
            'name' => 'Driver Lama',
            'email' => 'driver-profile2@example.test',
            'npm' => '2410631170102',
            'phone_number' => '081234567890',
            'role' => 'driver',
            'vehicle_name' => 'Honda Beat',
            'license_plate' => 'T 1111 AA',
            'verification_status' => 'approved',
            'password' => 'password',
        ]);

        $response = $this->actingAs($driver)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), [
                'name' => 'Driver Lama',
                'email' => 'driver-profile2@example.test',
                'vehicle_name' => '',
                'license_plate' => '',
            ]);

        $response->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors(['vehicle_name', 'license_plate']);
    }

    public function test_driver_cannot_change_vehicle_details_while_having_an_active_order(): void
    {
        $driver = User::create([
            'name' => 'Driver Aktif',
            'email' => 'driver-active@example.test',
            'npm' => '2410631170104',
            'phone_number' => '081234567890',
            'role' => 'driver',
            'vehicle_name' => 'Honda Vario 125',
            'license_plate' => 'T 1234 AB',
            'verification_status' => 'approved',
            'password' => 'password',
        ]);

        $customer = User::create([
            'name' => 'Customer Aktif',
            'email' => 'customer-active@example.test',
            'npm' => '2410631170105',
            'phone_number' => '081234567891',
            'role' => 'customer',
            'verification_status' => 'approved',
            'password' => 'password',
        ]);

        Order::create([
            'order_code' => 'KJ-ACTIVE-01',
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'driver_vehicle_name' => 'Honda Vario 125',
            'driver_license_plate' => 'T 1234 AB',
            'pickup_address' => 'Kampus 1',
            'pickup_latitude' => -6.3,
            'pickup_longitude' => 107.3,
            'destination_address' => 'Galuh Mas',
            'destination_latitude' => -6.31,
            'destination_longitude' => 107.31,
            'distance_km' => 2.5,
            'total_fare' => 10000,
            'status' => 'taken',
            'taken_at' => now(),
        ]);

        $response = $this->actingAs($driver)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), [
                'name' => 'Driver Aktif',
                'email' => 'driver-active@example.test',
                'vehicle_name' => 'Yamaha NMAX',
                'license_plate' => 'B 9999 XX',
            ]);

        $response->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors(['vehicle_name', 'license_plate']);

        $this->assertDatabaseHas('users', [
            'id' => $driver->id,
            'vehicle_name' => 'Honda Vario 125',
            'license_plate' => 'T 1234 AB',
        ]);
    }

    public function test_updating_vehicle_after_completed_order_does_not_overwrite_order_snapshot(): void
    {
        $driver = User::create([
            'name' => 'Driver Selesai',
            'email' => 'driver-done@example.test',
            'npm' => '2410631170106',
            'phone_number' => '081234567890',
            'role' => 'driver',
            'vehicle_name' => 'Honda Vario 125',
            'license_plate' => 'T 1234 AB',
            'verification_status' => 'approved',
            'password' => 'password',
        ]);

        $customer = User::create([
            'name' => 'Customer Selesai',
            'email' => 'customer-done@example.test',
            'npm' => '2410631170107',
            'phone_number' => '081234567891',
            'role' => 'customer',
            'verification_status' => 'approved',
            'password' => 'password',
        ]);

        $order = Order::create([
            'order_code' => 'KJ-DONE-01',
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'driver_vehicle_name' => 'Honda Vario 125',
            'driver_license_plate' => 'T 1234 AB',
            'pickup_address' => 'Kampus 1',
            'pickup_latitude' => -6.3,
            'pickup_longitude' => 107.3,
            'destination_address' => 'Galuh Mas',
            'destination_latitude' => -6.31,
            'destination_longitude' => 107.31,
            'distance_km' => 2.5,
            'total_fare' => 10000,
            'status' => 'completed',
            'taken_at' => now()->subHour(),
            'completed_at' => now(),
        ]);

        $this->actingAs($driver)
            ->patch(route('profile.update'), [
                'name' => 'Driver Selesai',
                'email' => 'driver-done@example.test',
                'vehicle_name' => 'Yamaha NMAX',
                'license_plate' => 'T 5678 CD',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $driver->id,
            'vehicle_name' => 'Yamaha NMAX',
            'license_plate' => 'T 5678 CD',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'driver_vehicle_name' => 'Honda Vario 125',
            'driver_license_plate' => 'T 1234 AB',
        ]);
    }

    public function test_customer_profile_update_ignores_vehicle_fields(): void
    {
        $customer = User::create([
            'name' => 'Customer Biasa',
            'email' => 'customer-profile@example.test',
            'npm' => '2410631170103',
            'phone_number' => '081234567890',
            'role' => 'customer',
            'verification_status' => 'approved',
            'password' => 'password',
        ]);

        $response = $this->actingAs($customer)->patch(route('profile.update'), [
            'name' => 'Customer Updated',
            'email' => 'customer-profile@example.test',
            'vehicle_name' => 'Motor Gelap',
            'license_plate' => 'T 9999 ZZ',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'Customer Updated',
            'vehicle_name' => null,
            'license_plate' => null,
        ]);
    }
}