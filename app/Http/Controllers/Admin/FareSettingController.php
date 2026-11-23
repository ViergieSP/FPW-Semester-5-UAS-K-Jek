<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FareSettingRequest;
use App\Models\FareSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FareSettingController extends Controller
{
    public function index(): View
    {
        $fareSettings = FareSetting::query()
            ->orderByDesc('is_active')
            ->orderBy('setting_name')
            ->paginate(10);

        return view('admin.fare-settings.index', [
            'fareSettings' => $fareSettings,
            'hasActiveFare' => FareSetting::query()->where('is_active', true)->exists(),
        ]);
    }

    public function create(): View
    {
        return view('admin.fare-settings.create', [
            'fareSetting' => new FareSetting([
                'base_fare' => 5000,
                'fare_per_km' => 3500,
                'min_distance_km' => 1,
                'is_active' => ! FareSetting::query()->where('is_active', true)->exists(),
            ]),
        ]);
    }

    public function store(FareSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            $fareSetting = FareSetting::create($data);

            if ($fareSetting->is_active) {
                $this->deactivateOthers($fareSetting);
            }
        });

        return to_route('admin.fare-settings.index')
            ->with('success', 'Tarif baru berhasil ditambahkan.');
    }

    public function edit(FareSetting $fareSetting): View
    {
        return view('admin.fare-settings.edit', [
            'fareSetting' => $fareSetting,
        ]);
    }

    public function update(FareSettingRequest $request, FareSetting $fareSetting): RedirectResponse
    {
        $data = $request->validated();

        if ($fareSetting->is_active && ! $data['is_active']) {
            return back()
                ->withInput()
                ->withErrors(['is_active' => 'Tarif aktif tidak dapat dinonaktifkan langsung. Aktifkan tarif lain terlebih dahulu.']);
        }

        DB::transaction(function () use ($fareSetting, $data): void {
            $fareSetting->update($data);

            if ($fareSetting->is_active) {
                $this->deactivateOthers($fareSetting);
            }
        });

        return to_route('admin.fare-settings.index')
            ->with('success', 'Tarif berhasil diperbarui.');
    }

    public function destroy(FareSetting $fareSetting): RedirectResponse
    {
        if ($fareSetting->is_active) {
            return to_route('admin.fare-settings.index')
                ->with('error', 'Tarif yang sedang aktif tidak dapat dihapus. Aktifkan tarif lain terlebih dahulu.');
        }

        $fareSetting->delete();

        return to_route('admin.fare-settings.index')
            ->with('success', 'Tarif berhasil dihapus.');
    }

    /**
     * Hanya satu tarif yang boleh aktif pada satu waktu.
     */
    private function deactivateOthers(FareSetting $activeFare): void
    {
        FareSetting::query()
            ->whereKeyNot($activeFare->getKey())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
