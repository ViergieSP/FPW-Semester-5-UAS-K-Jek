<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Board Pesanan Terbuka
            </h2>
            <p class="text-sm text-gray-500">Temukan pesanan dan ambil perjalanan yang tersedia.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
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

            <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <form method="GET" action="{{ route('driver.orders.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700">Pencarian kata kunci</label>
                        <input
                            id="search"
                            name="search"
                            type="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Kode pesanan, alamat, atau catatan"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label for="pickup" class="block text-sm font-medium text-gray-700">Titik jemput</label>
                        <input
                            id="pickup"
                            name="pickup"
                            type="text"
                            value="{{ $filters['pickup'] ?? '' }}"
                            placeholder="Cari titik jemput"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label for="destination" class="block text-sm font-medium text-gray-700">Tujuan</label>
                        <input
                            id="destination"
                            name="destination"
                            type="text"
                            value="{{ $filters['destination'] ?? '' }}"
                            placeholder="Cari tujuan"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div class="flex flex-wrap items-center gap-3 md:col-span-4">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Cari pesanan
                        </button>
                        <a href="{{ route('driver.orders.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Reset filter
                        </a>
                    </div>
                </form>
            </section>

            <section class="flex flex-col gap-4" aria-label="Daftar pesanan terbuka">
                @forelse ($orders as $order)
                    <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex flex-col gap-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $order->order_code }}</h3>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-800">
                                        Open
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>

                                <div class="grid gap-3 text-sm sm:grid-cols-2">
                                    <div>
                                        <p class="font-medium text-gray-500">Titik jemput</p>
                                        <p class="text-gray-900">{{ $order->pickup_address }}</p>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-500">Tujuan</p>
                                        <p class="text-gray-900">{{ $order->destination_address }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600">
                                    <span>Pemesan: {{ $order->customer->name }}</span>
                                    <span>Jarak: {{ number_format((float) $order->distance_km, 2, ',', '.') }} km</span>
                                    <span>Tarif: Rp{{ number_format((float) $order->total_fare, 0, ',', '.') }}</span>
                                </div>

                                @if ($order->notes)
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Catatan:</span> {{ $order->notes }}
                                    </p>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('driver.orders.take', $order) }}" class="shrink-0">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                                >
                                    Ambil Pesanan
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                        <h3 class="text-base font-semibold text-gray-900">Tidak ada pesanan yang cocok</h3>
                        <p class="mt-2 text-sm text-gray-600">Coba ubah kata kunci atau filter rute.</p>
                    </div>
                @endforelse
            </section>

            @if ($orders->hasPages())
                <div>{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
