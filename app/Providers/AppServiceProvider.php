<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pengelolaan akun oleh admin hanya berlaku untuk mahasiswa (customer/driver).
        Route::bind('student', function (string $value): User {
            return User::query()->students()->findOrFail($value);
        });
    }
}
