<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationRequest;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'in:'.implode(',', Location::CATEGORIES)],
        ]);
        $search = $filters['search'] ?? null;
        $category = $filters['category'] ?? null;

        $locations = Location::query()
            ->when($search !== null, function (Builder $query) use ($search): void {
                $keyword = '%'.Str::lower($search).'%';
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->whereRaw('LOWER(name) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(address_detail) LIKE ?', [$keyword]);
                });
            })
            ->when($category !== null, function (Builder $query) use ($category): void {
                $query->where('category', $category);
            })
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.locations.index', [
            'locations' => $locations,
            'search' => $search,
            'category' => $category,
        ]);
    }

    public function create(): View
    {
        return view('admin.locations.create', [
            'location' => new Location(['category' => 'Kampus']),
        ]);
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        Location::create($request->validated());

        return to_route('admin.locations.index')
            ->with('success', 'Titik lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', [
            'location' => $location,
        ]);
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return to_route('admin.locations.index')
            ->with('success', 'Titik lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return to_route('admin.locations.index')
            ->with('success', 'Titik lokasi berhasil dihapus.');
    }
}
