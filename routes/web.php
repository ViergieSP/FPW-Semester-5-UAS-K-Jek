<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect Dashboard Otomatis Berdasarkan Peran
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'driver' => redirect()->route('driver.dashboard'),
        'customer' => redirect()->route('customer.dashboard'),
        default => abort(403),
    };
})->middleware(['auth'])->name('dashboard');

// Halaman Status Menunggu Verifikasi
Route::get('/verification-status', function () {
    if (auth()->user()->verification_status === 'approved' || auth()->user()->role === 'admin') {
        return redirect()->route('dashboard');
    }
    return view('auth.student-verification-status');
})->middleware('auth')->name('verification.notice.student');


// ==========================================================
// 1. GRUP RUTE ADMIN
// ==========================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Nanti diganti Controller
    })->name('dashboard');
});


// ==========================================================
// 2. GRUP RUTE DRIVER
// ==========================================================
Route::middleware(['auth', 'role:driver', 'verified.student'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', function () {
        return view('driver.dashboard'); // Nanti diganti Controller
    })->name('dashboard');
});


// ==========================================================
// 3. GRUP RUTE CUSTOMER
// ==========================================================
Route::middleware(['auth', 'role:customer', 'verified.student'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', function () {
        return view('customer.dashboard'); // Nanti diganti Controller
    })->name('dashboard');
});


// ==========================================================
// RUTE PROFIL & UBAH KATA SANDI
// ==========================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';