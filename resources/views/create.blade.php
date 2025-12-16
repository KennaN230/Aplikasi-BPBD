    @extends('layouts.app')

    @section('title', 'Tambah Kejadian')

    @section('content')
    <!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="container my-4">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-plus-circle"></i> Tambah Kejadian
        </div>
        <div class="card-body">
            <form id="formKejadian" action="{{ route('kejadian.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- === Jenis Bencana === --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis Bencana</label>
                    <select name="id_jenis_bencana" id="id_jenis_bencana" class="form-select" required>
                        <option value="">-- Pilih Jenis Bencana --</option>
                        @foreach($jenisBencana as $jb)
                            <option value="{{ $jb->id_jenis_bencana }}">{{ $jb->jenis_bencana }}</option>
                        @endforeach
                    </select>
                </div>

    {{-- === Nama Kejadian Manual === --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Nama Kejadian</label>
    <input type="text" name="nama_kejadian" 
           class="form-control" 
           placeholder="Contoh: Banjir Bandang, Longsor, Puting Beliung" 
           required>
    <small class="text-muted">Masukkan nama kejadian secara manual.</small>
</div>


                {{-- === Tanggal & Waktu === --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Waktu</label>
                        <input type="time" name="waktu" class="form-control">
                    </div>
                </div>

            {{-- === Provinsi & Kabupaten === --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Provinsi</label>
                        <input type="text" value="{{ $provinsi->nama_provinsi ?? 'Jawa Timur' }}" class="form-control bg-light" readonly>
                        <input type="hidden" name="id_provinsi" value="{{ $provinsi->id_provinsi ?? 1 }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kabupaten</label>
                        <input type="text" value="{{ $kabupaten->nama_kabupaten ?? 'Kabupaten Malang' }}" class="form-control bg-light" readonly>
                        <input type="hidden" name="id_kabupaten" value="{{ $kabupaten->id_kabupaten ?? 1 }}">
                    </div>
                </div>

            {{-- Kecamatan & Desa --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kecamatan</label>
                        <select name="id_kecamatan" id="kecamatan" class="form-select" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach($kecamatan as $kec)
                                <option value="{{ $kec->id_kecamatan }}">{{ $kec->kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Desa</label>
                        <select name="id_desa" id="desa" class="form-select" required>
                            <option value="">-- Pilih Desa --</option>
                        </select>
                    </div>
                </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {

        // Saat kecamatan berubah
        $('#kecamatan').on('change', function () {
            var kecamatanId = $(this).val();
            var desaSelect = $('#desa');
            desaSelect.empty().append('<option value="">-- Pilih Desa --</option>');
            $('#latitude').val('');
            $('#longitude').val('');

            if (kecamatanId) {
                $.ajax({
                    url: '/get-tb_desa/' + kecamatanId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.length > 0) {
                            $.each(data, function (key, desa) {
                                desaSelect.append(
                                    `<option value="${desa.id_desa}" 
                                            data-lat="${desa.latitude}" 
                                            data-lon="${desa.longitude}">
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
        });

    });
    </script>

    {{-- Alamat --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="form-control">{{ old('alamat', $kejadian->alamat ?? '') }}</textarea>
                </div>
{{-- Peta Lokasi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Lokasi di Peta</label>
                    <div id="map" style="height: 350px; border-radius: 10px;" class="border"></div>
                </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Koordinat awal (Malang sebagai contoh)
    var defaultLat = -7.9797;
    var defaultLon = 112.6304;

    // Inisialisasi peta
    var map = L.map('map').setView([defaultLat, defaultLon], 11);

    // Tambahkan layer tile (peta dasar)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Marker (kosong dulu)
    var marker;

    // Event klik pada peta
    map.on('click', function(e) {
        var lat = e.latlng.lat.toFixed(6);
        var lon = e.latlng.lng.toFixed(6);

        // Isi input latitude & longitude
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;

        // Hapus marker lama jika ada
        if (marker) {
            map.removeLayer(marker);
        }

        // Tambah marker baru di titik yang diklik
        marker = L.marker([lat, lon]).addTo(map)
            .bindPopup("Lokasi dipilih:<br>Lat: " + lat + "<br>Lon: " + lon)
            .openPopup();
    });
});
</script>

    {{-- Latitude & Longitude --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control" readonly>
                    </div>
                </div>

           {{-- Penyebab --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Penyebab</label>
                    <textarea name="penyebab" class="form-control" rows="2"></textarea>
                </div>

                {{-- Kronologi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kronologi</label>
                    <textarea name="kronologi" class="form-control" rows="2"></textarea>
                </div>

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                </div>

                {{-- Sumber --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sumber</label>
                    <input type="text" name="sumber" class="form-control">
                </div>

                {{-- Kondisi Mutakhir --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kondisi Mutakhir</label>
                    <textarea name="kondisi_mutakhir" class="form-control"></textarea>
                </div>

            {{-- Status Darurat --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status Darurat</label>
                    <select name="id_status_darurat" class="form-select">
                        <option value="">-- Penetapan Status --</option>
                        @foreach($statusDarurat as $status)
                            <option value="{{ $status->id_status_darurat }}">{{ $status->status }}</option>
                        @endforeach
                    </select>
                </div>

            {{-- Upaya --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Upaya</label>
    <textarea name="upaya" class="form-control" rows="2" placeholder=""></textarea>
</div>

{{-- Logistik --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Logistik</label>
    <input type="text" name="logistik" class="form-control" placeholder="">
</div>

{{-- Dokumentasi --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Dokumentasi</label>
    <input type="file" name="dokumentasi[]" class="form-control" multiple accept="image/*,video/*">
    <div class="form-text text-muted">
        Unggah foto atau video. Bisa memilih lebih dari satu file.
    </div>
</div>

{{-- Sebaran Dampak --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Sebaran Dampak</label>
    <textarea name="sebaran_dampak" class="form-control" rows="2" placeholder="."></textarea>
</div>


            <input type="hidden" id="index_kejadian" name="index_kejadian" value="">

            {{-- Kode Indeks Bencana (KIB) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Indeks Bencana (KIB)</label>
                    <div class="input-group">
                        <input type="text" name="kib" id="kib" class="form-control bg-light" readonly>
                        <button type="button" id="generate-kib" class="btn btn-outline-primary">Generate</button>
                    </div>
                </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisSelect = document.getElementById('id_jenis_bencana');
    const tanggalInput = document.getElementById('tanggal');
    const kibInput = document.getElementById('kib');
    const indexInput = document.getElementById('index_kejadian');

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
            kibInput.value = '';
            indexInput.value = '';
            return;
        }

        let index = 1; // Default dummy mulai dari 1
        try {
            const res = await fetch(`{{ route('kejadian.getIndex') }}?tanggal=${tanggal}`);
            if (!res.ok) throw new Error('HTTP ' + res.status);

            const data = await res.json();
            if (data.index) index = data.index;
        } catch (error) {
            console.warn('⚠️ Gagal ambil index dari server, gunakan index dummy = 1');
            index = 1;
        }

        indexInput.value = index;
        kibInput.value = buildKIB(jenis, tanggal, index);
    }

    // Generate otomatis saat tanggal atau jenis berubah
    jenisSelect.addEventListener('change', generateKIB);
    tanggalInput.addEventListener('change', generateKIB);
});
</script>

            {{-- Unsur --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unsur yang Terlibat</label>
                    <textarea name="unsur" class="form-control" rows="2"></textarea>
                </div>

            {{-- Data Korban --}}
                <hr>
                <h5 class="fw-bold text-primary mb-3">Data Korban</h5>
                <div id="korban-wrapper">
                    <div class="border p-3 mb-3 rounded-3 bg-light korban-item">
                        <div class="mb-3">
                            <label class="form-label">Kategori Korban</label>
                            <select name="korban[0][id_kategori_korban]" class="form-select">
                                @foreach($kategoriKorban as $k)
                                    <option value="{{ $k->id_kategori_korban }}">{{ $k->kategori_korban }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori Umur</label>
                            <select name="korban[0][id_kategori_umur]" class="form-select">
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
                    </div>
                </div>

                <div class="text-end mb-3">
                    <button type="button" id="add-korban" class="btn btn-success btn-sm">
                        <i class="bi bi-person-plus"></i> Tambah Korban
                    </button>
                </div>

    {{-- === Data Rumah === --}}
<div class="card shadow-sm border-0 rounded-3 mt-4">
    <div class="card-header bg-light fw-semibold">
        🏠 Data Rumah
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <label class="form-label fw-semibold">Rusak Ringan</label>
                <input type="number" name="rmh_rr" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label fw-semibold">Rusak Sedang</label>
                <input type="number" name="rmh_rs" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label fw-semibold">Rusak Berat</label>
                <input type="number" name="rmh_rb" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label fw-semibold">Tenggelam</label>
                <input type="number" name="terendam" class="form-control" value="0" min="0">
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
                <select name="id_jenis_kerusakan_sosek" class="form-select" required>
                    <option disabled selected>Pilih Jenis</option>
                    @foreach($jenisKerusakan2 as $jk2)
                        <option value="{{ $jk2->id_jenis_kerusakan_sosek }}">{{ $jk2->jenis_kerusakan_sosek }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="sosek[luas]" class="form-control" placeholder="Luas">
            </div>
            <div class="col-md-2">
                <input type="number" name="sosek[rr]" class="form-control" placeholder="Rusak Ringan">
            </div>
            <div class="col-md-2">
                <input type="number" name="sosek[rs]" class="form-control" placeholder="Rusak Sedang">
            </div>
            <div class="col-md-2">
                <input type="number" name="sosek[rb]" class="form-control" placeholder="Rusak Berat">
            </div>
            <div class="col-md-2">
                <input type="number" name="sosek[terendam]" class="form-control" placeholder="Tenggelam">
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
                <select name="id_jenis_kerusakan_sarpras" class="form-select" required>
                    <option disabled selected>Pilih Jenis</option>
                    @foreach($jenisKerusakan3 as $jk3)
                        <option value="{{ $jk3->id_jenis_kerusakan_sarpras }}">{{ $jk3->jenis_kerusakan_sarpras }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="sarpras[rr]" class="form-control" placeholder="Rusak Ringan">
            </div>
            <div class="col-md-2">
                <input type="number" name="sarpras[rs]" class="form-control" placeholder="Rusak Sedang">
            </div>
            <div class="col-md-2">
                <input type="number" name="sarpras[rb]" class="form-control" placeholder="Rusak Berat">
            </div>
            <div class="col-md-2">
                <input type="number" name="sarpras[terendam]" class="form-control" placeholder="Tenggelam">
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
                <select name="id_jenis_kerusakan_pelayanandasar" class="form-select" required>
                    <option disabled selected>Pilih Jenis</option>
                    @foreach($jenisKerusakan as $jk)
                        <option value="{{ $jk->id_jenis_kerusakan_pelayanandasar }}">{{ $jk->jenis_kerusakan_pelayanandasar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="pelayanan_rr" class="form-control" placeholder="Rusak Ringan">
            </div>
            <div class="col-md-2">
                <input type="number" name="pelayanan_rs" class="form-control" placeholder="Rusak Sedang">
            </div>
            <div class="col-md-2">
                <input type="number" name="pelayanan_rb" class="form-control" placeholder="Rusak Berat">
            </div>
            <div class="col-md-2">
                <input type="number" name="pelayanan_terendam" class="form-control" placeholder="Tenggelam">
            </div>
            <div class="col-md-2">
                <input type="number" name="taksiran" class="form-control" placeholder="Taksiran (Rp)">
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
        <i class="bi bi-save"></i> Simpan
    </button>
</div>


    </div>
        </form>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
    document.getElementById('add-korban').addEventListener('click', function() {
        let wrapper = document.getElementById('korban-wrapper');
        let count = wrapper.querySelectorAll('.korban-item').length;

        let newItem = wrapper.querySelector('.korban-item').cloneNode(true);

        // update name index
        newItem.querySelectorAll('select, input').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${count}]`);
            el.value = el.type === "number" ? 0 : el.value;
        });

        wrapper.appendChild(newItem);
    });

    document.getElementById('formKejadian').addEventListener('submit', function(e) {
        e.preventDefault(); // cegah submit langsung

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Ingin menambahkan data ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            reverseButtons: true,
            customClass: {
                confirmButton: 'bg-green-600 text-white px-4 py-2 rounded mx-2',
                cancelButton: 'bg-red-600 text-white px-4 py-2 rounded mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit(); // submit form kalau user pilih "Iya"
            }
        });
    });
    </script>

    @endsection