<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();

            // Relasi ke User
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');

            // Data Titik Jemput
            $table->string('pickup_address');
            $table->decimal('pickup_latitude', 10, 8);
            $table->decimal('pickup_longitude', 11, 8);

            // Data Titik Tujuan
            $table->string('destination_address');
            $table->decimal('destination_latitude', 10, 8);
            $table->decimal('destination_longitude', 11, 8);

            // Perhitungan Jarak & Tarif
            $table->decimal('distance_km', 5, 2);
            $table->decimal('total_fare', 10, 2);
            $table->text('notes')->nullable();

            // State Machine Flow (W6)
            $table->enum('status', ['open', 'taken', 'on_the_way', 'completed', 'cancelled'])->default('open');
            $table->text('cancellation_reason')->nullable();

            // Tracking waktu alur
            $table->timestamp('taken_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};