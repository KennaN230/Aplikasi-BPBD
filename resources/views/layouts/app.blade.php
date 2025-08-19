{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body class="flex bg-gray-100 min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-blue-700 text-white flex flex-col">
        <div class="p-6 border-b border-blue-500">
            <h1 class="text-2xl font-bold">BPBD</h1>
            <p class="text-sm text-gray-200">Kabupaten Malang</p>
        </div>
        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="block py-2 px-3 rounded transition 
                       {{ request()->routeIs('dashboard') ? 'bg-blue-500' : 'hover:bg-blue-500' }}">
                       Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('kejadian') }}" 
                       class="block py-2 px-3 rounded transition 
                       {{ request()->routeIs('kejadian') ? 'bg-blue-500' : 'hover:bg-blue-500' }}">
                       Kejadian
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.home') }}" 
                       class="block py-2 px-3 rounded transition 
                       {{ request()->routeIs('admin.home') ? 'bg-blue-500' : 'hover:bg-blue-500' }}">
                       Admin
                    </a>
                </li>
            </ul>
        </nav>
        <footer class="p-4 text-xs text-gray-200 border-t border-blue-500">
            © 2025 BPBD Malang
        </footer>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-8">
        <div class="bg-white shadow rounded-xl p-6">
            @yield('content')
        </div>
    </main>

</body>
</html>
