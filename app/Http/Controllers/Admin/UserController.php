<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(UserIndexRequest $request): View
    {
        $filters = $request->validated();
        $status = $request->statusFilter();
        $role = $filters['role'] ?? null;
        $search = $filters['search'] ?? null;

        $users = User::query()
            ->students()
            ->when($status !== 'all', function (Builder $query) use ($status): void {
                $query->where('verification_status', $status);
            })
            ->when($role !== null, function (Builder $query) use ($role): void {
                $query->where('role', $role);
            })
            ->when($search !== null, function (Builder $query) use ($search): void {
                $keyword = '%'.Str::lower($search).'%';
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->whereRaw('LOWER(name) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(npm) LIKE ?', [$keyword]);
                });
            })
            // Antrean verifikasi diproses dari pendaftar terlama (FIFO).
            ->when(
                $status === 'pending',
                fn (Builder $query) => $query->orderBy('created_at')->orderBy('id'),
                fn (Builder $query) => $query->orderByDesc('created_at')->orderByDesc('id'),
            )
            ->paginate(10)
            ->withQueryString();

        $statusCounts = User::query()
            ->students()
            ->selectRaw('verification_status, COUNT(*) as total')
            ->groupBy('verification_status')
            ->pluck('total', 'verification_status');

        return view('admin.users.index', [
            'users' => $users,
            'status' => $status,
            'role' => $role,
            'search' => $search,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function show(User $student): View
    {
        return view('admin.users.show', [
            'student' => $student,
        ]);
    }
}
