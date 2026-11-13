<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Status Verifikasi Akun Mahasiswa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                @if(auth()->user()->verification_status === 'pending')
                    <div class="text-yellow-500 text-5xl mb-4">⏳</div>
                    <h3 class="text-lg font-bold text-gray-900">Akun Sedang Menunggu Verifikasi Admin</h3>
                    <p class="text-gray-600 mt-2">
                        Terima kasih telah mendaftar di <strong>K-JEK UNSIKA</strong>. Pengurus komunitas sedang memeriksa
                        foto Anda untuk memastikan keamanan komunitas. Silakan cek kembali secara berkala.
                    </p>
                @elseif(auth()->user()->verification_status === 'rejected')
                    <div class="text-red-500 text-5xl mb-4">❌</div>
                    <h3 class="text-lg font-bold text-red-700">Verifikasi Akun Ditolak</h3>
                    <p class="text-gray-600 mt-2">
                        Alasan Penolakan:
                        <strong>{{ auth()->user()->rejection_reason ?? 'Foto tidak jelas atau bukan mahasiswa aktif.' }}</strong>
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>