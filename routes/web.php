<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FareSettingController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\UserActivationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserVerificationController;
use App\Http\Controllers\Driver\OrderBoardController;
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
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Pengelolaan akun & antrean verifikasi mahasiswa (W3 / UC-03)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{student}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{student}/approve', [UserVerificationController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{student}/reject', [UserVerificationController::class, 'reject'])->name('users.reject');
    Route::patch('/users/{student}/activation', [UserActivationController::class, 'toggle'])->name('users.activation');

    // Data master (W4)
    Route::resource('fare-settings', FareSettingController::class)->except('show');
    Route::resource('locations', LocationController::class)->except('show');
});

// ==========================================================
// 2. GRUP RUTE DRIVER
// ==========================================================
Route::middleware(['auth', 'role:driver', 'verified.student'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', function () {
        return view('driver.dashboard'); // Nanti diganti Controller
    })->name('dashboard');

    Route::get('/orders', [OrderBoardController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/take', [OrderBoardController::class, 'take'])->name('orders.take');
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

require __DIR__.'/auth.php';
