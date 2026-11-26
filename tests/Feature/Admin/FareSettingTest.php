<?php

namespace Tests\Feature\Admin;

use App\Models\FareSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FareSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'setting_name' => 'Tarif Reguler',
            'base_fare' => 5000,
            'fare_per_km' => 3500,
            'min_distance_km' => 1,
            'is_active' => 1,
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.fare-settings.index'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_create_a_fare_setting(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)->get(route('admin.fare-settings.index'))->assertForbidden();
        $this->actingAs($customer)->post(route('admin.fare-settings.store'), $this->validPayload())->assertForbidden();

        $this->assertDatabaseCount('fare_settings', 0);
    }

    public function test_index_lists_fare_settings_with_the_active_one_first(): void
    {
        $admin = User::factory()->admin()->create();
        FareSetting::factory()->create(['setting_name' => 'Tarif Lama']);
        FareSetting::factory()->active()->create(['setting_name' => 'Tarif Berjalan', 'base_fare' => 6500]);

        $this->actingAs($admin)
            ->get(route('admin.fare-settings.index'))
            ->assertOk()
            ->assertSeeInOrder(['Tarif Berjalan', 'Tarif Lama'])
            ->assertSee('Rp6.500')
            ->assertDontSee('Belum ada tarif yang aktif');
    }

    public function test_index_warns_when_no_fare_is_active(): void
    {
        $admin = User::factory()->admin()->create();
        FareSetting::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.fare-settings.index'))
            ->assertSee('Belum ada tarif yang aktif');
    }

    public function test_create_and_edit_forms_are_rendered(): void
    {
        $admin = User::factory()->admin()->create();
        $fareSetting = FareSetting::factory()->create(['setting_name' => 'Tarif Kampus']);

        $this->actingAs($admin)
            ->get(route('admin.fare-settings.create'))
            ->assertOk()
            ->assertSee('Tambah Tarif');

        $this->actingAs($admin)
            ->get(route('admin.fare-settings.edit', $fareSetting))
            ->assertOk()
            ->assertSee('Tarif Kampus');
    }

    public function test_admin_can_create_a_fare_setting(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.fare-settings.store'), $this->validPayload())
            ->assertRedirect(route('admin.fare-settings.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('fare_settings', [
            'setting_name' => 'Tarif Reguler',
            'base_fare' => 5000,
            'fare_per_km' => 3500,
            'is_active' => true,
        ]);
    }

    public function test_creating_an_active_fare_deactivates_the_previous_one(): void
    {
        $admin = User::factory()->admin()->create();
        $previous = FareSetting::factory()->active()->create();

        $this->actingAs($admin)->post(route('admin.fare-settings.store'), $this->validPayload());

        $this->assertFalse($previous->refresh()->is_active);
        $this->assertSame(1, FareSetting::query()->where('is_active', true)->count());
    }

    public function test_creating_an_inactive_fare_keeps_the_current_one_active(): void
    {
        $admin = User::factory()->admin()->create();
        $current = FareSetting::factory()->active()->create();

        $this->actingAs($admin)->post(route('admin.fare-settings.store'), $this->validPayload(['is_active' => 0]));

        $this->assertTrue($current->refresh()->is_active);
        $this->assertSame(1, FareSetting::query()->where('is_active', true)->count());
    }

    public function test_store_requires_all_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.fare-settings.store'), [])
            ->assertSessionHasErrors(['setting_name', 'base_fare', 'fare_per_km', 'min_distance_km']);
    }

    /**
     * @return array<string, array{0: string, 1: mixed}>
     */
    public static function invalidFieldValues(): array
    {
        return [
            'negative base fare' => ['base_fare', -1],
            'non numeric base fare' => ['base_fare', 'abc'],
            'base fare over column limit' => ['base_fare', 100000000],
            'negative fare per km' => ['fare_per_km', -500],
            'negative minimum distance' => ['min_distance_km', -1],
            'minimum distance over column limit' => ['min_distance_km', 100],
        ];
    }

    #[DataProvider('invalidFieldValues')]
    public function test_store_rejects_invalid_values(string $field, mixed $value): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.fare-settings.store'), $this->validPayload([$field => $value]))
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('fare_settings', 0);
    }

    public function test_store_rejects_a_duplicate_name(): void
    {
        $admin = User::factory()->admin()->create();
        FareSetting::factory()->create(['setting_name' => 'Tarif Reguler']);

        $this->actingAs($admin)
            ->post(route('admin.fare-settings.store'), $this->validPayload())
            ->assertSessionHasErrors(['setting_name' => 'Nama tarif sudah digunakan.']);
    }

    public function test_admin_can_update_a_fare_setting_keeping_its_own_name(): void
    {
        $admin = User::factory()->admin()->create();
        $fareSetting = FareSetting::factory()->active()->create(['setting_name' => 'Tarif Reguler']);

        $this->actingAs($admin)
            ->put(route('admin.fare-settings.update', $fareSetting), $this->validPayload(['fare_per_km' => 4000]))
            ->assertRedirect(route('admin.fare-settings.index'))
            ->assertSessionHas('success');

        $this->assertEquals(4000, $fareSetting->refresh()->fare_per_km);
    }

    public function test_activating_a_fare_through_update_deactivates_the_other(): void
    {
        $admin = User::factory()->admin()->create();
        $current = FareSetting::factory()->active()->create();
        $next = FareSetting::factory()->create();

        $this->actingAs($admin)->put(
            route('admin.fare-settings.update', $next),
            $this->validPayload(['setting_name' => $next->setting_name, 'is_active' => 1]),
        );

        $this->assertTrue($next->refresh()->is_active);
        $this->assertFalse($current->refresh()->is_active);
    }

    public function test_the_active_fare_cannot_be_deactivated_directly(): void
    {
        $admin = User::factory()->admin()->create();
        $fareSetting = FareSetting::factory()->active()->create();

        $this->actingAs($admin)
            ->put(
                route('admin.fare-settings.update', $fareSetting),
                $this->validPayload(['setting_name' => $fareSetting->setting_name, 'is_active' => 0]),
            )
            ->assertSessionHasErrors('is_active');

        $this->assertTrue($fareSetting->refresh()->is_active);
    }

    public function test_admin_can_delete_an_inactive_fare(): void
    {
        $admin = User::factory()->admin()->create();
        $fareSetting = FareSetting::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.fare-settings.destroy', $fareSetting))
            ->assertRedirect(route('admin.fare-settings.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($fareSetting);
    }

    public function test_the_active_fare_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $fareSetting = FareSetting::factory()->active()->create();

        $this->actingAs($admin)
            ->delete(route('admin.fare-settings.destroy', $fareSetting))
            ->assertSessionHas('error');

        $this->assertModelExists($fareSetting);
    }
}
