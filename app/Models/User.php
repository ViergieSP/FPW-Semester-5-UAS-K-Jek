<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'npm',
        'phone_number',
        'role',
        'vehicle_name',
        'license_plate',
        'profile_photo',
        'verification_status',
        'rejection_reason',
        'is_active',
        'password',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Akun mahasiswa (customer/driver), tidak termasuk admin.
     */
    public function scopeStudents(Builder $query): void
    {
        $query->where('role', '!=', 'admin');
    }

    // Helper Cek Role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'approved';
    }

    public function hasVehicleDetails(): bool
    {
        return filled($this->vehicle_name) && filled($this->license_plate);
    }

    public static function normalizeLicensePlate(string $licensePlate): string
    {
        $squished = Str::upper(Str::squish($licensePlate));

        return (string) preg_replace(
            '/^([A-Z]{1,2})\s*(\d{1,4})\s*([A-Z]{1,3})$/',
            '$1 $2 $3',
            $squished
        );
    }

    public function ordersAsCustomer(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function ordersAsDriver(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function hasActiveOrder(): bool
    {
        if (! $this->isDriver()) {
            return false;
        }

        return $this->ordersAsDriver()
            ->whereIn('status', ['taken', 'on_the_way'])
            ->exists();
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'driver_id');
    }

    public function getProfileUrlAttribute(): ?string
    {
        if (! $this->profile_photo) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        return $disk->url($this->profile_photo);
    }
}
