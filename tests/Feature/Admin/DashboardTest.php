<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->driver()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_shows_the_summary_counters(): void
    {
        $admin = User::factory()->admin()->create();

        // Mahasiswa terverifikasi: 2 driver (1 aktif, 1 nonaktif) + 1 customer.
        User::factory()->driver()->approved()->create();
        User::factory()->driver()->approved()->inactive()->create();
        User::factory()->customer()->approved()->create();
        // Belum terverifikasi atau ditolak tidak dihitung sebagai terdaftar.
        User::factory()->customer()->pending()->count(2)->create();
        User::factory()->driver()->rejected()->create();

        $customer = User::factory()->customer()->approved()->create();
        Order::factory()->for($customer, 'customer')->count(3)->create();
        Order::factory()->for($customer, 'customer')->create(['created_at' => now()->subDays(2)]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('totalStudents', 4)
            ->assertViewHas('activeDrivers', 1)
            ->assertViewHas('pendingVerifications', 2)
            ->assertViewHas('transactionsToday', 3)
            ->assertViewHas('completedToday', 0);
    }

    public function test_dashboard_counts_completed_transactions_today(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->completed()->create();
        Order::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('transactionsToday', 2)
            ->assertViewHas('completedToday', 1);
    }

    public function test_dashboard_lists_the_oldest_pending_registrants_first(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->customer()->pending()->create(['name' => 'Mendaftar Belakangan', 'created_at' => now()->subHour()]);
        User::factory()->customer()->pending()->create(['name' => 'Mendaftar Duluan', 'created_at' => now()->subDay()]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSeeInOrder(['Mendaftar Duluan', 'Mendaftar Belakangan']);
    }

    public function test_generic_dashboard_route_redirects_admins_to_the_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
