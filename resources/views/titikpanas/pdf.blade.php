<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Titik Panas</title>
    <style>
    @page {
        size: A4 landscape;
        margin: 20mm;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        margin: 0;
        line-height: 1.2;
    }

    /* Tambahkan ini untuk kontrol page break */
    .page-break {
        page-break-before: always;
    }
    
    .keep-together {
        page-break-inside: avoid;
    }
    
    .break-before {
        page-break-before: always;
    }

    /* KOP SURAT */
    .kop-table {
        width: 100%;
        border-collapse: collapse;
        border-bottom: 3px solid #000;
        margin-bottom: 15px;
        page-break-after: avoid;
    }

    .kop-table td {
        border: none !important;
        vertical-align: middle;
        text-align: center;
    }

    .kop-logo {
        width: auto;
        height: 80px;
    }

    .kop-text h2 {
        margin: 0;
        font-size: 16px;
    }

    .kop-text h3 {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
    }

    .kop-text p {
        margin: 1px;
        font-size: 11px;
    }

    /* JUDUL */
    .judul {
        text-align: center;
        margin: 20px 0 10px 0;
        page-break-after: avoid;
    }

    .judul h2 {
        text-decoration: underline;
        margin-bottom: 3px;
        font-size: 16px;
    }

    /* TABEL DATA */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
        font-size: 10px;
        page-break-inside: auto;
        margin-bottom: 100px; /* TAMBAHKAN MARGIN BOTTOM UNTUK RUANG TANDA TANGAN */
    }

    th, td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
        vertical-align: middle;
        font-size: 9px;
        line-height: 1.1;
    }

    th {
        background: #f2f2f2;
        font-weight: bold;
    }

    /* Sesuaikan lebar kolom lebih proporsional */
    th:nth-child(1), td:nth-child(1) { width: 5%; }
    th:nth-child(2), td:nth-child(2) { width: 10%; }
    th:nth-child(3), td:nth-child(3) { width: 8%; }
    th:nth-child(4), td:nth-child(4) { width: 8%; }
    th:nth-child(5), td:nth-child(5) { width: 8%; }
    th:nth-child(6), td:nth-child(6) { width: 12%; }
    th:nth-child(7), td:nth-child(7) { width: 8%; }
    th:nth-child(8), td:nth-child(8) { width: 8%; }
    th:nth-child(9), td:nth-child(9) { width: 12%; }
    th:nth-child(10), td:nth-child(10) { width: 11%; }

    /* TANDA TANGAN - PERBAIKI POSISI */
    .ttd-container {
        position: fixed;
        bottom: 30mm; /* POSISIKAN DARI BAWAH HALAMAN */
        right: 20mm;
        width: 100%;
        text-align: right;
        page-break-inside: avoid;
    }

    .ttd {
        display: inline-block;
        text-align: center;
        margin-top: 50px;
        font-size: 11px;
    }

    .ttd p {
        margin: 8px 0; /* TAMBAH JARAK ANTAR BARIS */
        line-height: 1.4;
    }

    .ttd-space {
        height: 80px; /* RUANG KOSONG UNTUK TANDA TANGAN */
        margin: 10px 0;
    }

    /* Untuk data yang panjang */
    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* WRAPPER UNTUK KONTEN UTAMA */
    .content-wrapper {
        min-height: calc(100vh - 150px); /* PASTIKAN ADA RUANG UNTUK TANDA TANGAN */
        position: relative;
    }
