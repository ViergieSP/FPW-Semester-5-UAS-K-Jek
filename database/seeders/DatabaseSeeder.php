<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FareSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name' => 'Admin K-JEK',
            'email' => 'admin@kjek.id',
            'npm' => '2410631170001',
            'phone_number' => '081234567890',
            'role' => 'admin',
            'verification_status' => 'approved',
            'password' => Hash::make('password123'),
        ]);

        // 2. Akun Driver
        User::create([
            'name' => 'Budi Driver Mahasiswa',
            'email' => 'driver@kjek.id',
            'npm' => '2410631170099',
            'phone_number' => '089876543210',
            'role' => 'driver',
            'verification_status' => 'approved',
            'password' => Hash::make('password123'),
        ]);

        // 3. Akun Customer
        User::create([
            'name' => 'Siti Customer Mahasiswi',
            'email' => 'customer@kjek.id',
            'npm' => '2410631170088',
            'phone_number' => '085678901234',
            'role' => 'customer',
            'verification_status' => 'approved',
            'password' => Hash::make('password123'),
        ]);

        // 4. Default Setting Tarif Master
        FareSetting::create([
            'setting_name' => 'Tarif Dasar Kampus Unsika',
            'base_fare' => 5000,
            'fare_per_km' => 3500,
            'min_distance_km' => 1.0,
            'is_active' => true,
        ]);
    }
}