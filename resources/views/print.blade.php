@extends('layouts.app')

@section('title', 'Laporan Kejadian Bencana')

@section('content')
<div class="container mx-auto my-6 px-4">

    {{-- Kop Surat --}}
    <div class="text-center mb-6 border-b-4 pb-3">
        <h2 class="text-xl font-bold">PEMERINTAH KABUPATEN MALANG</h2>
        <h3 class="text-lg font-semibold">BADAN PENANGGULANGAN BENCANA DAERAH</h3>
        <p class="text-sm">Jalan Trunojoyo Kepanjen, Kabupaten Malang, Jawa Timur</p>
        <p class="text-sm">Telepon/Faks: (0341) 392121 | Website: bpbd.malangkab.go.id | Email: bpbd@malangkab.go.id | Kode Pos: 65163</p>
    </div>

    <div class="text-center mb-6">
        <h2 class="text-lg font-bold underline">LAPORAN KEJADIAN BENCANA</h2>
        <p class="text-sm">Periode: {{ \Carbon\Carbon::parse($tanggal ?? now())->translatedFormat('l, d F Y') }}</p>
    </div>

    @forelse($kejadian as $k)
        @php
            $rumah = $k->rumah ?? (object)[
                'rmh_rb' => 0,
                'rmh_rs' => 0,
                'rmh_rr' => 0,
                'terendam' => 0
            ];
        @endphp

        {{-- Info Kejadian --}}
        <div class="mb-6">
            <table class="table-auto w-full border border-gray-300 text-sm">
                <tbody>
                    <tr class="bg-gray-100">
                        <th class="border px-3 py-2 text-left w-1/4">Jenis Kejadian</th>
                        <td class="border px-3 py-2">{{ $k->jenisBencana->nama_bencana ?? 'Banjir' }}</td>
                    </tr>
                    <tr>
                        <th class="border px-3 py-2 text-left">Nama Kejadian</th>
                        <td class="border px-3 py-2">{{ $k->namaKejadian->nama ?? 'Banjir Bandang' }}</td>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="border px-3 py-2 text-left">Tanggal & Waktu</th>
                        <td class="border px-3 py-2">{{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('l, d F Y') }} / {{ $k->waktu ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="border px-3 py-2 text-left">Lokasi</th>
                        <td class="border px-3 py-2">
                            {{ $k->desa->nama ?? 'Bantur' }}, {{ $k->kecamatan->nama ?? 'Bantur' }}, {{ $k->kabupaten->nama ?? 'Kabupaten Malang' }}
                        </td>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="border px-3 py-2 text-left">Koordinat</th>
                        <td class="border px-3 py-2">{{ $k->latitude ?? '-' }}, {{ $k->longitude ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="border px-3 py-2 text-left">Sumber Info</th>
                        <td class="border px-3 py-2">{{ $k->sumber_info ?? 'TRC BPBD Kab. Malang' }}</td>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="border px-3 py-2 text-left">Info Masuk</th>
                        <td class="border px-3 py-2">{{ \Carbon\Carbon::parse($k->info_masuk ?? now())->translatedFormat('l, d F Y H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <th class="border px-3 py-2 text-left">Kronologi</th>
                        <td class="border px-3 py-2">{{ $k->kronologi ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Dampak --}}
        <div class="mb-6">
            <h5 class="font-semibold mb-2">Dampak:</h5>
            <table class="table-auto w-full border border-gray-300 text-sm">
                <tbody>
                    <tr class="bg-gray-100">
                        <th class="border px-3 py-2">Rumah Rusak Berat</th>
                        <td class="border px-3 py-2 text-center">{{ $rumah->rmh_rb ?? 0 }}</td>
                        <th class="border px-3 py-2">Rumah Rusak Sedang</th>
                        <td class="border px-3 py-2 text-center">{{ $rumah->rmh_rs ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th class="border px-3 py-2">Rumah Rusak Ringan</th>
                        <td class="border px-3 py-2 text-center">{{ $rumah->rmh_rr ?? 0 }}</td>
                        <th class="border px-3 py-2">Rumah Terendam</th>
                        <td class="border px-3 py-2 text-center">{{ $rumah->terendam ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Korban --}}
        <div class="mb-6">
            <h5 class="font-semibold mb-2">Korban:</h5>
            <table class="table-auto w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2">Kondisi</th>
                        <th class="border px-3 py-2 text-center">L</th>
                        <th class="border px-3 py-2 text-center">P</th>
                        <th class="border px-3 py-2 text-center">Jumlah</th>
                    </tr>
                </thead> 
            </table>
        </div>

        {{-- Kebutuhan Mendesak --}}
        <div class="mb-6">
            <h5 class="font-semibold mb-2">Kebutuhan Mendesak:</h5>
            <ol class="list-decimal list-inside ml-2">
                <li>Logistik</li>
                <li>Peralatan Kebersihan</li>
            </ol>
        </div>

        {{-- Upaya --}}
        <div class="mb-6">
            <h5 class="font-semibold mb-2">Upaya yang Dilakukan:</h5>
            <ol class="list-decimal list-inside ml-2">
                <li>Berkoordinasi dengan pihak terkait</li>
                <li>TRC PB melaksanakan identifikasi, assessment dan pembersihan dampak kejadian</li>
                <li>Distribusi bantuan logistik kebutuhan dasar</li>
            </ol>
        </div>

        {{-- Kondisi Saat Ini --}}
        <div class="mb-6">
            <h5 class="font-semibold mb-2">Kondisi Saat Ini:</h5>
            <p>Air sudah surut</p>
        </div>

        {{-- Unsur Terlibat --}}
        <div class="mb-6">
            <h5 class="font-semibold mb-2">Unsur yang Terlibat:</h5>
            <ol class="list-decimal list-inside ml-2">
                <li>BPBD</li>
                <li>TNI</li>
                <li>POLRI</li>
                <li>Dinas PU Bina Marga Provinsi Jawa Timur</li>
                <li>Dinas PU Bina Marga Kabupaten Malang</li>
                <li>Pemerintah Desa {{ $k->desa->nama ?? '-' }}</li>
                <li>Pemerintah Kecamatan {{ $k->kecamatan->nama ?? '-' }}</li>
                <li>PMI Kabupaten Malang</li>
                <li>SAR Awangga</li>
                <li>Relawan setempat</li>
                <li>Masyarakat</li>
            </ol>
        </div>

        {{-- Tanda Tangan --}}
        <div class="text-right my-6">
            <p>Kepanjen, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Bidang Pencegahan dan Kesiapsiagaan</p>
            <p>Manajer Pusdalops</p>
            <br><br><br>
            <p><u>ZAINUDDIN, S.H</u></p>
            <p>Penata Tingkat 1</p>
            <p>NIP. 19650101199001 1001</p>
        </div>

        <hr class="border-dashed border-2 my-8">

    @empty
        <p class="text-center text-gray-500">Belum ada data kejadian bencana.</p>
    @endforelse

</div>
@endsection
