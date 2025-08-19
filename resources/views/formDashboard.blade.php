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

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td><td>Wildatul Fajriyah</td><td>wildatul@gmail.com</td><td>Admin</td><td>Aktif</td>
                    <td>
                        <button class="btn btn-edit">Edit</button>
                        <button class="btn btn-delete">Hapus</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td><td>Junior Harianomang</td><td>junior@yahoo.com</td><td>Admin</td><td>Aktif</td>
                    <td>
                        <button class="btn btn-edit">Edit</button>
                        <button class="btn btn-delete">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            &copy; 2025 BPBD Kabupaten Malang | +62 822 4409 4886 | @bpbd_malangkab
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center text-sm text-gray-500 mt-6">
        &copy; 2025 BPBD Kabupaten Malang | +62 822 4409 4886 | @bpbd_malangkab
    </div>
@endsection
