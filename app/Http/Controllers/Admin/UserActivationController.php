<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserActivationController extends Controller
{
    /**
     * Mengaktifkan atau menonaktifkan akun mahasiswa.
     */
    public function toggle(User $student): RedirectResponse
    {
        $student->update(['is_active' => ! $student->is_active]);

        $message = $student->is_active
            ? "Akun {$student->name} berhasil diaktifkan."
            : "Akun {$student->name} berhasil dinonaktifkan.";

        return back()->with('success', $message);
    }
}
