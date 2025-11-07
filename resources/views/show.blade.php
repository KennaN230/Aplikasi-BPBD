@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-3xl font-bold mb-6 text-blue-700">📌 Detail Kejadian</h2>

    {{-- Info Kejadian --}}
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Judul</p>
            <p class="font-semibold">test</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Tanggal</p>
            <p class="font-semibold">{{ $kejadian->tanggal }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Lokasi</p>
            <p class="font-semibold">{{ $kejadian->kecamatan->kecamatan ?? '-' }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Pengawas</p>
            <p class="font-semibold">{{ $kejadian->pengawas->nama_pengawas ?? '-' }}</p>
        </div>
    </div>

    <hr class="my-6">

    {{-- Data Rumah --}}
    <h3 class="text-xl font-bold text-gray-700 mb-3">🏠 Data Rumah</h3>
    <table class="w-full border border-gray-300 rounded-lg overflow-hidden mb-6">
        <thead class="bg-blue-100 text-blue-700">
            <tr>
                <th class="px-4 py-2 text-left">Rusak Ringan</th>
                <th class="px-4 py-2 text-left">Rusak Sedang</th>
                <th class="px-4 py-2 text-left">Rusak Berat</th>
                <th class="px-4 py-2 text-left">Terendam</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-white">
                <td class="px-4 py-2">{{ $kejadian->rumah->rr ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->rumah->rs ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->rumah->rb ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->rumah->terendam ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Sosial Ekonomi --}}
    <h3 class="text-xl font-bold text-gray-700 mb-3">💰 Kerusakan Sosial Ekonomi</h3>
    <table class="w-full border border-gray-300 rounded-lg overflow-hidden mb-6">
        <thead class="bg-blue-100 text-blue-700">
            <tr>
                <th class="px-4 py-2 text-left">Rusak Ringan</th>
                <th class="px-4 py-2 text-left">Rusak Sedang</th>
                <th class="px-4 py-2 text-left">Rusak Berat</th>
                <th class="px-4 py-2 text-left">Terendam</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-white">
                <td class="px-4 py-2">{{ $kejadian->sosek->rr ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->sosek->rs ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->sosek->rb ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->sosek->terendam ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Sarpras --}}
    <h3 class="text-xl font-bold text-gray-700 mb-3">🏢 Kerusakan Sarana dan Prasarana</h3>
    <table class="w-full border border-gray-300 rounded-lg overflow-hidden mb-6">
        <thead class="bg-blue-100 text-blue-700">
            <tr>
                <th class="px-4 py-2 text-left">Rusak Ringan</th>
                <th class="px-4 py-2 text-left">Rusak Sedang</th>
                <th class="px-4 py-2 text-left">Rusak Berat</th>
                <th class="px-4 py-2 text-left">Terendam</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-white">
                <td class="px-4 py-2">{{ $kejadian->sarpras->rr ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->sarpras->rs ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->sarpras->rb ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->sarpras->terendam ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Pelayanan --}}
    <h3 class="text-xl font-bold text-gray-700 mb-3">⚕️ Kerusakan Pelayanan Dasar</h3>
    <table class="w-full border border-gray-300 rounded-lg overflow-hidden mb-6">
        <thead class="bg-blue-100 text-blue-700">
            <tr>
                <th class="px-4 py-2 text-left">Rusak Ringan</th>
                <th class="px-4 py-2 text-left">Rusak Sedang</th>
                <th class="px-4 py-2 text-left">Rusak Berat</th>
                <th class="px-4 py-2 text-left">Terendam</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-white">
                <td class="px-4 py-2">{{ $kejadian->pelayanan->rr ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->pelayanan->rs ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->pelayanan->rb ?? 0 }}</td>
                <td class="px-4 py-2">{{ $kejadian->pelayanan->terendam ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Korban --}}
    <h3 class="text-xl font-bold text-gray-700 mb-3">👥 Data Korban</h3>
    <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
        <thead class="bg-blue-100 text-blue-700">
            <tr>
                <th class="px-4 py-2 text-left">Kategori</th>
                <th class="px-4 py-2 text-left">Laki-laki</th>
                <th class="px-4 py-2 text-left">Perempuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kejadian->korban as $korban)
                <tr class="bg-white">
                    <td class="px-4 py-2">{{ $korban->kategori_korban->nama ?? 'Tanpa Kategori' }}</td>
                    <td class="px-4 py-2">{{ $korban->L }}</td>
                    <td class="px-4 py-2">{{ $korban->P }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center px-4 py-2 text-gray-500">Tidak ada data korban</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tombol --}}
    <div class="mt-8 flex justify-end">
        <a href="{{ route('kejadian') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow">
            ⬅ Kembali
        </a>
    </div>  
</div>
@endsection
