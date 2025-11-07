@extends('layouts.app')

@section('title', 'Edit Kejadian')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-bold mb-4">Edit Kejadian</h2>

    <form action="{{ route('kejadian.update', $kejadian->id_kejadian) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Jenis Bencana --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Jenis Bencana</label>
    <select name="id_jenis_bencana" class="w-full border rounded px-3 py-2" required>
        <option value="">-- Pilih Jenis Bencana --</option>
        @foreach($jenisBencana as $jb)
            <option value="{{ $jb->id_jenis_bencana }}" 
                {{ $kejadian->id_jenis_bencana == $jb->id_jenis_bencana ? 'selected' : '' }}>
                {{ $jb->jenis_bencana }}
            </option>
        @endforeach
    </select>
</div>

{{-- Nama Kejadian --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Nama Kejadian</label>
    <select name="id_nama_kejadian" class="w-full border rounded px-3 py-2" required>
        <option value="">-- Pilih Nama Kejadian --</option>
        @foreach($namaKejadian as $nk)
            <option value="{{ $nk->id_nama_kejadian }}" 
                {{ $kejadian->id_nama_kejadian == $nk->id_nama_kejadian ? 'selected' : '' }}>
                {{ $nk->nama_kejadian }}
            </option>
        @endforeach
    </select>
</div>


        {{-- Tanggal --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
            <input type="date" name="tanggal"
                   value="{{ old('tanggal', $kejadian->tanggal) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Waktu --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Waktu</label>
            <input type="time" name="waktu"
                   value="{{ old('waktu', $kejadian->waktu) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Provinsi & Kabupaten (readonly) --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Provinsi</label>
            <input type="text" name="id_provinsi" value="1" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kabupaten</label>
            <input type="text" name="id_kabupaten" value="1" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
        </div>

        {{-- Kecamatan & Desa --}}
        {{-- Kecamatan --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
    <select name="id_kecamatan" class="w-full border rounded px-3 py-2" required>
        <option value="">-- Pilih Kecamatan --</option>
        @foreach($kecamatan as $kec)
            <option value="{{ $kec->id }}" {{ old('id_kecamatan') == $kec->id ? 'selected' : '' }}>
                {{ $kec->kecamatan }}
            </option>
        @endforeach
    </select>
    @error('id_kecamatan')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div> 
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Desa</label>
            <input type="text" name="id_desa"
                   value="{{ old('id_desa', $kejadian->id_desa) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Longitude & Latitude --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Longitude</label>
            <input type="text" name="longitude"
                   value="{{ old('longitude', $kejadian->longitude) }}"
                   class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Latitude</label>
            <input type="text" name="latitude"
                   value="{{ old('latitude', $kejadian->latitude) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Penyebab --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Penyebab</label>
            <textarea name="penyebab" class="w-full border rounded px-3 py-2" rows="2">{{ old('penyebab', $kejadian->penyebab) }}</textarea>
        </div>

        {{-- Kronologi --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kronologi</label>
            <textarea name="kronologi" class="w-full border rounded px-3 py-2" rows="2">{{ old('kronologi', $kejadian->kronologi) }}</textarea>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" class="w-full border rounded px-3 py-2" rows="2">{{ old('deskripsi', $kejadian->deskripsi) }}</textarea>
        </div>

        {{-- Sumber --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Sumber</label>
            <input type="text" name="sumber"
                   value="{{ old('sumber', $kejadian->sumber) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Kondisi Mutakhir --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kondisi Mutakhir</label>
            <textarea name="kondisi_mutakhir" class="w-full border rounded px-3 py-2" rows="2">{{ old('kondisi_mutakhir', $kejadian->kondisi_mutakhir) }}</textarea>
        </div>

        {{-- Status Darurat --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Status Darurat</label>
            <select name="id_status_darurat" class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Status Darurat --</option>
                @foreach($statusDarurat as $status)
                    <option value="{{ $status->id_status_darurat }}"
                        {{ $kejadian->id_status_darurat == $status->id_status_darurat ? 'selected' : '' }}>
                        {{ $status->status }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Upaya --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Upaya</label>
            <textarea name="upaya" class="w-full border rounded px-3 py-2" rows="2">{{ old('upaya', $kejadian->upaya) }}</textarea>
        </div>

        {{-- Dokumentasi --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Dokumentasi</label>
            <input type="file" name="dokumentasi" class="w-full border rounded px-3 py-2">
            @if($kejadian->dokumentasi)
                <p class="text-sm mt-1">File saat ini: 
                    <a href="{{ asset('storage/' . $kejadian->dokumentasi) }}" target="_blank" class="text-blue-600 underline">Lihat</a>
                </p>
            @endif
        </div>

        {{-- Sebaran Dampak --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Sebaran Dampak</label>
            <textarea name="sebaran_dampak" class="w-full border rounded px-3 py-2" rows="2">{{ old('sebaran_dampak', $kejadian->sebaran_dampak) }}</textarea>
        </div>

        {{-- KIB --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">KIB</label>
            <textarea name="kib" class="w-full border rounded px-3 py-2" rows="2">{{ old('kib', $kejadian->kib) }}</textarea>
        </div>

        {{-- === Data Korban === --}}
<h2 class="font-bold mt-6 mb-2">Data Korban</h2>

<div id="korban-wrapper">
    @foreach($kejadian->korban as $i => $korban)
        <div class="korban-item border rounded p-3 mb-3">
            <div class="mb-4">
                <label class="block text-sm font-medium">Kategori Korban</label>
                <select name="korban[{{ $i }}][id_kategori_korban]" class="w-full border rounded px-3 py-2">
                    @foreach($kategoriKorban as $k)
                        <option value="{{ $k->id_kategori_korban }}" 
                            {{ $k->id_kategori_korban == $korban->id_kategori_korban ? 'selected' : '' }}>
                            {{ $k->kategori_korban }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Kategori Umur</label>
                <select name="korban[{{ $i }}][id_kategori_umur]" class="w-full border rounded px-3 py-2">
                    @foreach($kategoriUmur as $u)
                        <option value="{{ $u->id_kategori_umur }}" 
                            {{ $u->id_kategori_umur == $korban->id_kategori_umur ? 'selected' : '' }}>
                            {{ $u->kategori_umur }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Laki-laki</label>
                    <input type="number" name="korban[{{ $i }}][L]" class="w-full border rounded px-3 py-2"
                        value="{{ $korban->L }}" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium">Perempuan</label>
                    <input type="number" name="korban[{{ $i }}][P]" class="w-full border rounded px-3 py-2"
                        value="{{ $korban->P }}" min="0">
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Tombol Tambah Korban --}}
<div class="mb-4">
    <button type="button" id="add-korban" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded">
        + Tambah Korban
    </button>
</div>

{{-- === Data Rumah === --}}
<div class="bg-white shadow rounded-xl p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏠 Data Rumah</h2>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rusak Ringan</label>
            <input type="number" name="rmh_rr" value="{{ old('rmh_rr', $kejadian->rmh_rr ?? 0) }}" class="w-full border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rusak Sedang</label>
            <input type="number" name="rmh_rs" value="{{ old('rmh_rs', $kejadian->rmh_rs ?? 0) }}" class="w-full border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rusak Berat</label>
            <input type="number" name="rmh_rb" value="{{ old('rmh_rb', $kejadian->rmh_rb ?? 0) }}" class="w-full border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Terendam</label>
            <input type="number" name="terendam" value="{{ old('terendam', $kejadian->terendam ?? 0) }}" class="w-full border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>
</div>

{{-- === Kerusakan Infrastruktur === --}}
<div class="bg-white shadow rounded-xl p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏗️ Kerusakan Infrastruktur</h3>
    <div class="grid grid-cols-6 gap-3">
        <select name="sosek[id_jenis_kerusakan_sosek]" class="border-gray-300 rounded-lg p-2">
            @for ($i = 1; $i <= 9; $i++)
                <option value="{{ $i }}" {{ old('sosek.id_jenis_kerusakan_sosek', $sosek->id_jenis_kerusakan_sosek ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <input type="number" name="sosek[luas]" value="{{ old('sosek.luas', $sosek->luas ?? 0) }}" placeholder="Luas" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="sosek[rr]" value="{{ old('sosek.rr', $sosek->rr ?? 0) }}" placeholder="RR" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="sosek[rs]" value="{{ old('sosek.rs', $sosek->rs ?? 0) }}" placeholder="RS" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="sosek[rb]" value="{{ old('sosek.rb', $sosek->rb ?? 0) }}" placeholder="RB" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="sosek[terendam]" value="{{ old('sosek.terendam', $sosek->terendam ?? 0) }}" placeholder="Terendam" class="border-gray-300 rounded-lg p-2">
    </div>
</div>

{{-- === Fasilitas Umum === --}}
<div class="bg-white shadow rounded-xl p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏢 Fasilitas Umum</h3>
    <div class="grid grid-cols-5 gap-3">
        <select name="sarpras[id_jenis_kerusakan_sarpras]" class="border-gray-300 rounded-lg p-2">
            @for ($i = 1; $i <= 8; $i++)
                <option value="{{ $i }}" {{ old('sarpras.id_jenis_kerusakan_sarpras', $sarpras->id_jenis_kerusakan_sarpras ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <input type="number" name="sarpras[rr]" value="{{ old('sarpras.rr', $sarpras->rr ?? 0) }}" placeholder="RR" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="sarpras[rs]" value="{{ old('sarpras.rs', $sarpras->rs ?? 0) }}" placeholder="RS" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="sarpras[rb]" value="{{ old('sarpras.rb', $sarpras->rb ?? 0) }}" placeholder="RB" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="sarpras[terendam]" value="{{ old('sarpras.terendam', $sarpras->terendam ?? 0) }}" placeholder="Terendam" class="border-gray-300 rounded-lg p-2">
    </div>
</div>

{{-- === Fasilitas Pendidikan === --}}
<div class="bg-white shadow rounded-xl p-6 mt-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🎓 Fasilitas Pendidikan</h3>
    <div class="grid grid-cols-6 gap-3">
        <select name="id_jenis_kerusakan_pelayanandasar" class="border-gray-300 rounded-lg p-2">
            @for ($i = 1; $i <= 3; $i++)
                <option value="{{ $i }}" {{ old('id_jenis_kerusakan_pelayanandasar', $pelayanan->id_jenis_kerusakan_pelayanandasar ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <input type="number" name="pelayanan_rr" value="{{ old('pelayanan_rr', $pelayanan->pelayanan_rr ?? 0) }}" placeholder="RR" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="pelayanan_rs" value="{{ old('pelayanan_rs', $pelayanan->pelayanan_rs ?? 0) }}" placeholder="RS" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="pelayanan_rb" value="{{ old('pelayanan_rb', $pelayanan->pelayanan_rb ?? 0) }}" placeholder="RB" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="pelayanan_terendam" value="{{ old('pelayanan_terendam', $pelayanan->pelayanan_terendam ?? 0) }}" placeholder="Terendam" class="border-gray-300 rounded-lg p-2">
        <input type="number" name="taksiran" value="{{ old('taksiran', $pelayanan->taksiran ?? 0) }}" placeholder="Taksiran (Rp)" class="border-gray-300 rounded-lg p-2">
    </div>
</div>

{{-- === Pilih Pengawas === --}}
<h3 class="font-bold mt-6 mb-2">Pengawas</h3>
<div class="mb-4">
    <label for="nip_pengawas" class="block text-sm font-medium text-gray-700">Pilih Pengawas</label>
    <select name="nip_pengawas" id="nip_pengawas" class="border rounded px-3 py-2 w-full">
        <option value="">-- Pilih Pengawas --</option>
        @foreach($pengawas as $p)
            <option value="{{ $p->nip_pengawas }}"
                {{ $kejadian->nip_pengawas == $p->nip_pengawas ? 'selected' : '' }}>
                {{ $p->nama_pengawas }} ({{ $p->nip_pengawas }})
            </option>
        @endforeach
    </select>

    <button type="button" id="tambah-pengawas"
        class="mt-2 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
        + Tambah Pengawas
    </button>
</div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-2">
            <a href="{{ route('kejadian') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
        </div>
    </form>
</div>
@endsection
