<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>K-JEK — Komunitas Ojek Mahasiswa UNSIKA</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap"
        rel="stylesheet" />

    <!-- Scripts & Styles (Bawaan Breeze/Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-slate-50 text-slate-800">

    <!-- NAVBAR -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <div
                        class="w-10 h-10 bg-blue-900 text-amber-400 rounded-xl flex items-center justify-center font-extrabold text-lg shadow-sm">
                        K
                    </div>
                    <div>
                        <span class="font-extrabold text-xl tracking-tight text-blue-950">K-JEK</span>
                        <span
                            class="hidden sm:inline-block text-xs font-semibold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full ml-1">UNSIKA</span>
                    </div>
                </a>

                <!-- Nav Links (Desktop) -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="#keunggulan" class="hover:text-blue-900 transition">Keunggulan</a>
                    <a href="#cara-kerja" class="hover:text-blue-900 transition">Cara Kerja</a>
                    <a href="#tarif" class="hover:text-blue-900 transition">Skema Tarif</a>
                </nav>

                <!-- Auth Action Buttons -->
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                                Dashboard Saya &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-blue-900 transition">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                                    Daftar Mahasiswa
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section
        class="relative overflow-hidden bg-gradient-to-br from-blue-950 via-blue-900 to-slate-900 text-white py-16 lg:py-24">
        <!-- Decorative Background Blur -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-amber-400/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Copywriting -->
                <div class="lg:col-span-7 space-y-6">
                    <div
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/15 text-amber-300 text-xs font-semibold tracking-wide uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Eksklusif Civitas Akademika UNSIKA Karawang
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                        Tebengan Kampus <span class="text-amber-400">Aman, Jelas,</span> &amp; Ramah Kantong.
                    </h1>

                    <p class="text-base sm:text-lg text-blue-100/90 max-w-2xl leading-relaxed">
                        Platform resmi komunitas K-JEK. Tidak ada lagi pesan orderan tenggelam di grup WhatsApp, rebutan
                        pesanan, atau tarif sepihak. Semua terstruktur, transparan per kilometer, dan terverifikasi
                        mahasiswa.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-6 py-3.5 bg-amber-400 hover:bg-amber-300 text-blue-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 transition text-sm sm:text-base">
                                Buka Aplikasi K-JEK
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="px-6 py-3.5 bg-amber-400 hover:bg-amber-300 text-blue-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 transition text-sm sm:text-base">
                                Pesan Tebengan Sekarang
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-6 py-3.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold rounded-xl transition text-sm sm:text-base">
                                Gabung Jadi Driver Part-Time
                            </a>
                        @endauth
                    </div>

                    <div class="flex items-center gap-6 pt-4 text-xs sm:text-sm text-blue-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Verifikasi Mahasiswa Aktif
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Estimasi Tarif Otomatis
                        </div>
                    </div>
                </div>

                <!-- Right Preview Card (Simulasi Board Order) -->
                <div class="lg:col-span-5">
                    <div class="bg-white text-slate-800 rounded-2xl p-6 shadow-2xl border border-slate-100">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Contoh Order
                                    Board</p>
                                <h3 class="font-bold text-lg text-slate-900">#KJEK-2026-042</h3>
                            </div>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">
                                OPEN
                            </span>
                        </div>

                        <div class="py-5 space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 w-3 h-3 rounded-full bg-blue-600 ring-4 ring-blue-100"></div>
                                <div>
                                    <p class="text-xs text-slate-400 font-medium">Titik Jemput</p>
                                    <p class="font-semibold text-slate-800">Gerbang Utama Kampus 1 UNSIKA</p>
                                </div>
                            </div>

                            <div class="ml-1.5 border-l-2 border-dashed border-slate-200 h-4"></div>

                            <div class="flex items-start gap-3">
                                <div class="mt-1 w-3 h-3 rounded-full bg-amber-500 ring-4 ring-amber-100"></div>
                                <div>
                                    <p class="text-xs text-slate-400 font-medium">Titik Tujuan</p>
                                    <p class="font-semibold text-slate-800">Perumnas Telukjambe / Galuh Mas</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-slate-50 rounded-xl p-4 flex items-center justify-between border border-slate-100">
                            <div>
                                <p class="text-xs text-slate-500">Estimasi Jarak &amp; Tarif</p>
                                <p class="font-extrabold text-lg text-blue-950">3.4 km &bull; Rp 11.900</p>
                            </div>
                            <span class="px-4 py-2 bg-blue-900 text-white text-xs font-bold rounded-lg">
                                Ambil Order
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KEUNGGULAN / SOLUSI MASALAH SECTION -->
    <section id="keunggulan" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wider mb-2">Kenapa K-JEK Web?</h2>
                <p class="text-3xl font-extrabold text-slate-900">Solusi Modern dari Kendala Grup WhatsApp</p>
                <p class="mt-3 text-slate-600">Dirancang khusus untuk mengatasi masalah pemesanan manual di komunitas
                    ojek mahasiswa UNSIKA.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2">Anti Chat Tenggelam</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Semua permintaan tebengan tampil rapi di <em>Board
                            Pesanan</em> terpusat sampai diambil oleh driver.</p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2">Status Terkunci Jelas</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Alur status bertahap (<em>Open &rarr; Taken &rarr;
                            OTW &rarr; Completed</em>) mencegah orderan ganda atau bentrok driver.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-md transition">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2">Tarif Adil Otomatis</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Kalkulator peta menghitung biaya otomatis
                        berdasarkan jarak per km agar adil bagi driver maupun penumpang.</p>
                </div>

                <!-- Card 4 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-md transition">
                    <div
                        class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2">Verifikasi Mahasiswa &amp; Rating</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Wajib validasi mahasiswa oleh Admin sebelum
                        bertransaksi, dilengkapi riwayat perjalanan serta rating bintang.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CARA KERJA SECTION -->
    <section id="cara-kerja" class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wider mb-2">Alur Penggunaan</h2>
                <p class="text-3xl font-extrabold text-slate-900">Mudah Digunakan oleh Mahasiswa</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Alur Customer -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                    <div class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full mb-4">
                        UNTUK CUSTOMER (PEMESAN)
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-6">Cara Memesan Tebengan</h3>
                    <ol class="space-y-4 text-sm text-slate-600">
                        <li class="flex gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-blue-900 text-white font-bold text-xs flex items-center justify-center shrink-0">1</span>
                            <span><strong>Daftar &amp; Upload Foto:</strong> Registrasi akun lalu tunggu persetujuan
                                verifikasi dari Admin.</span>
                        </li>
                        <li class="flex gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-blue-900 text-white font-bold text-xs flex items-center justify-center shrink-0">2</span>
                            <span><strong>Tandai Titik di Peta:</strong> Pilih titik jemput dan tujuan, sistem otomatis
                                menghitung estimasi tarif.</span>
                        </li>
                        <li class="flex gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-blue-900 text-white font-bold text-xs flex items-center justify-center shrink-0">3</span>
                            <span><strong>Tunggu Driver &amp; Koordinasi:</strong> Pantau status pesanan hingga diambil
                                driver dan lanjut koordinasi via WhatsApp.</span>
                        </li>
                        <li class="flex gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-blue-900 text-white font-bold text-xs flex items-center justify-center shrink-0">4</span>
                            <span><strong>Selesai &amp; Beri Ulasan:</strong> Setelah sampai tujuan, berikan rating
                                bintang untuk menjaga reputasi driver.</span>
                        </li>
                    </ol>
                </div>

                <!-- Alur Driver -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                    <div class="inline-block px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full mb-4">
                        UNTUK DRIVER (PENGEMUDI)
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-6">Cara Mengambil Orderan</h3>
                    <ol class="space-y-4 text-sm text-slate-600">
                        <li class="flex gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-amber-500 text-blue-950 font-bold text-xs flex items-center justify-center shrink-0">1</span>
                            <span><strong>Verifikasi Driver Mahasiswa:</strong> Daftar sebagai driver dengan mengunggah
                                foto diri untuk keamanan.</span>
                        </li>
                        <li class="flex gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-amber-500 text-blue-950 font-bold text-xs flex items-center justify-center shrink-0">2</span>
                            <span><strong>Pantau Board Pesanan:</strong> Cari pesanan berstatus <em>OPEN</em> yang
                                rutenya searah dengan jadwal kuliahmu.</span>
                        </li>
                        <li class="flex gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-amber-500 text-blue-950 font-bold text-xs flex items-center justify-center shrink-0">3</span>
                            <span><strong>Ambil &amp; Jemput:</strong> Klik <em>Ambil Pesanan</em>, hubungi pemesan
                                lewat tombol WhatsApp, dan navigasi via Google Maps.</span>
                        </li>
                        <li class="flex gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-amber-500 text-blue-950 font-bold text-xs flex items-center justify-center shrink-0">4</span>
                            <span><strong>Update Status &amp; Penghasilan:</strong> Ubah status hingga
                                <em>Completed</em> dan pantau rekap pendapatan harianmu.</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- SKEMA TARIF BANNER -->
    <section id="tarif" class="py-16 bg-blue-950 text-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-3">Tarif Transparan</h2>
            <p class="text-blue-200 max-w-2xl mx-auto mb-8 text-sm sm:text-base">
                Mengikuti standar komunitas K-JEK UNSIKA, tarif dihitung otomatis berdasarkan jarak tempuh (kisaran
                Rp3.000 – Rp4.000/km) dengan tarif dasar minimum yang terjangkau mahasiswa.
            </p>
            <a href="{{ route('register') }}"
                class="inline-flex items-center px-6 py-3 bg-amber-400 hover:bg-amber-300 text-blue-950 font-bold rounded-xl transition text-sm">
                Mulai Gunakan K-JEK Sekarang &rarr;
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-8 border-t border-slate-800">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="font-bold text-white">K-JEK</span>
                <span>&bull; Komunitas Ojek Mahasiswa UNSIKA</span>
            </div>
            <p class="text-xs text-slate-500 text-center sm:text-right">
                &copy; {{ date('Y') }} Kelompok 1 (5E Informatika) &mdash; Fakultas Ilmu Komputer Universitas
                Singaperbangsa Karawang.
            </p>
        </div>
    </footer>

</body>

</html>