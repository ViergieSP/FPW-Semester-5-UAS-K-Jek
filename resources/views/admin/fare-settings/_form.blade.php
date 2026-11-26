<form method="POST" action="{{ $action }}" class="flex flex-col gap-5">
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div>
        <x-input-label for="setting_name" value="Nama tarif" />
        <x-text-input id="setting_name" name="setting_name" type="text" class="mt-1 block w-full" required
            :value="old('setting_name', $fareSetting->setting_name)" />
        <x-input-error :messages="$errors->get('setting_name')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div>
            <x-input-label for="base_fare" value="Tarif dasar (Rp)" />
            <x-text-input id="base_fare" name="base_fare" type="number" step="0.01" min="0" class="mt-1 block w-full" required
                :value="old('base_fare', $fareSetting->base_fare)" />
            <x-input-error :messages="$errors->get('base_fare')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="fare_per_km" value="Tarif per km (Rp)" />
            <x-text-input id="fare_per_km" name="fare_per_km" type="number" step="0.01" min="0" class="mt-1 block w-full" required
                :value="old('fare_per_km', $fareSetting->fare_per_km)" />
            <x-input-error :messages="$errors->get('fare_per_km')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="min_distance_km" value="Jarak minimum (km)" />
            <x-text-input id="min_distance_km" name="min_distance_km" type="number" step="0.01" min="0" class="mt-1 block w-full" required
                :value="old('min_distance_km', $fareSetting->min_distance_km)" />
            <x-input-error :messages="$errors->get('min_distance_km')" class="mt-2" />
        </div>
    </div>

    <div>
        <label for="is_active" class="inline-flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $fareSetting->is_active))
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Jadikan tarif aktif (tarif aktif lainnya akan dinonaktifkan)</span>
        </label>
        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('admin.fare-settings.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</form>
