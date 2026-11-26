<?php

namespace Tests\Feature\Admin;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LocationTest extends TestCase
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
            'name' => 'Gerbang Utama UNSIKA',
            'category' => 'Gerbang',
            'latitude' => -6.3,
            'longitude' => 107.3,
            'address_detail' => 'Jl. HS Ronggowaluyo',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.locations.index'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_manage_locations(): void
    {
        $driver = User::factory()->driver()->create();

        $this->actingAs($driver)->get(route('admin.locations.index'))->assertForbidden();
        $this->actingAs($driver)->post(route('admin.locations.store'), $this->validPayload())->assertForbidden();

        $this->assertDatabaseCount('locations', 0);
    }

    public function test_index_can_search_and_filter_by_category(): void
    {
        $admin = User::factory()->admin()->create();
        Location::factory()->create(['name' => 'Fakultas Teknik', 'category' => 'Fakultas']);
        Location::factory()->create(['name' => 'Gerbang Barat', 'category' => 'Gerbang']);
        Location::factory()->create(['name' => 'Fakultas Hukum', 'category' => 'Fakultas']);

        $this->actingAs($admin)
            ->get(route('admin.locations.index', ['search' => 'teknik']))
            ->assertOk()
            ->assertSee('Fakultas Teknik')
            ->assertDontSee('Fakultas Hukum');

        $this->actingAs($admin)
            ->get(route('admin.locations.index', ['category' => 'Gerbang']))
            ->assertSee('Gerbang Barat')
            ->assertDontSee('Fakultas Teknik');
    }

    public function test_index_paginates_ten_locations_per_page(): void
    {
        $admin = User::factory()->admin()->create();
        Location::factory()->count(11)->create();

        $this->actingAs($admin)
            ->get(route('admin.locations.index'))
            ->assertViewHas('locations', fn ($locations): bool => $locations->total() === 11
                && $locations->count() === 10);
    }

    public function test_create_and_edit_forms_are_rendered(): void
    {
        $admin = User::factory()->admin()->create();
        $location = Location::factory()->create(['name' => 'Halte Kampus Timur']);

        $this->actingAs($admin)
            ->get(route('admin.locations.create'))
            ->assertOk()
            ->assertSee('Tambah Titik Lokasi');

        $this->actingAs($admin)
            ->get(route('admin.locations.edit', $location))
            ->assertOk()
            ->assertSee('Halte Kampus Timur');
    }

    public function test_admin_can_create_a_location(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.locations.store'), $this->validPayload())
            ->assertRedirect(route('admin.locations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('locations', [
            'name' => 'Gerbang Utama UNSIKA',
            'category' => 'Gerbang',
            'address_detail' => 'Jl. HS Ronggowaluyo',
        ]);
    }

    public function test_store_requires_name_category_and_coordinates(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.locations.store'), [])
            ->assertSessionHasErrors(['name', 'category', 'latitude', 'longitude']);
    }

    /**
     * @return array<string, array{0: string, 1: mixed}>
     */
    public static function invalidFieldValues(): array
    {
        return [
            'latitude above 90' => ['latitude', 91],
            'latitude below -90' => ['latitude', -91],
            'longitude above 180' => ['longitude', 181],
            'longitude not numeric' => ['longitude', 'timur'],
            'category outside the list' => ['category', 'Mall'],
        ];
    }

    #[DataProvider('invalidFieldValues')]
    public function test_store_rejects_invalid_values(string $field, mixed $value): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.locations.store'), $this->validPayload([$field => $value]))
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('locations', 0);
    }

    public function test_store_rejects_a_duplicate_name(): void
    {
        $admin = User::factory()->admin()->create();
        Location::factory()->create(['name' => 'Gerbang Utama UNSIKA']);

        $this->actingAs($admin)
            ->post(route('admin.locations.store'), $this->validPayload())
            ->assertSessionHasErrors(['name' => 'Nama lokasi sudah digunakan.']);
    }

    public function test_admin_can_update_a_location_keeping_its_own_name(): void
    {
        $admin = User::factory()->admin()->create();
        $location = Location::factory()->create(['name' => 'Gerbang Utama UNSIKA']);

        $this->actingAs($admin)
            ->put(route('admin.locations.update', $location), $this->validPayload(['latitude' => -6.31]))
            ->assertRedirect(route('admin.locations.index'))
            ->assertSessionHas('success');

        $this->assertEquals(-6.31, $location->refresh()->latitude);
    }

    public function test_admin_can_delete_a_location(): void
    {
        $admin = User::factory()->admin()->create();
        $location = Location::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.locations.destroy', $location))
            ->assertRedirect(route('admin.locations.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($location);
    }
}
