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
        'is_active'
    ];

    // Scope untuk mengambil tarif yang sedang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->first();
    }
}