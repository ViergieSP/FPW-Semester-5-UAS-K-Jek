<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'name' => 'Haris Mahasiswa',
            'email' => 'haris@k-jek.id',
            'npm' => '552313333549',
            'phone_number' => '081234567890',
            'role' => 'driver',
            'profile_photo' => UploadedFile::fake()->image('ktm.jpg'),
            'password' => 'password-kuat-123',
            'password_confirmation' => 'password-kuat-123',
        ];
    }

    public function test_registration_stores_the_photo_and_creates_a_pending_account(): void
    {
        Storage::fake('s3');

        $this->post(route('register'), $this->payload())
            ->assertRedirect(route('dashboard', absolute: false));

        $user = User::query()->where('email', 'haris@k-jek.id')->firstOrFail();

        $this->assertSame('pending', $user->verification_status);
        $this->assertTrue($user->is_active);
        $this->assertNotNull($user->profile_photo);
        Storage::disk('s3')->assertExists($user->profile_photo);
    }

    public function test_registration_fails_when_the_upload_cannot_be_stored(): void
    {
        // Disk dengan root yang tidak bisa ditulis meniru S3 yang belum dikonfigurasi (store() => false).
        config(['filesystems.disks.s3' => [
            'driver' => 'local',
            'root' => storage_path('app/private/'.str_repeat('x', 300)),
            'throw' => false,
        ]]);

        $this->post(route('register'), $this->payload())
            ->assertSessionHasErrors('profile_photo');

        $this->assertDatabaseMissing('users', ['email' => 'haris@k-jek.id']);
        $this->assertGuest();
    }
}
