<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $driver = auth()->user();

        $activeOrder = Order::query()
            ->with('customer:id,name,phone_number')
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['taken', 'on_the_way'])
            ->latest('taken_at')
            ->first();

        $completedToday = Order::query()
            ->where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereDate('completed_at', today());

        return view('driver.dashboard', [
            'activeOrder' => $activeOrder,
            'completedToday' => (clone $completedToday)->count(),
            'earningsToday' => (clone $completedToday)->sum('total_fare'),
        ]);
    }
}
