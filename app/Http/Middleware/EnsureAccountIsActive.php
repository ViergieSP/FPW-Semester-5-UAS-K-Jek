<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public const DEACTIVATED_MESSAGE = 'Akun Anda telah dinonaktifkan oleh pengurus komunitas. Silakan hubungi admin K-JEK.';

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => self::DEACTIVATED_MESSAGE]);
        }

        return $next($request);
    }
}
