<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserVerificationController extends Controller
{
    public function approve(User $student): RedirectResponse
    {
        if ($student->verification_status === 'approved') {
            return back()->with('error', "Akun {$student->name} sudah disetujui sebelumnya.");
        }

        $student->update([
            'verification_status' => 'approved',
            'rejection_reason' => null,
        ]);

        return to_route('admin.users.index')
            ->with('success', "Akun {$student->name} berhasil disetujui.");
    }

    public function reject(RejectUserRequest $request, User $student): RedirectResponse
    {
        if ($student->verification_status === 'rejected') {
            return back()->with('error', "Akun {$student->name} sudah ditolak sebelumnya.");
        }

        $student->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->validated('rejection_reason'),
        ]);

        return to_route('admin.users.index')
            ->with('success', "Akun {$student->name} telah ditolak.");
    }
}