</style>
</head>
<body>

    <div class="content-wrapper">
        {{-- KOP SURAT --}}
        <table class="kop-table">
            <tr>
                <td style="width: 15%; text-align: left;">
                    @if($logo2)
                        <img src="data:image/png;base64,{{ $logo2 }}" alt="Logo Kanan" class="kop-logo">
                    @endif
                </td>
                <td style="width: 70%;" class="kop-text">
                    <h2>PEMERINTAH KABUPATEN MALANG</h2>
                    <h3>BADAN PENANGGULANGAN BENCANA DAERAH</h3>
                    <p>Jalan Trunojoyo Kepanjen, Kabupaten Malang, Jawa Timur</p>
                    <p>Telepon/Faks (0341) 392121 | Laman: bpbd.malangkab.go.id</p>
                    <p>Pos-el: bpbd@malangkab.go.id | Kode Pos: 65163</p>
                </td>
                <td style="width: 15%; text-align: right;">
                    <!-- Kosongkan jika tidak ada logo kanan -->
                </td>
            </tr>
        </table>

        {{-- JUDUL --}}
        <div class="judul">
            <h2>LAPORAN TITIK PANAS</h2>
            @php
                if (str_contains($tanggal, 's/d')) {
                    [$start, $end] = explode(' s/d ', $tanggal);
                    $startDate = \Carbon\Carbon::parse($start)->translatedFormat('d F Y');
                    $endDate   = \Carbon\Carbon::parse($end)->translatedFormat('d F Y');
                } else {
                    $startDate = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
                    $endDate   = null;
                }
            @endphp
            <p>Periode: {{ $startDate }} @if($endDate) s/d {{ $endDate }} @endif</p>
        </div>

        {{-- TABEL DATA --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Titik Panas</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Kecamatan</th>
                    <th>Satelit</th>
                    <th>Waktu</th>
                    <th>Tingkat Kepercayaan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kejadian as $i => $item)
                    @php
                        try {
                            $tanggalEvent = $item->tanggal 
                                ? \Carbon\Carbon::parse($item->tanggal)
                                : null;
                        } catch (\Exception $e) {
                            $tanggalEvent = null;
                        }
                    @endphp

                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $tanggalEvent ? $tanggalEvent->translatedFormat('d F Y') : '-' }}</td>
                        <td>{{ $item->titik_panas ?? '-' }}</td>
                        <td>{{ $item->latitude ?? '-' }}</td>
                        <td>{{ $item->longitude ?? '-' }}</td>
                        <td class="truncate" title="{{ $item->kecamatan ?? '' }}">{{ $item->kecamatan ?? '-' }}</td>
                        <td>{{ $item->satelit ?? '-' }}</td>
                        <td>{{ $item->waktu ?? '-' }}</td>
                        <td>{{ $item->tingkat_kepercayaan ?? '-' }}</td>
                        <td class="truncate" title="{{ $item->keterangan ?? '' }}">{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                    
                    {{-- Tambahkan page break setiap 15 baris (dikurangi untuk beri ruang tanda tangan) --}}
                    @if(($i + 1) % 15 == 0 && ($i + 1) < count($kejadian))
                        </tbody>
                        </table>
                        
                        {{-- Tanda tangan untuk halaman sebelumnya --}}
                        @if(count($kejadian) > 0)
                        <div class="ttd-container">
                            <div class="ttd">
                                <p>Tanggal TTD: {{ $tglTtd }}</p>
                                <p>Kepala Bidang Pencegahan dan Kesiapsiagaan <br>
                                Manajer Pusdalops PB,</p>
                                <div class="ttd-space"></div>
                                <p><strong><u>ZAINUDDIN, S.H.</u></strong></p>
                                <p>Penata Tingkat 1</p>
                                <p>NIP. 19650101 199001 1 001</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Page break untuk halaman berikutnya --}}
                        <div class="page-break"></div>
                        
                        {{-- Ulang kop surat dan judul untuk halaman baru --}}
                        <table class="kop-table">
                            <tr>
                                <td style="width: 15%; text-align: left;">
                                    @if($logo2)
                                        <img src="data:image/png;base64,{{ $logo2 }}" alt="Logo Kanan" class="kop-logo">
                                    @endif
                                </td>
                                <td style="width: 70%;" class="kop-text">
                                    <h2>PEMERINTAH KABUPATEN MALANG</h2>
                                    <h3>BADAN PENANGGULANGAN BENCANA DAERAH</h3>
                                    <p>Jalan Trunojoyo Kepanjen, Kabupaten Malang, Jawa Timur</p>
                                    <p>Telepon/Faks (0341) 392121 | Laman: bpbd.malangkab.go.id</p>
                                    <p>Pos-el: bpbd@malangkab.go.id | Kode Pos: 65163</p>
                                </td>
                                <td style="width: 15%; text-align: right;"></td>
                            </tr>
                        </table>
                        
                        <div class="judul">
                            <h2>LAPORAN TITIK PANAS (Lanjutan)</h2>
                            <p>Periode: {{ $startDate }} @if($endDate) s/d {{ $endDate }} @endif</p>
                        </div>
                        
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Titik Panas</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Kecamatan</th>
                                    <th>Satelit</th>
                                    <th>Waktu</th>
                                    <th>Tingkat Kepercayaan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                    
                @empty
                    <tr>
                        <td colspan="10">Data tidak ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- TANDA TANGAN UNTUK HALAMAN TERAKHIR --}}
    @if($ttdPertama)
    <div class="ttd-container">
        <div class="ttd">
            <p>Tanggal TTD: {{ $tglTtd }}</p>
            <p>{!! nl2br(e($ttdPertama->jabatan)) !!}</p> {{-- jika jabatan berisi <br> --}}
            <div class="ttd-space"></div>
            <p><strong><u>{{ $ttdPertama->nama_pengawas }}</u></strong></p>
            <p>NIP. {{ $ttdPertama->nip_pengawas }}</p>
        </div>
    </div>
@else
    {{-- Fallback ke data statis jika tidak ada data di database --}}
    <div class="ttd-container">
        <div class="ttd">
            <p>Tanggal TTD: {{ $tglTtd }}</p>
            <p>Kepala Bidang Pencegahan dan Kesiapsiagaan <br>
               Manajer Pusdalops PB,</p>
            <div class="ttd-space"></div>
            <p><strong><u>ZAINUDDIN, S.H.</u></strong></p>
            <p>Penata Tingkat 1</p>
            <p>NIP. 19650101 199001 1 001</p>
        </div>
    </div>
@endif

</body>
</html>