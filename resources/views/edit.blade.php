@extends('layouts.app')

@section('title', 'Edit Kejadian')

@section('content')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container my-4">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-pencil-square"></i> Edit Kejadian
        </div>
        <div class="card-body">
            <form id="formKejadian" action="{{ route('kejadian.update', $kejadian->id_kejadian) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- === Jenis Bencana === --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis Bencana</label>
                    <select name="id_jenis_bencana" id="id_jenis_bencana" class="form-select" required>
                        <option value="">-- Pilih Jenis Bencana --</option>
                        @foreach($jenisBencana as $jb)
                            <option value="{{ $jb->id_jenis_bencana }}" {{ $kejadian->id_jenis_bencana == $jb->id_jenis_bencana ? 'selected' : '' }}>
                                {{ $jb->jenis_bencana }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- === Nama Kejadian Manual === --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kejadian</label>
                    <input type="text" name="nama_kejadian" 
                           class="form-control" 
                           placeholder="Contoh: Banjir Bandang, Longsor, Puting Beliung" 
                           value="{{ old('nama_kejadian', $kejadian->nama_kejadian) }}"
                           required>
                    <small class="text-muted">Masukkan nama kejadian secara manual.</small>
                </div>

                {{-- === Tanggal & Waktu === --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal', $kejadian->tanggal) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Waktu</label>
                        <input type="time" name="waktu" class="form-control" value="{{ old('waktu', $kejadian->waktu) }}" required>
                    </div>
                </div>

                {{-- === Provinsi & Kabupaten === --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Provinsi</label>
                        <input type="text" value="Jawa Timur" class="form-control bg-light" readonly>
                        <input type="hidden" name="id_provinsi" value="{{ $kejadian->id_provinsi ?? 1 }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kabupaten</label>
                        <input type="text" value="Kabupaten Malang" class="form-control bg-light" readonly>
                        <input type="hidden" name="id_kabupaten" value="{{ $kejadian->id_kabupaten ?? 1 }}">
                    </div>
                </div>

                {{-- Kecamatan & Desa --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kecamatan</label>
                        <select name="id_kecamatan" id="kecamatan" class="form-select" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach($kecamatan as $kec)
                                <option value="{{ $kec->id_kecamatan }}" {{ $kejadian->id_kecamatan == $kec->id_kecamatan ? 'selected' : '' }}>
                                    {{ $kec->kecamatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Desa</label>
                        <select name="id_desa" id="desa" class="form-select" required>
                            <option value="">-- Pilih Desa --</option>
                            @foreach($desa as $d)
                                <option value="{{ $d->id_desa }}" 
                                        data-lat="{{ $d->latitude }}" 
                                        data-lon="{{ $d->longitude }}"
                                        {{ $kejadian->id_desa == $d->id_desa ? 'selected' : '' }}>
                                    {{ $d->desa }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- AJAX Script for Desa --}}
                <script>
                $(document).ready(function() {
                    // Saat kecamatan berubah
                    $('#kecamatan').on('change', function () {
                        var kecamatanId = $(this).val();
                        var desaSelect = $('#desa');
                        desaSelect.empty().append('<option value="">-- Pilih Desa --</option>');

                        if (kecamatanId) {
                            $.ajax({
                                url: '/get-tb_desa/' + kecamatanId,
                                type: 'GET',
                                dataType: 'json',
                                success: function (data) {
                                    if (data.length > 0) {
                                        $.each(data, function (key, desa) {
                                            var selected = ({{ $kejadian->id_desa }} == desa.id_desa) ? 'selected' : '';
                                            desaSelect.append(
                                                `<option value="${desa.id_desa}" 
                                                        data-lat="${desa.latitude}" 
                                                        data-lon="${desa.longitude}"
                                                        ${selected}>
                                                    ${desa.desa}
                                                </option>`
                                            );
                                        });
                                    } else {
                                        desaSelect.append('<option value="">Tidak ada desa di kecamatan ini</option>');
                                    }
                                }
                            });
                        }
                    });

                    // Saat desa dipilih
                    $('#desa').on('change', function () {
                        var selected = $(this).find(':selected');
                        var lat = selected.data('lat') || '';
                        var lon = selected.data('lon') || '';

                        $('#latitude').val(lat);
                        $('#longitude').val(lon);
                        
                        // Update map marker
                        if (window.marker && window.map) {
                            window.map.setView([lat, lon], 13);
                            window.marker.setLatLng([lat, lon]);
                            window.marker.bindPopup("Lokasi: Lat: " + lat + ", Lon: " + lon).openPopup();
                        }
                    });
                });
                </script>

                {{-- Alamat --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="form-control">{{ old('alamat', $kejadian->alamat) }}</textarea>
                </div>

                {{-- Peta Lokasi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Lokasi di Peta</label>
                    <div id="map" style="height: 350px; border-radius: 10px;" class="border"></div>
                </div>

                {{-- Map Script --}}
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Koordinat dari database atau default
                    var currentLat = {{ $kejadian->latitude ?? -7.9797 }};
                    var currentLon = {{ $kejadian->longitude ?? 112.6304 }};

                    // Inisialisasi peta
                    var map = L.map('map').setView([currentLat, currentLon], 13);

                    // Tambahkan layer tile (peta dasar)
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(map);

                    // Marker awal
                    var marker = L.marker([currentLat, currentLon]).addTo(map)
                        .bindPopup("Lokasi saat ini:<br>Lat: " + currentLat + "<br>Lon: " + currentLon)
                        .openPopup();

                    // Simpan ke window object untuk akses global
                    window.map = map;
                    window.marker = marker;

                    // Event klik pada peta
                    map.on('click', function(e) {
                        var lat = e.latlng.lat.toFixed(6);
                        var lon = e.latlng.lng.toFixed(6);

                        // Isi input latitude & longitude
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lon;

                        // Pindahkan marker
                        marker.setLatLng([lat, lon])
                            .bindPopup("Lokasi dipilih:<br>Lat: " + lat + "<br>Lon: " + lon)
                            .openPopup();
                    });
                });
                </script>

                {{-- Latitude & Longitude --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control" value="{{ old('latitude', $kejadian->latitude) }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control" value="{{ old('longitude', $kejadian->longitude) }}" readonly>
                    </div>
                </div>

                {{-- Penyebab --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Penyebab</label>
                    <textarea name="penyebab" class="form-control" rows="2">{{ old('penyebab', $kejadian->penyebab) }}</textarea>
                </div>

                {{-- Kronologi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kronologi</label>
                    <textarea name="kronologi" class="form-control" rows="2">{{ old('kronologi', $kejadian->kronologi) }}</textarea>
                </div>

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $kejadian->deskripsi) }}</textarea>
                </div>

                {{-- Sumber --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sumber</label>
                    <input type="text" name="sumber" class="form-control" value="{{ old('sumber', $kejadian->sumber) }}">
                </div>

                {{-- Kondisi Mutakhir --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kondisi Mutakhir</label>
                    <textarea name="kondisi_mutakhir" class="form-control">{{ old('kondisi_mutakhir', $kejadian->kondisi_mutakhir) }}</textarea>
                </div>

                {{-- Status Darurat --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status Darurat</label>
                    <select name="id_status_darurat" class="form-select">
                        <option value="">-- Penetapan Status --</option>
                        @foreach($statusDarurat as $status)
                            <option value="{{ $status->id_status_darurat }}" {{ $kejadian->id_status_darurat == $status->id_status_darurat ? 'selected' : '' }}>
                                {{ $status->status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Upaya --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upaya</label>
                    <textarea name="upaya" class="form-control" rows="2">{{ old('upaya', $kejadian->upaya) }}</textarea>
                </div>

                {{-- Logistik --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Logistik</label>
                    <input type="text" name="logistik" class="form-control" value="{{ old('logistik', $kejadian->logistik) }}">
                </div>

                {{-- Dokumentasi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Dokumentasi</label>
                    <input type="file" name="dokumentasi[]" class="form-control" multiple accept="image/*,video/*">
                    <div class="form-text text-muted">
                        Unggah foto atau video tambahan. Bisa memilih lebih dari satu file.
                    </div>
                    @if($kejadian->dokumentasi)
                        @php
                            $existingDocs = json_decode($kejadian->dokumentasi, true) ?? [];
                        @endphp
                        @if(!empty($existingDocs))
                            <div class="mt-2">
                                <small class="text-muted">Dokumentasi saat ini:</small>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @foreach($existingDocs as $index => $doc)
                                        <div class="position-relative">
                                            <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="badge bg-primary text-decoration-none">
                                                File {{ $index + 1 }}
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger remove-doc" data-index="{{ $index }}" style="position: absolute; top: -8px; right: -8px; padding: 0; width: 16px; height: 16px; font-size: 10px; line-height: 1;">
                                                ×
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" id="removed_docs" name="removed_docs" value="">
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Script untuk hapus dokumentasi --}}
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const removedDocs = [];
                    
                    document.addEventListener('click', function(e) {
                        if (e.target.classList.contains('remove-doc')) {
                            const index = e.target.getAttribute('data-index');
                            removedDocs.push(index);
                            document.getElementById('removed_docs').value = JSON.stringify(removedDocs);
                            e.target.closest('div').remove();
                        }
                    });
                });
                </script>

                {{-- Sebaran Dampak --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sebaran Dampak</label>
                    <textarea name="sebaran_dampak" class="form-control" rows="2">{{ old('sebaran_dampak', $kejadian->sebaran_dampak) }}</textarea>
                </div>

                <input type="hidden" id="index_kejadian" name="index_kejadian" value="{{ $kejadian->index_kejadian }}">

                {{-- Kode Indeks Bencana (KIB) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Indeks Bencana (KIB)</label>
                    <div class="input-group">
                        <input type="text" name="kib" id="kib" class="form-control bg-light" value="{{ old('kib', $kejadian->kib) }}" readonly>
                        <button type="button" id="generate-kib" class="btn btn-outline-primary">Regenerate</button>
                    </div>
                </div>

                {{-- Script untuk KIB --}}
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const jenisSelect = document.getElementById('id_jenis_bencana');
                    const tanggalInput = document.getElementById('tanggal');
                    const kibInput = document.getElementById('kib');
                    const indexInput = document.getElementById('index_kejadian');
                    const generateBtn = document.getElementById('generate-kib');

                    // Fungsi pembentuk kode KIB
                    function buildKIB(jenis, tanggal, index) {
                        const provinsi = '35';  // Jawa Timur
                        const kabupaten = '07'; // Kabupaten Malang
                        const jenisFormatted = jenis.toString().padStart(3, '0');
                        const tanggalFormatted = tanggal.replaceAll('-', '');
                        const indexFormatted = index.toString().padStart(2, '0');

                        return `${provinsi}${kabupaten}${jenisFormatted}${tanggalFormatted}${indexFormatted}`;
                    }

                    // Fungsi utama generator
                    async function generateKIB() {
                        const jenis = jenisSelect.value;
                        const tanggal = tanggalInput.value;

                        if (!jenis || !tanggal) {
                            alert('Pilih jenis bencana dan tanggal terlebih dahulu!');
                            return;
                        }

                        let index = {{ $kejadian->index_kejadian ?? 1 }};
                        
                        // Jika tanggal berubah, hitung index baru
                        if (tanggal !== '{{ $kejadian->tanggal }}') {
                            try {
                                const res = await fetch(`{{ route('kejadian.getIndex') }}?tanggal=${tanggal}`);
                                if (!res.ok) throw new Error('HTTP ' + res.status);

                                const data = await res.json();
                                if (data.index) index = data.index;
                            } catch (error) {
                                console.warn('⚠️ Gagal ambil index dari server, gunakan index existing');
                            }
                        }

                        indexInput.value = index;
                        kibInput.value = buildKIB(jenis, tanggal, index);
                    }

                    // Tombol generate
                    generateBtn.addEventListener('click', generateKIB);
                });
                </script>

                {{-- Unsur --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unsur yang Terlibat</label>
                    <textarea name="unsur" class="form-control" rows="2">{{ old('unsur', $kejadian->unsur) }}</textarea>
                </div>

                {{-- Data Korban --}}
<hr>
<h5 class="fw-bold text-primary mb-3">Data Korban</h5>
<div id="korban-wrapper">
    @if(count($kejadian->korban) > 0)
        @foreach($kejadian->korban as $index => $korban)
        <div class="border p-3 mb-3 rounded-3 bg-light korban-item">
            <div class="mb-3">
                <label class="form-label">Kategori Korban</label>
                <select name="korban[{{ $index }}][id_kategori_korban]" class="form-select">
                    @foreach($kategoriKorban as $k)
                        <option value="{{ $k->id_kategori_korban }}" {{ $korban->id_kategori_korban == $k->id_kategori_korban ? 'selected' : '' }}>
                            {{ $k->kategori_korban }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Kategori Umur</label>
                <select name="korban[{{ $index }}][id_kategori_umur]" class="form-select">
                    @foreach($kategoriUmur as $u)
                        <option value="{{ $u->id_kategori_umur }}" {{ $korban->id_kategori_umur == $u->id_kategori_umur ? 'selected' : '' }}>
                            {{ $u->kategori_umur }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col">
                    <label class="form-label">Laki-laki</label>
                    <input type="number" name="korban[{{ $index }}][L]" class="form-control" value="{{ $korban->L }}" min="0">
                </div>
                <div class="col">
                    <label class="form-label">Perempuan</label>
                    <input type="number" name="korban[{{ $index }}][P]" class="form-control" value="{{ $korban->P }}" min="0">
                </div>
            </div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-korban">Hapus</button>
            </div>
        </div>
        @endforeach
    @else
    {{-- Template default jika tidak ada data korban --}}
    <div class="border p-3 mb-3 rounded-3 bg-light korban-item">
        <div class="mb-3">
            <label class="form-label">Kategori Korban</label>
            <select name="korban[0][id_kategori_korban]" class="form-select">
                <option value="">-- Pilih Kategori Korban --</option>
                @foreach($kategoriKorban as $k)
                    <option value="{{ $k->id_kategori_korban }}">{{ $k->kategori_korban }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori Umur</label>
            <select name="korban[0][id_kategori_umur]" class="form-select">
                <option value="">-- Pilih Kategori Umur --</option>
                @foreach($kategoriUmur as $u)
                    <option value="{{ $u->id_kategori_umur }}">{{ $u->kategori_umur }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col">
                <label class="form-label">Laki-laki</label>
                <input type="number" name="korban[0][L]" class="form-control" value="0" min="0">
            </div>
            <div class="col">
                <label class="form-label">Perempuan</label>
                <input type="number" name="korban[0][P]" class="form-control" value="0" min="0">
            </div>
        </div>
        <div class="text-end mt-2">
            <button type="button" class="btn btn-outline-danger btn-sm remove-korban">Hapus</button>
        </div>
    </div>
    @endif
</div>

<div class="text-end mb-3">
    <button type="button" id="add-korban" class="btn btn-success btn-sm">
        <i class="bi bi-person-plus"></i> Tambah Korban
    </button>
</div>

{{-- Script untuk tambah/hapus korban --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Template untuk korban baru
    function getKorbanTemplate(index) {
        return `
        <div class="border p-3 mb-3 rounded-3 bg-light korban-item">
            <div class="mb-3">
                <label class="form-label">Kategori Korban</label>
                <select name="korban[${index}][id_kategori_korban]" class="form-select">
                    <option value="">-- Pilih Kategori Korban --</option>
                    @foreach($kategoriKorban as $k)
                        <option value="{{ $k->id_kategori_korban }}">{{ $k->kategori_korban }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Kategori Umur</label>
                <select name="korban[${index}][id_kategori_umur]" class="form-select">
                    <option value="">-- Pilih Kategori Umur --</option>
                    @foreach($kategoriUmur as $u)
                        <option value="{{ $u->id_kategori_umur }}">{{ $u->kategori_umur }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col">
                    <label class="form-label">Laki-laki</label>
                    <input type="number" name="korban[${index}][L]" class="form-control" value="0" min="0">
                </div>
                <div class="col">
                    <label class="form-label">Perempuan</label>
                    <input type="number" name="korban[${index}][P]" class="form-control" value="0" min="0">
                </div>
            </div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-korban">Hapus</button>
            </div>
        </div>
        `;
    }

    // Tambah korban
    document.getElementById('add-korban').addEventListener('click', function() {
        let wrapper = document.getElementById('korban-wrapper');
        let count = wrapper.querySelectorAll('.korban-item').length;
        
        // Tambah item baru dengan template
        const newItem = document.createElement('div');
        newItem.innerHTML = getKorbanTemplate(count);
        wrapper.appendChild(newItem);
    });

    // Hapus korban (gunakan event delegation)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-korban')) {
            const item = e.target.closest('.korban-item');
            // Cegah hapus semua jika hanya tersisa satu
            if (document.querySelectorAll('.korban-item').length > 1) {
                item.remove();
            } else {
                alert('Minimal harus ada satu data korban');
            }
        }
    });
});
</script>

                {{-- === Data Rumah === --}}
                <div class="card shadow-sm border-0 rounded-3 mt-4">
                    <div class="card-header bg-light fw-semibold">
                        🏠 Data Rumah
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold">Rusak Ringan</label>
                                <input type="number" name="rmh_rr" class="form-control" value="{{ old('rmh_rr', $kejadian->rumah->rmh_rr ?? 0) }}" min="0">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold">Rusak Sedang</label>
                                <input type="number" name="rmh_rs" class="form-control" value="{{ old('rmh_rs', $kejadian->rumah->rmh_rs ?? 0) }}" min="0">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold">Rusak Berat</label>
                                <input type="number" name="rmh_rb" class="form-control" value="{{ old('rmh_rb', $kejadian->rumah->rmh_rb ?? 0) }}" min="0">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold">Tenggelam</label>
                                <input type="number" name="terendam" class="form-control" value="{{ old('terendam', $kejadian->rumah->terendam ?? 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === Kerusakan Infrastruktur / Sosial Ekonomi === --}}
                <div class="card shadow-sm border-0 rounded-3 mt-4">
                    <div class="card-header bg-light fw-semibold">
                        🏗️ Kerusakan Infrastruktur
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select name="id_jenis_kerusakan_sosek" class="form-select">
                                    <option value="">Pilih Jenis</option>
                                    @foreach($jenisKerusakan2 as $jk2)
                                        <option value="{{ $jk2->id_jenis_kerusakan_sosek }}" {{ ($sosek->id_jenis_kerusakan_sosek ?? '') == $jk2->id_jenis_kerusakan_sosek ? 'selected' : '' }}>
                                            {{ $jk2->jenis_kerusakan_sosek }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sosek[luas]" class="form-control" placeholder="Luas" value="{{ old('sosek.luas', $sosek->luas ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sosek[rr]" class="form-control" placeholder="Rusak Ringan" value="{{ old('sosek.rr', $sosek->sosek_rr ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sosek[rs]" class="form-control" placeholder="Rusak Sedang" value="{{ old('sosek.rs', $sosek->sosek_rs ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sosek[rb]" class="form-control" placeholder="Rusak Berat" value="{{ old('sosek.rb', $sosek->sosek_rb ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sosek[terendam]" class="form-control" placeholder="Tenggelam" value="{{ old('sosek.terendam', $sosek->sosek_terendam ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === Kerusakan Fasilitas Umum === --}}
                <div class="card shadow-sm border-0 rounded-3 mt-4">
                    <div class="card-header bg-light fw-semibold">
                        🏢 Kerusakan Fasilitas Umum
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="id_jenis_kerusakan_sarpras" class="form-select">
                                    <option value="">Pilih Jenis</option>
                                    @foreach($jenisKerusakan3 as $jk3)
                                        <option value="{{ $jk3->id_jenis_kerusakan_sarpras }}" {{ ($sarpras->id_jenis_kerusakan_sarpras ?? '') == $jk3->id_jenis_kerusakan_sarpras ? 'selected' : '' }}>
                                            {{ $jk3->jenis_kerusakan_sarpras }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sarpras[rr]" class="form-control" placeholder="Rusak Ringan" value="{{ old('sarpras.rr', $sarpras->sarpras_rr ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sarpras[rs]" class="form-control" placeholder="Rusak Sedang" value="{{ old('sarpras.rs', $sarpras->sarpras_rs ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sarpras[rb]" class="form-control" placeholder="Rusak Berat" value="{{ old('sarpras.rb', $sarpras->sarpras_rb ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="sarpras[terendam]" class="form-control" placeholder="Tenggelam" value="{{ old('sarpras.terendam', $sarpras->sarpras_terendam ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === Kerusakan Fasilitas Pendidikan === --}}
                <div class="card shadow-sm border-0 rounded-3 mt-4 mb-4">
                    <div class="card-header bg-light fw-semibold">
                        🎓 Kerusakan Fasilitas Pendidikan
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select name="id_jenis_kerusakan_pelayanandasar" class="form-select">
                                    <option value="">Pilih Jenis</option>
                                    @foreach($jenisKerusakan as $jk)
                                        <option value="{{ $jk->id_jenis_kerusakan_pelayanandasar }}" {{ ($pelayanan->id_jenis_kerusakan_pelayanandasar ?? '') == $jk->id_jenis_kerusakan_pelayanandasar ? 'selected' : '' }}>
                                            {{ $jk->jenis_kerusakan_pelayanandasar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="pelayanan_rr" class="form-control" placeholder="Rusak Ringan" value="{{ old('pelayanan_rr', $pelayanan->pelayanan_rr ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="pelayanan_rs" class="form-control" placeholder="Rusak Sedang" value="{{ old('pelayanan_rs', $pelayanan->pelayanan_rs ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="pelayanan_rb" class="form-control" placeholder="Rusak Berat" value="{{ old('pelayanan_rb', $pelayanan->pelayanan_rb ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="pelayanan_terendam" class="form-control" placeholder="Tenggelam" value="{{ old('pelayanan_terendam', $pelayanan->pelayanan_terendam ?? 0) }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="taksiran" class="form-control" placeholder="Taksiran (Rp)" value="{{ old('taksiran', $pelayanan->taksiran ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === Petugas Piket === --}}
                <div class="card mt-3 shadow-sm">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-person-badge"></i> Petugas Piket
                    </div>
                    <div class="card-body">
                        <label class="form-label">Daftar Petugas</label>

                        <div id="pengawas-container">
                            @php
                                $existingPengawas = $kejadian->nip_pengawas ? explode(',', $kejadian->nip_pengawas) : [];
                            @endphp
                            @foreach($existingPengawas as $index => $nip)
                            <div class="pengawas-item row g-2 mb-2 align-items-center">
                                <div class="col-md-10">
                                    <select name="nip_pengawas[]" class="form-select">
                                        <option value="">-- Pilih Petugas Piket --</option>
                                        @foreach($pengawas as $p)
                                            <option value="{{ $p->nip_pengawas }}" {{ $nip == $p->nip_pengawas ? 'selected' : '' }}>
                                                {{ $p->nama_pengawas }} ({{ $p->jabatan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-danger w-100 hapus-btn">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                            @endforeach
                            @if(count($existingPengawas) === 0)
                            <div class="pengawas-item row g-2 mb-2 align-items-center">
                                <div class="col-md-10">
                                    <select name="nip_pengawas[]" class="form-select">
                                        <option value="">-- Pilih Petugas Piket --</option>
                                        @foreach($pengawas as $p)
                                            <option value="{{ $p->nip_pengawas }}">{{ $p->nama_pengawas }} ({{ $p->jabatan }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-danger w-100 hapus-btn">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>

                        <button type="button" id="tambah-pengawas" class="btn btn-outline-primary mt-2">
                            <i class="bi bi-person-plus"></i> Tambah Petugas Piket
                        </button>
                    </div>
                </div>

                {{-- === SCRIPT === --}}
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('pengawas-container');
                    const tambahBtn = document.getElementById('tambah-pengawas');

                    tambahBtn.addEventListener('click', function() {
                        const newItem = document.createElement('div');
                        newItem.classList.add('pengawas-item', 'row', 'g-2', 'mb-2', 'align-items-center');

                        newItem.innerHTML = `
                            <div class="col-md-10">
                                <select name="nip_pengawas[]" class="form-select">
                                    <option value="">-- Pilih Petugas Piket --</option>
                                    @foreach($pengawas as $p)
                                        <option value="{{ $p->nip_pengawas }}">{{ $p->nama_pengawas }} ({{ $p->jabatan }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-danger w-100 hapus-btn">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        `;
                        container.appendChild(newItem);
                    });

                    container.addEventListener('click', function(e) {
                        if (e.target.closest('.hapus-btn')) {
                            e.target.closest('.pengawas-item').remove();
                        }
                    });
                });
                </script>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('kejadian') }}" class="btn btn-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="bi bi-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('formKejadian').addEventListener('submit', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Ingin mengupdate data ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Iya, Update',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            confirmButton: 'bg-green-600 text-white px-4 py-2 rounded mx-2',
            cancelButton: 'bg-red-600 text-white px-4 py-2 rounded mx-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});
</script>

@endsection