@php
    $tabs = [
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'all' => 'Semua',
    ];
    $tabCounts = [
        'pending' => (int) ($statusCounts['pending'] ?? 0),
        'approved' => (int) ($statusCounts['approved'] ?? 0),
        'rejected' => (int) ($statusCounts['rejected'] ?? 0),
        'all' => (int) $statusCounts->sum(),
    ];
    $verificationBadges = [
        'pending' => ['yellow', 'Menunggu'],
        'approved' => ['green', 'Disetujui'],
        'rejected' => ['red', 'Ditolak'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Verifikasi &amp; Pengguna</h2>
            <p class="text-sm text-gray-500">Tinjau berkas pendaftar, setujui atau tolak, dan kelola status akun mahasiswa.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <nav class="flex flex-wrap gap-2" aria-label="Filter status verifikasi">
                @foreach ($tabs as $tabKey => $tabLabel)
                    <a href="{{ route('admin.users.index', array_filter(['status' => $tabKey, 'role' => $role, 'search' => $search])) }}"
                        @class([
                            'inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium',
                            'bg-indigo-600 text-white' => $status === $tabKey,
                            'bg-white text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50' => $status !== $tabKey,
                        ])
                        @if ($status === $tabKey) aria-current="page" @endif>
                        {{ $tabLabel }}
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs',
                            'bg-indigo-500 text-white' => $status === $tabKey,
                            'bg-gray-100 text-gray-600' => $status !== $tabKey,
                        ])>{{ $tabCounts[$tabKey] }}</span>
                    </a>
                @endforeach
            </nav>

            <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <input type="hidden" name="status" value="{{ $status }}">

                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Cari nama, email, atau NPM" />
                        <x-text-input id="search" name="search" type="search" class="mt-1 block w-full" :value="$search" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Peran" />
                        <select id="role" name="role"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua</option>
                            <option value="customer" @selected($role === 'customer')>Customer</option>
                            <option value="driver" @selected($role === 'driver')>Driver</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <x-primary-button>Cari</x-primary-button>
                        <a href="{{ route('admin.users.index', ['status' => $status]) }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Reset</a>
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th scope="col" class="px-4 py-3">Mahasiswa</th>
                                <th scope="col" class="px-4 py-3">NPM</th>
                                <th scope="col" class="px-4 py-3">Peran</th>
                                <th scope="col" class="px-4 py-3">Verifikasi</th>
                                <th scope="col" class="px-4 py-3">Akun</th>
                                <th scope="col" class="px-4 py-3">Mendaftar</th>
                                <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                @php([$badgeColor, $badgeLabel] = $verificationBadges[$user->verification_status])
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $user->npm }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ ucfirst($user->role) }}</td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :color="$badgeColor">{{ $badgeLabel }}</x-status-badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($user->is_active)
                                            <x-status-badge color="green">Aktif</x-status-badge>
                                        @else
                                            <x-status-badge color="gray">Nonaktif</x-status-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.users.show', $user) }}"
                                                class="font-medium text-indigo-600 hover:text-indigo-800">Tinjau</a>

                                            <form method="POST" action="{{ route('admin.users.activation', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="font-medium {{ $user->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                        Tidak ada pengguna yang cocok dengan filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($users->hasPages())
                <div>{{ $users->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
