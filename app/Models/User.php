<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'npm',
        'phone_number',
        'role',
        'ktm_photo',
        'verification_status',
        'rejection_reason',
        'password'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
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
}