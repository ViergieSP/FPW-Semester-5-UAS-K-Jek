<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Lokasi</h2>
                <p class="text-sm text-gray-500">Kelola titik lokasi dan zona kampus yang menjadi acuan penjemputan &amp; tujuan.</p>
            </div>
            <a href="{{ route('admin.locations.create') }}"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Tambah Lokasi
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <form method="GET" action="{{ route('admin.locations.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Cari nama atau alamat" />
                        <x-text-input id="search" name="search" type="search" class="mt-1 block w-full" :value="$search" />
                    </div>

                    <div>
                        <x-input-label for="category" value="Kategori" />
                        <select id="category" name="category"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua</option>
                            @foreach (\App\Models\Location::CATEGORIES as $option)
                                <option value="{{ $option }}" @selected($category === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <x-primary-button>Cari</x-primary-button>
                        <a href="{{ route('admin.locations.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Reset</a>
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th scope="col" class="px-4 py-3">Nama</th>
                                <th scope="col" class="px-4 py-3">Kategori</th>
                                <th scope="col" class="px-4 py-3">Koordinat</th>
                                <th scope="col" class="px-4 py-3">Detail Alamat</th>
                                <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($locations as $location)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $location->name }}</td>
                                    <td class="px-4 py-3"><x-status-badge color="indigo">{{ $location->category }}</x-status-badge></td>
                                    <td class="px-4 py-3 text-gray-700">{{ $location->latitude }}, {{ $location->longitude }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $location->address_detail ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.locations.edit', $location) }}"
                                                class="font-medium text-indigo-600 hover:text-indigo-800">Ubah</a>

                                            <form method="POST" action="{{ route('admin.locations.destroy', $location) }}"
                                                onsubmit="return confirm('Hapus titik lokasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 hover:text-red-800">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                        Tidak ada titik lokasi yang cocok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($locations->hasPages())
                <div>{{ $locations->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
