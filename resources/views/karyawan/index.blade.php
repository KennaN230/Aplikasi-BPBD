@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">📋 Data Petugas Piket</h1>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tombol Tambah --}}
    <button id="btnTambah" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 hover:bg-blue-700">
        + Tambah Petugas Piket
    </button>

    {{-- Modal Tambah --}}
    <div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96 shadow-lg">
            <h2 class="text-lg font-semibold mb-3">Tambah Petugas Piket</h2>
            <form method="POST" action="{{ route('karyawan.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">NIP</label>
                    <input type="text" name="nip_pengawas" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="nama_pengawas" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                    <input type="text" name="jabatan" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Tugas</label>
                    <input type="text" name="tugas" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" id="btnBatal" class="px-3 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <table class="w-full border-collapse bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="py-2 px-4 text-left">#</th>
                <th class="py-2 px-4 text-left">NIP</th>
                <th class="py-2 px-4 text-left">Nama</th>
                <th class="py-2 px-4 text-left">Jabatan</th>
                <th class="py-2 px-4 text-left">Tugas</th>
                <th class="py-2 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawan as $index => $k)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2 px-4">{{ $index + 1 }}</td>
                <td class="py-2 px-4">{{ $k->nip_pengawas }}</td>
                <td class="py-2 px-4">{{ $k->nama_pengawas }}</td>
                <td class="py-2 px-4">{{ $k->jabatan }}</td>
                <td class="py-2 px-4">{{ $k->tugas }}</td>
                <td class="py-2 px-4 text-center">
                    <form action="{{ route('karyawan.destroy', $k->nip_pengawas) }}" method="POST" onsubmit="return confirm('Yakin hapus karyawan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-gray-500">Belum ada data karyawan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    const modal = document.getElementById('modalTambah');
    document.getElementById('btnTambah').onclick = () => modal.classList.remove('hidden');
    document.getElementById('btnBatal').onclick = () => modal.classList.add('hidden');
</script>
@endsection
