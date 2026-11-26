<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverOrderBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_sees_only_open_orders_with_ten_orders_per_page(): void
    {
        $driver = $this->createUser('driver');
        $customer = $this->createUser('customer');

        foreach (range(1, 11) as $number) {
            $this->createOrder($customer, sprintf('KJ-OPEN-%02d', $number));
        }

        $this->createOrder($customer, 'KJ-TAKEN', 'Kampus 1', 'Galuh Mas', 'taken', $driver);

        $response = $this->actingAs($driver)->get(route('driver.orders.index'));

        $response->assertOk()
            ->assertViewHas('orders', function ($orders): bool {
                return $orders->total() === 11
                    && $orders->perPage() === 10
                    && $orders->count() === 10;
            })
            ->assertSee('KJ-OPEN-11')
            ->assertDontSee('KJ-TAKEN');
    }

    public function test_driver_can_search_by_keyword_and_filter_pickup_and_destination(): void
    {
        $driver = $this->createUser('driver');
        $customer = $this->createUser('customer');

        $this->createOrder($customer, 'KJ-MATCH', 'Kampus UNSIKA 1', 'Galuh Mas');
        $this->createOrder($customer, 'KJ-WRONG-DESTINATION', 'Kampus UNSIKA 1', 'Telukjambe');
        $this->createOrder($customer, 'KJ-WRONG-PICKUP', 'Kampus 2', 'Galuh Mas');
        $this->createOrder($customer, 'KJ-WRONG-KEYWORD', 'Kampus UNSIKA 1', 'Galuh Mas', 'open', null, 'Tidak cocok');

        $response = $this->actingAs($driver)->get(route('driver.orders.index', [
            'search' => 'kj-match',
            'pickup' => 'unsika 1',
            'destination' => 'galuh mas',
        ]));

        $response->assertOk()
            ->assertSee('KJ-MATCH')
            ->assertDontSee('KJ-WRONG-DESTINATION')
            ->assertDontSee('KJ-WRONG-PICKUP')
            ->assertDontSee('KJ-WRONG-KEYWORD');
    }

    public function test_order_board_requires_authentication(): void
    {
        $this->get(route('driver.orders.index'))
            ->assertRedirect(route('login'));
    }

    public function test_only_verified_drivers_can_open_the_order_board(): void
    {
        $customer = $this->createUser('customer');
        $pendingDriver = $this->createUser('driver', 'pending');

        $this->actingAs($customer)
            ->get(route('driver.orders.index'))
            ->assertForbidden();

        $this->actingAs($pendingDriver)
            ->get(route('driver.orders.index'))
            ->assertRedirect(route('verification.notice.student'));
    }

    public function test_driver_can_take_an_open_order(): void
    {
        $driver = $this->createUser('driver');
        $customer = $this->createUser('customer');
        $order = $this->createOrder($customer, 'KJ-TAKE-ME');

        $response = $this->actingAs($driver)
            ->post(route('driver.orders.take', $order));

        $response->assertRedirect(route('driver.orders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'driver_id' => $driver->id,
            'driver_vehicle_name' => $driver->vehicle_name,
            'driver_license_plate' => $driver->license_plate,
            'status' => 'taken',
        ]);
        $this->assertNotNull($order->refresh()->taken_at);
    }

    public function test_second_driver_cannot_take_an_order_already_claimed(): void
    {
        $firstDriver = $this->createUser('driver');
        $secondDriver = $this->createUser('driver');
        $customer = $this->createUser('customer');
        $order = $this->createOrder($customer, 'KJ-ONE-DRIVER-ONLY');

        $this->actingAs($firstDriver)
            ->post(route('driver.orders.take', $order))
            ->assertRedirect(route('driver.orders.index'));

        $this->actingAs($secondDriver)
            ->post(route('driver.orders.take', $order))
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'driver_id' => $firstDriver->id,
            'status' => 'taken',
        ]);
    }

    public function test_driver_without_vehicle_details_cannot_take_an_order(): void
    {
        $driver = $this->createUser('driver', 'approved', null, null);
        $customer = $this->createUser('customer');
        $order = $this->createOrder($customer, 'KJ-NEED-VEHICLE');

        $response = $this->actingAs($driver)
            ->post(route('driver.orders.take', $order));

        $response->assertRedirect(route('driver.orders.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'driver_id' => null,
            'status' => 'open',
        ]);
    }

    public function test_driver_cannot_take_a_second_order_while_an_order_is_active(): void
    {
        $driver = $this->createUser('driver');
        $customer = $this->createUser('customer');
        $activeOrder = $this->createOrder($customer, 'KJ-AKTIF', status: 'taken', driver: $driver);
        $openOrder = $this->createOrder($customer, 'KJ-BARU');

        $this->actingAs($driver)
            ->post(route('driver.orders.take', $openOrder))
            ->assertRedirect(route('driver.orders.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $openOrder->id,
            'status' => 'open',
            'driver_id' => null,
        ]);
        $this->assertSame('taken', $activeOrder->refresh()->status);
    }

    public function test_assigned_driver_can_move_order_from_taken_to_on_the_way(): void
    {
        $driver = $this->createUser('driver');
        $customer = $this->createUser('customer');
        $order = $this->createOrder($customer, 'KJ-JALAN', status: 'taken', driver: $driver);

        $this->actingAs($driver)
            ->patch(route('driver.orders.on-the-way', $order))
            ->assertRedirect(route('driver.dashboard'))
            ->assertSessionHas('success');

        $this->assertSame('on_the_way', $order->refresh()->status);
    }

    public function test_assigned_driver_can_complete_on_the_way_order(): void
    {
        $driver = $this->createUser('driver');
        $customer = $this->createUser('customer');
        $order = $this->createOrder($customer, 'KJ-SELESAI', status: 'on_the_way', driver: $driver);

        $this->actingAs($driver)
            ->patch(route('driver.orders.complete', $order))
            ->assertRedirect(route('driver.dashboard'))
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertSame('completed', $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function test_other_driver_cannot_change_assigned_order_status(): void
    {
        $owner = $this->createUser('driver');
        $otherDriver = $this->createUser('driver');
        $customer = $this->createUser('customer');
        $order = $this->createOrder($customer, 'KJ-MILIK-ORANG', status: 'taken', driver: $owner);

        $this->actingAs($otherDriver)
            ->patch(route('driver.orders.on-the-way', $order))
            ->assertForbidden();

        $this->assertSame('taken', $order->refresh()->status);
    }

    public function test_driver_cannot_skip_status_transitions(): void
    {
        $driver = $this->createUser('driver');
        $customer = $this->createUser('customer');
        $order = $this->createOrder($customer, 'KJ-LONCAT', status: 'taken', driver: $driver);

        $this->actingAs($driver)
            ->patch(route('driver.orders.complete', $order))
            ->assertRedirect(route('driver.dashboard'))
            ->assertSessionHas('error');

        $this->assertSame('taken', $order->refresh()->status);
        $this->assertNull($order->completed_at);
    }

    public function test_driver_dashboard_shows_active_order_and_today_metrics(): void
    {
        $driver = $this->createUser('driver');
        $customer = $this->createUser('customer');
        $activeOrder = $this->createOrder($customer, 'KJ-AKTIF-DASHBOARD', status: 'on_the_way', driver: $driver);
        $completedOrder = $this->createOrder($customer, 'KJ-HARI-INI', status: 'completed', driver: $driver);
        $completedOrder->update([
            'completed_at' => now(),
            'total_fare' => 25000,
        ]);

        $this->assertSame('on_the_way', $activeOrder->refresh()->status);
        $this->assertSame(25000, (int) $completedOrder->refresh()->total_fare);

        $this->actingAs($driver)
            ->get(route('driver.dashboard'))
            ->assertOk()
            ->assertViewHas('activeOrder', fn (Order $order): bool => $order->is($activeOrder))
            ->assertViewHas('completedToday', 1)
            ->assertViewHas('earningsToday', fn ($earnings): bool => (float) $earnings === 25000.0)
            ->assertSee('KJ-AKTIF-DASHBOARD')
            ->assertSee('Rp25.000');
    }

    private function createUser(
        string $role,
        string $verificationStatus = 'approved',
        ?string $vehicleName = 'Honda Vario 125',
        ?string $licensePlate = 'T 1234 AB',
    ): User {
        $id = str()->uuid()->toString();

        return User::create([
            'name' => ucfirst($role).' Test',
            'email' => "{$role}-{$id}@example.test",
            'npm' => $id,
            'phone_number' => '081234567890',
            'role' => $role,
            'vehicle_name' => $role === 'driver' ? $vehicleName : null,
            'license_plate' => $role === 'driver' ? $licensePlate : null,
            'verification_status' => $verificationStatus,
            'password' => 'password',
        ]);
    }

    private function createOrder(
        User $customer,
        string $orderCode,
        string $pickupAddress = 'Kampus UNSIKA 1',
        string $destinationAddress = 'Galuh Mas',
        string $status = 'open',
        ?User $driver = null,
        ?string $notes = null,
    ): Order {
        return Order::create([
            'order_code' => $orderCode,
            'customer_id' => $customer->id,
            'driver_id' => $driver?->id,
            'pickup_address' => $pickupAddress,
            'pickup_latitude' => -6.3,
            'pickup_longitude' => 107.3,
            'destination_address' => $destinationAddress,
            'destination_latitude' => -6.31,
            'destination_longitude' => 107.31,
            'distance_km' => 2.5,
            'total_fare' => 10000,
            'notes' => $notes,
            'status' => $status,
            'taken_at' => $driver ? now() : null,
        ]);
    }
}
