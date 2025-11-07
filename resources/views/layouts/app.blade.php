{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard BPBD' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        /* Tambahan kustom untuk sidebar */
        .sidebar {
            width: 250px;
            background-color: #122453;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
        }
    </style>
</head>
<body class="flex min-h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <!-- Header -->
            <div class="flex items-center p-4 border-b border-white/20">
                <div class="flex items-center space-x-1">
                    <img src="/gambar/Logo 1 1.png" alt="BPBD Kota Malang" class="w-6 h-6">
                    <img src="/gambar/Logo_Kabupaten_Malang 1.png" alt="Kabupaten Malang" class="w-6 h-6">
                </div>
                <h2 class="ml-2 text-sm font-semibold leading-tight">
                    Informasi Kejadian Kab Malang
                </h2>
            </div>

            <!-- Menu -->
            <nav class="sidebar-menu mt-2 flex flex-col text-sm">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-2 px-5 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <img src="/gambar/lg_rmh.png" class="w-5 h-5" alt="Icon">
                    Beranda
                </a>

                <a href="{{ route('kejadian') }}" 
                   class="flex items-center gap-2 px-5 py-3 hover:bg-white/10 {{ request()->routeIs('kejadian.index') ? 'active' : '' }}">
                    <img src="/gambar/lg_kejadian.png" class="w-5 h-5" alt="Kejadian">
                    Kejadian
                </a>

                <a href="#" class="flex items-center gap-2 px-5 py-3 hover:bg-white/10">
                    <img src="/gambar/lg_gempaBumi.png" class="w-5 h-5" alt="Gempa Bumi">
                    Gempa Bumi
                </a>

                <a href="/rain" class="flex items-center gap-2 px-5 py-3 hover:bg-white/10">
                    <img src="/gambar/lg_hujan.png" class="w-5 h-5" alt="Hari Hujan">
                    Hari Hujan & Tanpa Hujan
                </a>

                <a href="#" class="flex items-center gap-2 px-5 py-3 hover:bg-white/10">
                    <img src="/gambar/lg_gelombang.png" class="w-5 h-5" alt="Tinggi Gelombang">
                    Tinggi Gelombang
                </a>

                <a href="#" class="flex items-center gap-2 px-5 py-3 hover:bg-white/10">
                    <img src="/gambar/lg_destana.png" class="w-5 h-5" alt="DESTANA">
                    DESTANA Kab. Malang
                </a>

                <a href="#" class="flex items-center gap-2 px-5 py-3 hover:bg-white/10">
                    <img src="/gambar/lg_spab.png" class="w-5 h-5" alt="SPAB">
                    SPAB Kab. Malang
                </a>
            </nav>
        </div>

        <!-- Footer -->
        <footer class="p-4 text-xs text-gray-300 border-t border-gray-700">
            © 2025 BPBD Kab. Malang
        </footer>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <div class="bg-white shadow rounded-xl p-6">
            @yield('content')
        </div>
    </main>

</body>
</html>
