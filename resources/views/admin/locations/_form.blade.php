<form method="POST" action="{{ $action }}" class="flex flex-col gap-5">
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <x-input-label for="name" value="Nama lokasi" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required
                :value="old('name', $location->name)" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="category" value="Kategori" />
            <select id="category" name="category" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach (\App\Models\Location::CATEGORIES as $option)
                    <option value="{{ $option }}" @selected(old('category', $location->category) === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <x-input-label for="latitude" value="Latitude" />
            <x-text-input id="latitude" name="latitude" type="number" step="any" min="-90" max="90" class="mt-1 block w-full" required
                placeholder="-6.30000000" :value="old('latitude', $location->latitude)" />
            <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="longitude" value="Longitude" />
            <x-text-input id="longitude" name="longitude" type="number" step="any" min="-180" max="180" class="mt-1 block w-full" required
                placeholder="107.30000000" :value="old('longitude', $location->longitude)" />
            <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="address_detail" value="Detail alamat (opsional)" />
        <textarea id="address_detail" name="address_detail" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address_detail', $location->address_detail) }}</textarea>
        <x-input-error :messages="$errors->get('address_detail')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('admin.locations.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</form>
