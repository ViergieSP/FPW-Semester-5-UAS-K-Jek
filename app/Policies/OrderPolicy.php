<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function take(User $user, Order $order): bool
    {
        return $user->isDriver()
            && $user->isVerified()
            && $order->status === 'open'
            && $order->driver_id === null;
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->isDriver()
            && $user->isVerified()
            && $order->driver_id === $user->id;
    }
}
