<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    /**
     * Kategori titik lokasi / zona kampus yang tersedia.
     *
     * @var list<string>
     */
    public const CATEGORIES = [
        'Kampus',
        'Fakultas',
        'Gerbang',
        'Halte',
        'Kos / Kontrakan',
        'Lainnya',
    ];

    protected $fillable = [
        'name',
        'category',
        'latitude',
        'longitude',
        'address_detail',
    ];
}
