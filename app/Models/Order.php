<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'driver_id',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'destination_address',
        'destination_latitude',
        'destination_longitude',
        'distance_km',
        'total_fare',
        'notes',
        'status',
        'cancellation_reason',
        'taken_at',
        'completed_at'
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'total_fare' => 'decimal:2',
        'taken_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relasi Eloquent
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // Helper Generator Link WhatsApp Langsung
    public function getCustomerWaLinkAttribute(): string
    {
        $phone = preg_replace('/^0/', '62', $this->customer->phone_number);
        $message = urlencode("Halo {$this->customer->name}, saya driver K-JEK untuk pesanan {$this->order_code}. Posisi saya sedang menuju titik jemput.");
        return "https://wa.me/{$phone}?text={$message}";
    }
}