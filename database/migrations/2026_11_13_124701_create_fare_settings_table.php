<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fare_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_name')->default('Tarif Standar Mahasiswa');
            $table->decimal('base_fare', 10, 2)->default(5000);
            $table->decimal('fare_per_km', 10, 2)->default(3500);
            $table->decimal('min_distance_km', 4, 2)->default(1.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fare_settings');
    }
};