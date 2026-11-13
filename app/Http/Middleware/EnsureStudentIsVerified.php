<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role !== 'admin' && $user->verification_status !== 'approved') {
            return redirect()->route('verification.notice.student');
        }

        return $next($request);
    }
}