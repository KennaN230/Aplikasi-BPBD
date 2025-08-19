@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    {{-- Topbar --}}
    <div class="flex justify-between items-center mb-6 bg-white shadow rounded-xl p-4">
        <h1 class="text-2xl font-bold text-gray-700">Selamat Datang!</h1>
        <div class="flex items-center space-x-4">
            <span class="text-gray-500 text-sm">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                <span class="text-gray-600 font-semibold">A</span>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow-lg rounded-xl p-6 border-l-4 border-blue-900">
            <h2 class="text-3xl font-bold text-gray-800">10</h2>
            <p class="text-gray-500">Data Admin</p>
        </div>
        <div class="bg-white shadow-lg rounded-xl p-6 border-l-4 border-orange-600">
            <h2 class="text-3xl font-bold text-gray-800">05</h2>
            <p class="text-gray-500">Data User</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Daftar Pengguna</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-200 rounded-lg">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Nama Pengguna</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Role</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">1</td>
                        <td class="px-4 py-2">Wildatul Fajriyah</td>
                        <td class="px-4 py-2">wildatul@gmail.com</td>
                        <td class="px-4 py-2">Admin</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs bg-green-100 text-green-600 rounded">Aktif</span></td>
                        <td class="px-4 py-2 space-x-2">
                            <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Edit</button>
                            <button class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">Hapus</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">2</td>
                        <td class="px-4 py-2">Junior Harianomang</td>
                        <td class="px-4 py-2">junior@yahoo.com</td>
                        <td class="px-4 py-2">Admin</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs bg-green-100 text-green-600 rounded">Aktif</span></td>
                        <td class="px-4 py-2 space-x-2">
                            <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Edit</button>
                            <button class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center text-sm text-gray-500 mt-6">
        &copy; 2025 BPBD Kabupaten Malang | +62 822 4409 4886 | @bpbd_malangkab
    </div>
@endsection
