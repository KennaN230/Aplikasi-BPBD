<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tinggi Gelombang</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
        }

        /* --- KOP SURAT --- */
        .kop-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 90px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            flex-grow: 1;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 18px;
        }

        .kop-text h3 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
        }

        .kop-text p {
            margin: 2px;
            font-size: 13px;
        }

        /* --- JUDUL --- */
        .judul {
            text-align: center;
            margin: 30px 0;
        }

        .judul h2 {
            text-decoration: underline;
            margin-bottom: 5px;
        }

        /* --- TABEL --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #eee;
        }

        /* --- TANDA TANGAN --- */
        .ttd {
            margin-top: 10px;
            width: 100%;
            text-align: right;
        }

        .ttd p {
            margin: 0px 0;
        }
    </style>
</head>
<body onload="window.print()">

    {{-- KOP SURAT --}}
    <div class="kop-container">
    @if($logo2)
        <img src="data:image/png;base64,{{ $logo2 }}" alt="Logo Kanan" class="kop-logo">
    @endif
    <div class="kop-text">
        <h2>PEMERINTAH KABUPATEN MALANG</h2>
        <h3>BADAN PENANGGULANGAN BENCANA DAERAH</h3>
        <p>Jalan Trunojoyo Kepanjen, Kabupaten Malang, Jawa Timur</p>
        <p>Telepon/ Faksimile (0341) 392121 Laman : bpbd.malangkab.go.id</p>
        <p>Pos-el : bpbd@malangkab.go.id, Kode Pos : 65163</p>
    </div>
    
    </div>

    {{-- JUDUL --}}
    <div class="judul">
        <h2>LAPORAN TINGGI GELOMBANG LAUT</h2>
        @php
if (str_contains($periode, 's/d')) {
    [$start, $end] = explode(' s/d ', $periode);
    $startDate = \Carbon\Carbon::parse($start)->translatedFormat('d F Y');
    $endDate   = \Carbon\Carbon::parse($end)->translatedFormat('d F Y');
} else {
    $startDate = \Carbon\Carbon::parse($periode)->translatedFormat('d F Y');
    $endDate   = null;
}
@endphp
<p>Periode: {{ $startDate }} @if($endDate) s/d {{ $endDate }} @endif</p>
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tinggi Gelombang Maks</th>
                <th>Tinggi Gelombang Min</th>
            </tr>
        </thead>
        <tbody>
    @forelse($gelombang as $i => $item)
    <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
        <td>{{ $item->tinggi_gelombang_max }} m</td>
        <td>{{ $item->tinggi_gelombang_min }} m</td>
    </tr>
    @empty
    <tr>
        <td colspan="4">Data tidak ditemukan</td>
    </tr>
    @endforelse
</tbody>

    </table>

    {{-- TANDA TANGAN --}}
    <div class="ttd">
    <p>Malang, {{ now()->translatedFormat('d F Y') }}</p>

    <p>{{ $template->jabatan ?? '' }}</p>

    <br><br><br>

    <p><strong><u>{{ $template->nama_pengawas ?? '' }}</u></strong></p>

    <p>{{ $template->tugas ?? '' }}</p>

    <p>NIP. {{ $template->nip_pengawas ?? '' }}</p>
</div>
</body>
</html>