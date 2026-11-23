<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $driver = User::factory()->driver()->create();
        $student = User::factory()->customer()->pending()->create();

        $this->actingAs($driver)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($driver)->patch(route('admin.users.approve', $student))->assertForbidden();

        $this->assertSame('pending', $student->refresh()->verification_status);
    }

    public function test_index_shows_pending_queue_oldest_first_without_admins(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->pending()->create(['name' => 'Admin Lain Pending']);
        $newest = User::factory()->customer()->pending()->create([
            'name' => 'Pendaftar Terbaru',
            'created_at' => now()->subHour(),
        ]);
        $oldest = User::factory()->driver()->pending()->create([
            'name' => 'Pendaftar Terlama',
            'created_at' => now()->subDay(),
        ]);
        User::factory()->customer()->approved()->create(['name' => 'Sudah Disetujui']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSeeInOrder(['Pendaftar Terlama', 'Pendaftar Terbaru'])
            ->assertDontSee('Sudah Disetujui')
            ->assertDontSee('Admin Lain Pending')
            ->assertViewHas('users', fn ($users): bool => $users->total() === 2);
    }

    public function test_index_can_filter_by_status_role_and_search(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->driver()->approved()->create(['name' => 'Budi Driver', 'npm' => '111']);
        User::factory()->customer()->approved()->create(['name' => 'Budi Customer', 'npm' => '222']);
        User::factory()->driver()->rejected()->create(['name' => 'Budi Ditolak', 'npm' => '333']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['status' => 'approved', 'role' => 'driver', 'search' => 'budi']))
            ->assertOk()
            ->assertSee('Budi Driver')
            ->assertDontSee('Budi Customer')
            ->assertDontSee('Budi Ditolak');

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['status' => 'all', 'search' => '333']))
            ->assertOk()
            ->assertSee('Budi Ditolak')
            ->assertDontSee('Budi Driver');
    }

    public function test_index_rejects_an_unknown_status_filter(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['status' => 'bogus']))
            ->assertSessionHasErrors('status');
    }

    public function test_show_displays_the_student_details(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->customer()->pending()->create([
            'name' => 'Siti Pendaftar',
            'npm' => '2410631170777',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $student))
            ->assertOk()
            ->assertSee('Siti Pendaftar')
            ->assertSee('2410631170777');
    }

    public function test_admin_accounts_cannot_be_managed(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.users.show', $otherAdmin))->assertNotFound();
        $this->actingAs($admin)->patch(route('admin.users.activation', $otherAdmin))->assertNotFound();
        $this->actingAs($admin)->patch(route('admin.users.activation', $admin))->assertNotFound();

        $this->assertTrue($otherAdmin->refresh()->is_active);
        $this->assertTrue($admin->refresh()->is_active);
    }

    public function test_admin_can_approve_a_pending_student(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->customer()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.approve', $student))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertSame('approved', $student->refresh()->verification_status);
    }

    public function test_approving_a_rejected_student_clears_the_rejection_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->customer()->rejected('Foto buram')->create();

        $this->actingAs($admin)->patch(route('admin.users.approve', $student));

        $student->refresh();
        $this->assertSame('approved', $student->verification_status);
        $this->assertNull($student->rejection_reason);
    }

    public function test_approving_an_already_approved_student_reports_an_error(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->customer()->approved()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.show', $student))
            ->patch(route('admin.users.approve', $student))
            ->assertRedirect(route('admin.users.show', $student))
            ->assertSessionHas('error');
    }

    public function test_admin_can_reject_a_student_with_a_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->driver()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.reject', $student), ['rejection_reason' => 'Foto KTM tidak terbaca.'])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $student->refresh();
        $this->assertSame('rejected', $student->verification_status);
        $this->assertSame('Foto KTM tidak terbaca.', $student->rejection_reason);
    }

    public function test_rejecting_requires_a_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->driver()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.reject', $student), [])
            ->assertSessionHasErrors([
                'rejection_reason' => 'Alasan penolakan wajib diisi agar mahasiswa tahu apa yang harus diperbaiki.',
            ]);

        $this->assertSame('pending', $student->refresh()->verification_status);
    }

    public function test_admin_can_deactivate_and_reactivate_an_account(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->customer()->approved()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.activation', $student))
            ->assertSessionHas('success');
        $this->assertFalse($student->refresh()->is_active);

        $this->actingAs($admin)
            ->patch(route('admin.users.activation', $student))
            ->assertSessionHas('success');
        $this->assertTrue($student->refresh()->is_active);
    }

    public function test_deactivated_account_cannot_log_in(): void
    {
        $student = User::factory()->customer()->approved()->inactive()->create();

        $this->post(route('login'), ['email' => $student->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_account_is_logged_out_on_the_next_request(): void
    {
        $student = User::factory()->customer()->approved()->inactive()->create();

        $this->actingAs($student)
            ->get(route('customer.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
