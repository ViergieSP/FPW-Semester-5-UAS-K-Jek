<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Driver K-JEK
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                Selamat datang, {{ auth()->user()->name }}! Anda login sebagai
                <strong>{{ strtoupper(auth()->user()->role) }}</strong>.
            </div>

            @if (auth()->user()->hasVehicleDetails())
                <div
                    class="flex flex-col justify-between gap-4 overflow-hidden bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:rounded-lg">
                    <div class="flex flex-col gap-1">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                            Kendaraan Terdaftar
                        </h3>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ auth()->user()->vehicle_name }}
                            <span
                                class="ms-2 inline-flex items-center rounded-md bg-amber-100 px-2.5 py-0.5 text-sm font-bold text-amber-800">
                                {{ auth()->user()->license_plate }}
                            </span>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Ubah Data Kendaraan
                        </a>
                        <a href="{{ route('driver.orders.index') }}"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Buka Board Pesanan
                        </a>
                    </div>
                </div>
            @else
                <div role="alert"
                    class="flex flex-col justify-between gap-4 rounded-lg border border-amber-200 bg-amber-50 p-6 text-amber-900 sm:flex-row sm:items-center">
                    <div>
                        <h3 class="font-semibold">Data Kendaraan Belum Lengkap</h3>
                        <p class="mt-1 text-sm text-amber-800">
                            Lengkapi nama kendaraan dan plat nomor pada profil Anda agar dapat mengambil pesanan.
                        </p>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        class="inline-flex shrink-0 items-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                        Lengkapi Profil
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>