@php
    $verificationBadges = [
        'pending' => ['yellow', 'Menunggu verifikasi'],
        'approved' => ['green', 'Disetujui'],
        'rejected' => ['red', 'Ditolak'],
    ];
    [$badgeColor, $badgeLabel] = $verificationBadges[$student->verification_status];
    $isPdf = $student->profile_photo && str_ends_with(strtolower($student->profile_photo), '.pdf');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tinjau Pendaftar</h2>
                <p class="text-sm text-gray-500">Periksa berkas mahasiswa sebelum menyetujui atau menolak.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                &larr; Kembali ke daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Data Mahasiswa</h3>

                    <dl class="mt-4 grid grid-cols-3 gap-x-4 gap-y-3 text-sm">
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="col-span-2 font-medium text-gray-900">{{ $student->name }}</dd>

                        <dt class="text-gray-500">Email</dt>
                        <dd class="col-span-2 text-gray-900">{{ $student->email }}</dd>

                        <dt class="text-gray-500">NPM</dt>
                        <dd class="col-span-2 text-gray-900">{{ $student->npm }}</dd>

                        <dt class="text-gray-500">No. HP</dt>
                        <dd class="col-span-2 text-gray-900">{{ $student->phone_number }}</dd>

                        <dt class="text-gray-500">Peran</dt>
                        <dd class="col-span-2 text-gray-900">{{ ucfirst($student->role) }}</dd>

                        <dt class="text-gray-500">Mendaftar</dt>
                        <dd class="col-span-2 text-gray-900">{{ $student->created_at->format('d M Y, H:i') }}</dd>

                        <dt class="text-gray-500">Verifikasi</dt>
                        <dd class="col-span-2"><x-status-badge :color="$badgeColor">{{ $badgeLabel }}</x-status-badge></dd>

                        <dt class="text-gray-500">Akun</dt>
                        <dd class="col-span-2">
                            @if ($student->is_active)
                                <x-status-badge color="green">Aktif</x-status-badge>
                            @else
                                <x-status-badge color="gray">Nonaktif</x-status-badge>
                            @endif
                        </dd>

                        @if ($student->verification_status === 'rejected' && $student->rejection_reason)
                            <dt class="text-gray-500">Alasan ditolak</dt>
                            <dd class="col-span-2 text-red-700">{{ $student->rejection_reason }}</dd>
                        @endif
                    </dl>
                </section>

                <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Berkas Verifikasi</h3>

                    <div class="mt-4">
                        @if (! $student->profile_photo)
                            <p class="text-sm text-gray-500">Mahasiswa belum mengunggah berkas.</p>
                        @elseif ($isPdf)
                            <a href="{{ $student->profile_url }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-gray-200">
                                Buka berkas PDF
                            </a>
                        @else
                            <a href="{{ $student->profile_url }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ $student->profile_url }}" alt="Berkas verifikasi {{ $student->name }}"
                                    class="max-h-96 w-full rounded-lg object-contain ring-1 ring-gray-200">
                            </a>
                        @endif
                    </div>
                </section>
            </div>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-2" aria-label="Aksi verifikasi">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Setujui</h3>
                    <p class="mt-1 text-sm text-gray-500">Mahasiswa dapat langsung menggunakan layanan K-JEK.</p>

                    <form method="POST" action="{{ route('admin.users.approve', $student) }}" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <button type="submit" @disabled($student->verification_status === 'approved')
                            class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            Setujui Akun
                        </button>
                    </form>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Tolak</h3>

                    <form method="POST" action="{{ route('admin.users.reject', $student) }}" class="mt-3 flex flex-col gap-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="rejection_reason" value="Alasan penolakan" />
                            <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Contoh: Foto KTM tidak terbaca.">{{ old('rejection_reason') }}</textarea>
                            <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />
                        </div>

                        <div>
                            <x-danger-button :disabled="$student->verification_status === 'rejected'">Tolak Akun</x-danger-button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Status Akun</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Akun nonaktif tidak dapat masuk ke aplikasi, termasuk sesi yang sedang berjalan.
                </p>

                <form method="POST" action="{{ route('admin.users.activation', $student) }}" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center rounded-md px-4 py-2 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $student->is_active ? 'bg-gray-700 hover:bg-gray-800 focus:ring-gray-500' : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' }}">
                        {{ $student->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
