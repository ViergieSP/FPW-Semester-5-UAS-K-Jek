<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'npm',
        'phone_number',
        'role',
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

    // Relasi Transaksi
    public function ordersAsCustomer(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function ordersAsDriver(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
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

        return Storage::disk('s3')->url($this->profile_photo);
    }
}
