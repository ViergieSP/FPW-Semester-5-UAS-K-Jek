<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'npm' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'min:10', 'max:15'],
            'role' => ['required', 'in:customer,driver'],
            'profile_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Simpan foto ke storage/app/public/profile_photos
        $profilePath = $request->file('profile_photo')->store('profile_photos', 's3');

        // store() mengembalikan false (bukan exception) jika storage S3 gagal/tidak terkonfigurasi.
        if ($profilePath === false) {
            Log::error('Upload berkas verifikasi ke disk s3 gagal. Periksa AWS_* di .env.');

            return back()
                ->withInput($request->except('password', 'password_confirmation', 'profile_photo'))
                ->withErrors(['profile_photo' => 'Berkas gagal diunggah. Silakan coba lagi atau hubungi admin K-JEK.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'npm' => $request->npm,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'profile_photo' => $profilePath,
            'verification_status' => 'pending',
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
