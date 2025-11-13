@extends('layouts.app')

@section('title', 'Edit Kejadian')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Kejadian</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('kejadian.update', $kejadian->id_kejadian) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Jenis Bencana --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis Bencana</label>
                    <select name="id_jenis_bencana" class="form-select" required>
                        <option value="">-- Pilih Jenis Bencana --</option>
                        @foreach($jenisBencana as $jb)
                            <option value="{{ $jb->id_jenis_bencana }}" {{ $kejadian->id_jenis_bencana == $jb->id_jenis_bencana ? 'selected' : '' }}>
                                {{ $jb->jenis_bencana }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nama Kejadian --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kejadian</label>
                    <select name="id_nama_kejadian" class="form-select" required>
                        <option value="">-- Pilih Nama Kejadian --</option>
                        @foreach($namaKejadian as $nk)
                            <option value="{{ $nk->id_nama_kejadian }}" {{ $kejadian->id_nama_kejadian == $nk->id_nama_kejadian ? 'selected' : '' }}>
                                {{ $nk->nama_kejadian }}
                            </option>
                        @endforeach
                    </select>
                </div>


        {{-- Tanggal --}}
         <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $kejadian->tanggal) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Waktu</label>
                        <input type="time" name="waktu" class="form-control" value="{{ old('waktu', $kejadian->waktu) }}">
                    </div>
                </div>

        {{-- Provinsi & Kabupaten (readonly) --}}
        <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Provinsi</label>
                        <input type="text" class="form-control" value="1" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kabupaten</label>
                        <input type="text" class="form-control" value="1" readonly>
                    </div>
                </div>

        {{-- Kecamatan & Desa --}}
        <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kecamatan</label>
                        <select name="id_kecamatan" class="form-select" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach($kecamatan as $kec)
                                <option value="{{ $kec->id }}" {{ old('id_kecamatan', $kejadian->id_kecamatan) == $kec->id ? 'selected' : '' }}>
                                    {{ $kec->kecamatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Desa</label>
                        <input type="text" name="id_desa" class="form-control" value="{{ old('id_desa', $kejadian->id_desa) }}">
                    </div>
                </div>

        {{-- Longitude & Latitude --}}
        <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $kejadian->longitude) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $kejadian->latitude) }}">
                    </div>
                </div>

                {{-- Beberapa textarea --}}
                @foreach (['penyebab'=>'Penyebab', 'kronologi'=>'Kronologi', 'deskripsi'=>'Deskripsi', 'kondisi_mutakhir'=>'Kondisi Mutakhir', 'upaya'=>'Upaya', 'sebaran_dampak'=>'Sebaran Dampak', 'kib'=>'KIB'] as $field => $label)
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ $label }}</label>
                    <textarea name="{{ $field }}" class="form-control" rows="2">{{ old($field, $kejadian->$field) }}</textarea>
                </div>
                @endforeach

        {{-- Status Darurat --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Status Darurat</label>
                    <select name="id_status_darurat" class="form-select">
                        <option value="">-- Pilih Status --</option>
                        @foreach($statusDarurat as $status)
                            <option value="{{ $status->id_status_darurat }}" {{ $kejadian->id_status_darurat == $status->id_status_darurat ? 'selected' : '' }}>
                                {{ $status->status }}
                            </option>
                        @endforeach
                    </select>
                </div>

        {{-- Dokumentasi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Dokumentasi</label>
                    <input type="file" name="dokumentasi" class="form-control">
                    @if($kejadian->dokumentasi)
                        <small class="d-block mt-1">File saat ini: 
                            <a href="{{ asset('storage/' . $kejadian->dokumentasi) }}" target="_blank" class="text-decoration-underline text-primary">
                                Lihat Dokumentasi
                            </a>
                        </small>
                    @endif
                </div>


{{{-- === 🧍‍♂️ DATA KORBAN === --}}
<div class="card shadow-sm border-0 mt-4">
  <div class="card-header bg-secondary text-white fw-bold">
    <i class="bi bi-people"></i> Data Korban
  </div>
  <div class="card-body">
    <div id="korban-wrapper" class="row gy-3">
      @forelse($kejadian->korban as $i => $korban)
        <div class="col-12 korban-item border rounded p-3 bg-light position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-2 hapus-korban" aria-label="Hapus"></button>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kategori Korban</label>
              <select name="korban[{{ $i }}][id_kategori_korban]" class="form-select">
                @foreach($kategoriKorban as $k)
                  <option value="{{ $k->id_kategori_korban }}" {{ $k->id_kategori_korban == $korban->id_kategori_korban ? 'selected' : '' }}>
                    {{ $k->kategori_korban }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kategori Umur</label>
              <select name="korban[{{ $i }}][id_kategori_umur]" class="form-select">
                @foreach($kategoriUmur as $u)
                  <option value="{{ $u->id_kategori_umur }}" {{ $u->id_kategori_umur == $korban->id_kategori_umur ? 'selected' : '' }}>
                    {{ $u->kategori_umur }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Laki-laki</label>
              <input type="number" name="korban[{{ $i }}][L]" class="form-control" value="{{ $korban->L }}" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Perempuan</label>
              <input type="number" name="korban[{{ $i }}][P]" class="form-control" value="{{ $korban->P }}" min="0">
            </div>
          </div>
        </div>
      @empty
        <div class="text-muted fst-italic small">Belum ada data korban.</div>
      @endforelse
    </div>

    <div class="text-end mt-3">
      <button type="button" id="add-korban" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle"></i> Tambah Korban
      </button>
    </div>
  </div>
</div>

{{-- === 🏠 Data Rumah === --}}
<div class="bg-white shadow rounded-xl p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏠 Data Rumah</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $fields = [
                'rmh_rr' => 'Rusak Ringan',
                'rmh_rs' => 'Rusak Sedang',
                'rmh_rb' => 'Rusak Berat',
                'terendam' => 'Terendam'
            ];
        @endphp
        @foreach($fields as $key => $label)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $key }}" value="{{ old($key, $kejadian->$key ?? 0) }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-400 focus:border-blue-400">
            </div>
        @endforeach
    </div>
</div>

{{-- === 🏗️ Kerusakan Infrastruktur === --}}
<div class="bg-white shadow rounded-xl p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏗️ Kerusakan Infrastruktur</h3>
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <select name="sosek[id_jenis_kerusakan_sosek]" class="border-gray-300 rounded-lg px-3 py-2">
            <option value="">Pilih Jenis</option>
            @foreach($jenisKerusakan2 as $jk2)
                <option value="{{ $jk2->id_jenis_kerusakan_sosek }}" {{ old('sosek.id_jenis_kerusakan_sosek', $sosek->id_jenis_kerusakan_sosek ?? '') == $jk2->id_jenis_kerusakan_sosek ? 'selected' : '' }}>{{ $jk2->jenis_kerusakan_sosek }}</option>
            @endforeach
        </select>
        <input type="number" name="sosek[luas]" value="{{ old('sosek.luas', $sosek->luas ?? 0) }}" placeholder="Luas" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="sosek[rr]" value="{{ old('sosek.rr', $sosek->rr ?? 0) }}" placeholder="RR" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="sosek[rs]" value="{{ old('sosek.rs', $sosek->rs ?? 0) }}" placeholder="RS" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="sosek[rb]" value="{{ old('sosek.rb', $sosek->rb ?? 0) }}" placeholder="RB" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="sosek[terendam]" value="{{ old('sosek.terendam', $sosek->terendam ?? 0) }}" placeholder="Terendam" class="border-gray-300 rounded-lg px-3 py-2">
    </div>
</div>

{{-- === 🏢 Fasilitas Umum === --}}
<div class="bg-white shadow rounded-xl p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏢 Fasilitas Umum</h3>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <select name="sarpras[id_jenis_kerusakan_sarpras]" class="border-gray-300 rounded-lg px-3 py-2">
            <option value="">Pilih Jenis</option>
            @foreach($jenisKerusakan3 as $jk3)
                <option value="{{ $jk3->id_jenis_kerusakan_sarpras }}" {{ old('sarpras.id_jenis_kerusakan_sarpras', $sarpras->id_jenis_kerusakan_sarpras ?? '') == $jk3->id_jenis_kerusakan_sarpras ? 'selected' : '' }}>{{ $jk3->jenis_kerusakan_sarpras }}</option>
            @endforeach
        </select>
        <input type="number" name="sarpras[rr]" value="{{ old('sarpras.rr', $sarpras->rr ?? 0) }}" placeholder="RR" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="sosek[rs]" value="{{ $sosek['rs'] ?? 0 }}">
<input type="number" name="sosek[rb]" value="{{ $sosek['rb'] ?? 0 }}">
<input type="number" name="sosek[rr]" value="{{ $sosek['rr'] ?? 0 }}">
<input type="number" name="sosek[terendam]" value="{{ $sosek['terendam'] ?? 0 }}">
    </div>
</div>

{{-- === 🎓 Fasilitas Pendidikan === --}}
<div class="bg-white shadow rounded-xl p-6 mt-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🎓 Fasilitas Pendidikan</h3>
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <select name="id_jenis_kerusakan_pelayanandasar" class="border-gray-300 rounded-lg px-3 py-2">
            <option value="">Pilih Jenis</option>
            @foreach($jenisKerusakan as $jk)
                <option value="{{ $jk->id_jenis_kerusakan_pelayanandasar }}" {{ old('id_jenis_kerusakan_pelayanandasar', $pelayanan->id_jenis_kerusakan_pelayanandasar ?? '') == $jk->id_jenis_kerusakan_pelayanandasar ? 'selected' : '' }}>{{ $jk->jenis_kerusakan_pelayanandasar }}</option>
            @endforeach
        </select>
        <input type="number" name="pelayanan_rr" value="{{ old('pelayanan_rr', $pelayanan->pelayanan_rr ?? 0) }}" placeholder="RR" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="pelayanan_rs" value="{{ old('pelayanan_rs', $pelayanan->pelayanan_rs ?? 0) }}" placeholder="RS" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="pelayanan_rb" value="{{ old('pelayanan_rb', $pelayanan->pelayanan_rb ?? 0) }}" placeholder="RB" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="pelayanan_terendam" value="{{ old('pelayanan_terendam', $pelayanan->pelayanan_terendam ?? 0) }}" placeholder="Terendam" class="border-gray-300 rounded-lg px-3 py-2">
        <input type="number" name="taksiran" value="{{ old('taksiran', $pelayanan->taksiran ?? 0) }}" placeholder="Taksiran (Rp)" class="border-gray-300 rounded-lg px-3 py-2">
    </div>
</div>


{{-- === 👷‍♂️ DATA PENGAWAS === --}}
<div class="card shadow-sm border-0 mt-4 mb-4">
  <div class="card-header bg-secondary text-white fw-bold">
    <i class="bi bi-person-badge"></i> Pengawas
  </div>
  <div class="card-body">
    <div id="pengawas-container" class="row gy-3">
      @if($kejadian->pengawas)
    <div class="flex items-center gap-2">
        <select name="nip_pengawas[]" class="w-full border rounded px-3 py-2">
            <option value="">-- Pilih Petugas Piket --</option>
            @foreach($pengawas as $p)
                <option value="{{ $p->nip_pengawas }}" 
                    {{ $kejadian->pengawas->nip_pengawas == $p->nip_pengawas ? 'selected' : '' }}>
                    {{ $p->nama_pengawas }} ({{ $p->nip_pengawas }})
                </option>
            @endforeach
        </select>
    </div>
@else
    <div class="flex items-center gap-2">
        <select name="nip_pengawas[]" class="w-full border rounded px-3 py-2">
            <option value="">-- Pilih Petugas Piket --</option>
            @foreach($pengawas as $p)
                <option value="{{ $p->nip_pengawas }}">{{ $p->nama_pengawas }} ({{ $p->nip_pengawas }})</option>
            @endforeach
        </select>
    </div>
@endif
    </div>

    <div class="text-end mt-3">
      <button type="button" id="tambah-pengawas" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Tambah Pengawas
      </button>
    </div>
  </div>
</div>

{{-- === SCRIPT INTERAKTIF === --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  // === KORBAN ===
  const korbanWrapper = document.getElementById('korban-wrapper');
  document.getElementById('add-korban').addEventListener('click', function() {
    const index = korbanWrapper.children.length;
    const template = `
      <div class="col-12 korban-item border rounded p-3 bg-light position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 hapus-korban" aria-label="Hapus"></button>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Kategori Korban</label>
            <select name="korban[${index}][id_kategori_korban]" class="form-select">
              <option value="">-- Pilih --</option>
              @foreach($kategoriKorban as $k)
                <option value="{{ $k->id_kategori_korban }}">{{ $k->kategori_korban }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Kategori Umur</label>
            <select name="korban[${index}][id_kategori_umur]" class="form-select">
              <option value="">-- Pilih --</option>
              @foreach($kategoriUmur as $u)
                <option value="{{ $u->id_kategori_umur }}">{{ $u->kategori_umur }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Laki-laki</label>
            <input type="number" name="korban[${index}][L]" class="form-control" min="0">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Perempuan</label>
            <input type="number" name="korban[${index}][P]" class="form-control" min="0">
          </div>
        </div>
      </div>`;
    korbanWrapper.insertAdjacentHTML('beforeend', template);
  });

  korbanWrapper.addEventListener('click', function(e) {
    if (e.target.classList.contains('hapus-korban')) e.target.closest('.korban-item').remove();
  });

  // === PENGAWAS ===
  const pengawasContainer = document.getElementById('pengawas-container');
  document.getElementById('tambah-pengawas').addEventListener('click', function() {
    const newItem = `
      <div class="col-12 pengawas-item d-flex gap-2">
        <select name="nip_pengawas[]" class="form-select flex-grow-1">
          <option value="">-- Pilih Pengawas --</option>
          @foreach($pengawas as $p)
            <option value="{{ $p->nip_pengawas }}">{{ $p->nama_pengawas }} ({{ $p->nip_pengawas }})</option>
          @endforeach
        </select>
        <button type="button" class="btn btn-outline-danger hapus-pengawas">
          <i class="bi bi-trash"></i>
        </button>
      </div>`;
    pengawasContainer.insertAdjacentHTML('beforeend', newItem);
  });

  pengawasContainer.addEventListener('click', function(e) {
    if (e.target.closest('.hapus-pengawas')) e.target.closest('.pengawas-item').remove();
  });
});
</script>

        {{-- Tombol --}}
        <div class="flex justify-end gap-3 mt-4">
    <!-- Tombol Batal -->
    <a href="{{ route('kejadian') }}"
       class="px-5 py-2 rounded-lg bg-gradient-to-r from-gray-300 to-gray-400 text-gray-800 font-semibold shadow-md hover:from-gray-400 hover:to-gray-500 transition duration-300">
        Batal
    </a>

    <!-- Tombol Update -->
    <button type="submit"
            class="px-6 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold shadow-lg hover:from-blue-600 hover:to-blue-700 transition duration-300 transform hover:scale-105">
        Update
    </button>
</div>

    </form>
</div>
@endsection
