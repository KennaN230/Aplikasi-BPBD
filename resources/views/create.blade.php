    @extends('layouts.app')

    @section('title', 'Tambah Kejadian')

    @section('content')
    <!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Tambah Kejadian</h2>

        <form id="formKejadian" action="{{ route('kejadian.store') }}" method="POST" enctype="multipart/form-data">
        @csrf


            {{-- === Pilih Jenis Bencana === --}}
    <div class="mb-4">
        <label class="block font-medium">Jenis Bencana</label>
        <select name="id_jenis_bencana" id="id_jenis_bencana" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Jenis Bencana --</option>
            @foreach($jenisBencana as $jb)
                <option value="{{ $jb->id_jenis_bencana }}">
                    {{ $jb->jenis_bencana }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- === Pilih Nama Kejadian === --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nama Kejadian</label>
        <select name="id_nama_kejadian" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Nama Kejadian --</option>
            @foreach($namaKejadian as $nk)
                <option value="{{ $nk->id_nama_kejadian }}">
                    {{ $nk->nama_kejadian }}
                </option>
            @endforeach
        </select>
    </div>



            {{-- tanggal --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" class="w-full border rounded px-3 py-2"> 
            </div>

            {{-- waktu --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Waktu</label>
                <input type="time" name="waktu" class="w-full border rounded px-3 py-2">
            </div>

            {{-- Provinsi --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Provinsi</label>
        <input type="text"
            value="{{ $provinsi->nama_provinsi ?? 'Jawa Timur' }}"
            class="w-full border rounded px-3 py-2 bg-gray-100"
            readonly>
        <input type="hidden" name="id_provinsi" value="{{ $provinsi->id_provinsi ?? 1 }}">
    </div>

    {{-- Kabupaten --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Kabupaten</label>
        <input type="text"
            value="{{ $kabupaten->nama_kabupaten ?? 'Kabupaten Malang' }}"
            class="w-full border rounded px-3 py-2 bg-gray-100"
            readonly>
        <input type="hidden" name="id_kabupaten" value="{{ $kabupaten->id_kabupaten ?? 1 }}">
    </div>

            {{-- Kecamatan --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
        <select name="id_kecamatan" id="kecamatan" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Kecamatan --</option>
            @foreach($kecamatan as $kec)
                <option value="{{ $kec->id_kecamatan }}">{{ $kec->kecamatan }}</option>
            @endforeach
        </select>
    </div>


    {{-- Desa --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Desa</label>
        <select name="id_desa" id="desa" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Desa --</option>
        </select>
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

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
        <textarea name="alamat" rows="2" class="w-full border rounded px-3 py-2">{{ old('alamat', $kejadian->alamat ?? '') }}</textarea>
    </div>
{{-- Peta Lokasi --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Lokasi di Peta</label>
    <div id="map" style="height: 300px; border-radius: 8px;"></div>
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

    {{-- Latitude --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Latitude</label>
        <input type="text" id="latitude" name="latitude" class="w-full border rounded px-3 py-2" readonly>
    </div>

    {{-- Longitude --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Longitude</label>
        <input type="text" id="longitude" name="longitude" class="w-full border rounded px-3 py-2" readonly>
    </div>

            {{-- penyebab --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Penyebab</label>
                <textarea name="penyebab" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>

            {{-- kronologi --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Kronologi</label>
                <textarea name="kronologi" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>

            {{-- deskripsi --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>

            {{-- sumber --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Sumber</label>
                <input type="text" name="sumber" class="w-full border rounded px-3 py-2">
            </div>

            {{-- kondisi_mutakhir --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Kondisi Mutakhir</label>
                <textarea name="kondisi_mutakhir" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            {{-- id_status_darurat --}}
            <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Status Darurat</label>
        <select name="id_status_darurat" class="w-full border rounded px-3 py-2">
            <option value="">-- Pilih Status Darurat --</option>
            @foreach($statusDarurat as $status)
                <option value="{{ $status->id_status_darurat }}">
                    {{ $status->status }}
                </option>
            @endforeach
        </select>
    </div>


            {{-- upaya --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Upaya</label>
                <textarea name="upaya" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>

            {{-- logistik --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Logistik</label>
                <input type="text" name="logistik" class="w-full border rounded px-3 py-2">
            </div>

            {{-- dokumentasi --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Dokumentasi</label>
                <input type="file" name="dokumentasi" class="w-full border rounded px-3 py-2">
            </div>

            {{-- sebaran_dampak --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Sebaran Dampak</label>
                <textarea name="sebaran_dampak" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>

            <input type="hidden" id="index_kejadian" name="index_kejadian" value="">

            {{-- kib --}}
            <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Indeks Bencana (KIB)</label>
    <div class="flex gap-2">
        <input type="text" name="kib" id="kib"
            readonly
            class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2 flex-1 bg-gray-100">
        <button type="button" id="generate-kib"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">
            Generate
        </button>
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
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Unsur yang terlibat</label>
                <textarea name="unsur" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>

            {{-- === Data Korban === --}}
    <h2 class="font-bold mt-6 mb-2">Data Korban</h2>

    <div id="korban-wrapper">
        <div class="korban-item border rounded p-3 mb-3">
            <div class="mb-4">
                <label class="block text-sm font-medium">Kategori Korban</label>
                <select name="korban[0][id_kategori_korban]" class="w-full border rounded px-3 py-2">
                    @foreach($kategoriKorban as $k)
                        <option value="{{ $k->id_kategori_korban }}">{{ $k->kategori_korban }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Kategori Umur</label>
                <select name="korban[0][id_kategori_umur]" class="w-full border rounded px-3 py-2">
                    @foreach($kategoriUmur as $u)
                        <option value="{{ $u->id_kategori_umur }}">{{ $u->kategori_umur }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Laki-laki</label>
                    <input type="number" name="korban[0][L]" class="w-full border rounded px-3 py-2" value="0" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium">Perempuan</label>
                    <input type="number" name="korban[0][P]" class="w-full border rounded px-3 py-2" value="0" min="0">
                </div>
            </div>
        </div>
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
                <input type="number" name="rmh_rr" class="w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg px-3 py-2" value="0" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rusak Sedang</label>
                <input type="number" name="rmh_rs" class="w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg px-3 py-2" value="0" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rusak Berat</label>
                <input type="number" name="rmh_rb" class="w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg px-3 py-2" value="0" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Terendam</label>
                <input type="number" name="terendam" class="w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg px-3 py-2" value="0" min="0">
            </div>
        </div>
    </div>

    {{-- === Kerusakan Sosial Ekonomi === --}}
    <div class="bg-white shadow rounded-xl p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏗️ Kerusakan Infrastruktur</h3>

        <div class="grid grid-cols-6 gap-3">
            <select name="id_jenis_kerusakan_sosek"
        class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
        <option disabled selected>Pilih Jenis</option>
        @foreach($jenisKerusakan2 as $jk2)
            <option value="{{ $jk2->id_jenis_kerusakan_sosek }}">
                {{ $jk2->jenis_kerusakan_sosek }}
            </option>
        @endforeach
    </select>

            <input type="number" name="sosek[luas]" placeholder="Luas" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="sosek[rr]" placeholder="Rusak Ringan" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="sosek[rs]" placeholder="Rusak Sedang" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="sosek[rb]" placeholder="Rusak Berat" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="sosek[terendam]" placeholder="Terendam" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
        </div>
    </div>

    {{-- === Kerusakan Fasilitas Umum === --}}
    <div class="bg-white shadow rounded-xl p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏢 Kerusakan Fasilitas Umum</h3>

        <div class="grid grid-cols-5 gap-3">
            {{-- Jenis Kerusakan --}}
            <select name="id_jenis_kerusakan_sarpras"
        class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
        <option disabled selected>Pilih Jenis</option>
        @foreach($jenisKerusakan3 as $jk3)
            <option value="{{ $jk3->id_jenis_kerusakan_sarpras }}">
                {{ $jk3->jenis_kerusakan_sarpras }}
            </option>
        @endforeach
    </select>

            {{-- Input jumlah kerusakan --}}
            <input type="number" name="sarpras[rr]" placeholder="Rusak Ringan"
                class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="sarpras[rs]" placeholder="Rusak Sedang"
                class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="sarpras[rb]" placeholder="Rusak Berat"
                class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="sarpras[terendam]" placeholder="Terendam"
                class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
        </div>
    </div>

    {{-- === Kerusakan Fasilitas Pendidikan === --}}
    <div class="bg-white shadow rounded-xl p-6 mt-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🎓 Kerusakan Fasilitas Pendidikan</h3>
        <div class="grid grid-cols-6 gap-3">
            <select name="id_jenis_kerusakan_pelayanandasar"
        class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
        <option disabled selected>Pilih Jenis</option>
        @foreach($jenisKerusakan as $jk)
            <option value="{{ $jk->id_jenis_kerusakan_pelayanandasar }}">
                {{ $jk->jenis_kerusakan_pelayanandasar }}
            </option>
        @endforeach
    </select>
            <input type="number" name="pelayanan_rr" placeholder="Rusak Ringan" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="pelayanan_rs" placeholder="Rusak Sedang" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="pelayanan_rb" placeholder="Rusak Berat" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="pelayanan_terendam" placeholder="Terendam" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
            <input type="number" name="taksiran" placeholder="Taksiran (Rp)" class="border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg p-2">
        </div>
    </div>

    {{-- === Pilih Pengawas Dinamis === --}}
    <div class="mb-4">
        <label class="block font-medium mb-2">Petugas Piket</label>

        <div id="pengawas-container">
            <div class="pengawas-item mb-2 flex gap-2">
                
    <select name="nip_pengawas" id="nip_pengawas" class="w-full border rounded px-3 py-2">
        <option value="">-- Pilih Petugas Piket --</option>
        @foreach($pengawas as $p)
            <option value="{{ $p->nip_pengawas }}">{{ $p->nama_pengawas }} ({{ $p->jabatan }})</option>
        @endforeach
    </select>

                <button type="button" class="hapus-btn bg-red-500 text-white px-3 py-2 rounded">Hapus</button>
            </div>
        </div>

        <button type="button" id="tambah-pengawas"
            class="mt-2 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
            + Tambah Petugas Piket
        </button>
    </div>

    {{-- === SCRIPT DINAMIS === --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('pengawas-container');
        const tambahBtn = document.getElementById('tambah-pengawas');

        tambahBtn.addEventListener('click', function() {
            const newItem = document.createElement('div');
            newItem.classList.add('pengawas-item', 'mb-2', 'flex', 'gap-2');

            newItem.innerHTML = `
                <select name="nip_pengawas[]" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Petugas Piket --</option>
                    @foreach($pengawas as $p)
                        <option value="{{ $p->nip_pengawas }}">{{ $p->nama_pengawas }} ({{ $p->nip_pengawas }})</option>
                    @endforeach
                </select>
                <button type="button" class="hapus-btn bg-red-500 text-white px-3 py-2 rounded">Hapus</button>
            `;
            container.appendChild(newItem);
        });

        // event delegation agar tombol hapus berfungsi untuk semua item
        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('hapus-btn')) {
                e.target.parentElement.remove();
            }
        });
    });
    </script>


            {{-- Tombol --}}
    <div class="flex justify-end gap-2">
        <a href="{{ route('kejadian') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        Simpan
    </button>

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


