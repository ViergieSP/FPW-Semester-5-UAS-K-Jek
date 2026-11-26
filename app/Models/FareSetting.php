<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FareSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_name',
        'base_fare',
        'fare_per_km',
        'min_distance_km',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_fare' => 'decimal:2',
            'fare_per_km' => 'decimal:2',
            'min_distance_km' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Scope untuk mengambil tarif yang sedang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->first();
    }
}
