<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Tarif</h2>
                <p class="text-sm text-gray-500">Atur skema tarif dasar dan tarif per kilometer. Hanya satu tarif yang dapat aktif.</p>
            </div>
            <a href="{{ route('admin.fare-settings.create') }}"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Tambah Tarif
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            @unless ($hasActiveFare)
                <div role="alert" class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    Belum ada tarif yang aktif. Aktifkan salah satu tarif agar perhitungan biaya perjalanan dapat berjalan.
                </div>
            @endunless

            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th scope="col" class="px-4 py-3">Nama Tarif</th>
                                <th scope="col" class="px-4 py-3">Tarif Dasar</th>
                                <th scope="col" class="px-4 py-3">Tarif per Km</th>
                                <th scope="col" class="px-4 py-3">Jarak Minimum</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                                <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($fareSettings as $fareSetting)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $fareSetting->setting_name }}</td>
                                    <td class="px-4 py-3 text-gray-700">Rp{{ number_format((float) $fareSetting->base_fare, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-700">Rp{{ number_format((float) $fareSetting->fare_per_km, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ number_format((float) $fareSetting->min_distance_km, 2, ',', '.') }} km</td>
                                    <td class="px-4 py-3">
                                        @if ($fareSetting->is_active)
                                            <x-status-badge color="green">Aktif</x-status-badge>
                                        @else
                                            <x-status-badge color="gray">Nonaktif</x-status-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.fare-settings.edit', $fareSetting) }}"
                                                class="font-medium text-indigo-600 hover:text-indigo-800">Ubah</a>

                                            <form method="POST" action="{{ route('admin.fare-settings.destroy', $fareSetting) }}"
                                                onsubmit="return confirm('Hapus tarif ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 hover:text-red-800">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">Belum ada data tarif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($fareSettings->hasPages())
                <div>{{ $fareSettings->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
