<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard Admin K-JEK
            </h2>
            <p class="text-sm text-gray-500">
                Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan komunitas K-JEK hari ini.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <section aria-label="Ringkasan" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200" data-counter="total-students">
                    <p class="text-sm font-medium text-gray-500">Mahasiswa Terdaftar</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalStudents, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Akun customer &amp; driver yang sudah terverifikasi</p>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200" data-counter="active-drivers">
                    <p class="text-sm font-medium text-gray-500">Driver Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($activeDrivers, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Driver terverifikasi dengan akun aktif</p>
                </div>

                <a href="{{ route('admin.users.index', ['status' => 'pending']) }}"
                    class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 transition hover:ring-indigo-300"
                    data-counter="pending-verifications">
                    <p class="text-sm font-medium text-gray-500">Antrean Verifikasi</p>
                    <p class="mt-2 text-3xl font-bold {{ $pendingVerifications > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                        {{ number_format($pendingVerifications, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-indigo-600">Tinjau pendaftar &rarr;</p>
                </a>

                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200" data-counter="transactions-today">
                    <p class="text-sm font-medium text-gray-500">Transaksi Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($transactionsToday, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ number_format($completedToday, 0, ',', '.') }} selesai</p>
                </div>
            </section>

            <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200" aria-label="Pendaftar menunggu verifikasi">
                <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Pendaftar Menunggu Verifikasi</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Lihat semua
                    </a>
                </div>

                <ul class="divide-y divide-gray-100">
                    @forelse ($latestPending as $pendingUser)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $pendingUser->name }}</p>
                                <p class="text-xs text-gray-500">
                                    NPM {{ $pendingUser->npm }} &middot; {{ ucfirst($pendingUser->role) }} &middot;
                                    mendaftar {{ $pendingUser->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <a href="{{ route('admin.users.show', $pendingUser) }}"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                Tinjau
                            </a>
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center text-sm text-gray-500">
                            Tidak ada antrean verifikasi saat ini.
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-app-layout>
