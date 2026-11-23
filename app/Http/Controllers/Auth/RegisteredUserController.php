<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'npm' => ['required', 'string', 'max:20', 'unique:' . User::class],
            'phone_number' => ['required', 'string', 'min:10', 'max:15'],
            'role' => ['required', 'in:customer,driver'],
            'vehicle_name' => ['exclude_unless:role,driver', 'required', 'string', 'min:3', 'max:100'],
            'license_plate' => [
                'exclude_unless:role,driver',
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z]{1,2}\s*\d{1,4}\s*[A-Za-z]{1,3}$/',
            ],
            'profile_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'vehicle_name.required' => 'Nama kendaraan wajib diisi untuk pendaftaran driver.',
            'license_plate.required' => 'Plat nomor kendaraan wajib diisi untuk pendaftaran driver.',
            'license_plate.regex' => 'Format plat nomor tidak valid (contoh: T 1234 AB).',
        ]);

        $profilePath = $request->file('profile_photo')->store('profile_photos', 's3');
        $isDriver = $validated['role'] === 'driver';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'npm' => $validated['npm'],
            'phone_number' => $validated['phone_number'],
            'role' => $validated['role'],
            'vehicle_name' => $isDriver ? Str::squish($validated['vehicle_name']) : null,
            'license_plate' => $isDriver ? User::normalizeLicensePlate($validated['license_plate']) : null,
            'profile_photo' => $profilePath,
            'verification_status' => 'pending',
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}