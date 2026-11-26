<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Driver K-JEK
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                Selamat datang, {{ auth()->user()->name }}! Anda login sebagai
                <strong>{{ strtoupper(auth()->user()->role) }}</strong>.
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Pesanan Selesai Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $completedToday }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Estimasi Pendapatan Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">
                        Rp{{ number_format((float) $earningsToday, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            @if ($activeOrder)
                <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $activeOrder->order_code }}</h3>
                                <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide
                                    {{ $activeOrder->status === 'taken' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $activeOrder->status === 'taken' ? 'Diambil' : 'Dalam Perjalanan' }}
                                </span>
                            </div>

                            <div class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                                <div>
                                    <p class="font-medium text-gray-500">Pemesan</p>
                                    <p class="text-gray-900">{{ $activeOrder->customer->name }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Tarif</p>
                                    <p class="text-gray-900">Rp{{ number_format((float) $activeOrder->total_fare, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Titik Jemput</p>
                                    <p class="text-gray-900">{{ $activeOrder->pickup_address }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Tujuan</p>
                                    <p class="text-gray-900">{{ $activeOrder->destination_address }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col gap-3 sm:w-44">
                            <a href="{{ $activeOrder->customer_wa_link }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                                Hubungi via WhatsApp
                            </a>

                            @if ($activeOrder->status === 'taken')
                                <form method="POST" action="{{ route('driver.orders.on-the-way', $activeOrder) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                        Mulai Perjalanan
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('driver.orders.complete', $activeOrder) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                                        Selesaikan Pesanan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </section>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center">
                    <h3 class="text-base font-semibold text-gray-900">Tidak ada pesanan aktif</h3>
                    <p class="mt-2 text-sm text-gray-600">Ambil pesanan terbuka untuk memulai perjalanan.</p>
                    <a href="{{ route('driver.orders.index') }}"
                        class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Buka Board Pesanan
                    </a>
                </div>
            @endif

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