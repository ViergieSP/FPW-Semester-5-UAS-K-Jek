<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderBoardController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'pickup' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
        ]);
        $search = $filters['search'] ?? null;
        $pickup = $filters['pickup'] ?? null;
        $destination = $filters['destination'] ?? null;

        $orders = Order::query()
            ->with('customer:id,name')
            ->where('status', 'open')
            ->whereNull('driver_id')
            ->when($search !== null, function (Builder $query) use ($search): void {
                $keyword = '%'.Str::lower((string) $search).'%';
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->whereRaw('LOWER(order_code) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(pickup_address) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(destination_address) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(notes) LIKE ?', [$keyword]);
                });
            })
            ->when($pickup !== null, function (Builder $query) use ($pickup): void {
                $query->whereRaw('LOWER(pickup_address) LIKE ?', ['%'.Str::lower((string) $pickup).'%']);
            })
            ->when($destination !== null, function (Builder $query) use ($destination): void {
                $query->whereRaw('LOWER(destination_address) LIKE ?', ['%'.Str::lower((string) $destination).'%']);
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('driver.orders.index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    public function take(Request $request, Order $order): RedirectResponse
    {
        $driver = $request->user();

        if (! $driver instanceof User) {
            abort(403);
        }

        $wasTaken = DB::transaction(function () use ($driver, $order): bool {
            $availableOrder = Order::query()
                ->whereKey($order->getKey())
                ->where('status', 'open')
                ->whereNull('driver_id')
                ->lockForUpdate()
                ->first();

            if ($availableOrder === null) {
                return false;
            }

            $availableOrder->driver()->associate($driver);
            $availableOrder->status = 'taken';
            $availableOrder->taken_at = now();
            $availableOrder->save();

            return true;
        });

        if (! $wasTaken) {
            return to_route('driver.orders.index')
                ->with('error', 'Pesanan ini sudah diambil atau tidak lagi tersedia.');
        }

        return to_route('driver.orders.index')
            ->with('success', 'Pesanan berhasil diambil.');
    }
}
