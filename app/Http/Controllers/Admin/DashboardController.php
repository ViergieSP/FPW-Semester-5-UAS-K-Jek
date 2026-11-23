<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $students = User::query()->students();

        $ordersToday = Order::query()->whereDate('created_at', today());

        return view('admin.dashboard', [
            'totalStudents' => (clone $students)->where('verification_status', 'approved')->count(),
            'activeDrivers' => (clone $students)
                ->where('role', 'driver')
                ->where('verification_status', 'approved')
                ->where('is_active', true)
                ->count(),
            'pendingVerifications' => (clone $students)->where('verification_status', 'pending')->count(),
            'transactionsToday' => (clone $ordersToday)->count(),
            'completedToday' => (clone $ordersToday)->where('status', 'completed')->count(),
            'latestPending' => (clone $students)
                ->where('verification_status', 'pending')
                ->orderBy('created_at')
                ->limit(5)
                ->get(['id', 'name', 'npm', 'role', 'created_at']),
        ]);
    }
}
