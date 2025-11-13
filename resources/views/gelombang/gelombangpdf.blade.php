<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tinggi Gelombang</title>
    <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }

    /* Header fix */
    .header {
        display: table;
        width: 100%;
        margin-bottom: 20px;
        text-align: center;
    }
    .header > div {
        display: table-cell;
        vertical-align: middle;
        width: 33%;
    }
    .header .logo img {
        max-height: 70px;
        display: block;
        margin: 0 auto;
    }
    .header .center {
        text-align: center;
    }

    /* Separator biar tabel tidak hilang */
    .separator {
        display: block;
        clear: both;
        height: 10px;
    }

    .judul { text-align:center; font-weight:bold; font-size:16px; margin:10px 0; }
    table { width: 100%; border-collapse: collapse; margin-top:10px; }
    th, td { border:1px solid #000; padding:6px; text-align:center; }
    th { background:#eee; }
    .periode { font-size:12px; margin-top:5px; }
</style>

</head>
<body>

<div class="header">
    <div class="logo">
        @if($logo1)
            <img src="data:image/png;base64,{{ $logo1 }}" alt="Logo Kiri">
        @endif
    </div>
    <div class="center">
        <div>BADAN PENANGGULANGAN BENCANA DAERAH</div>
        <div class="periode">Periode: {{ $periode }}</div>
    </div>
    <div class="logo">
        @if($logo2)
            <img src="data:image/png;base64,{{ $logo2 }}" alt="Logo Kanan">
        @endif
    </div>
</div>

<div class="judul">Laporan Tinggi Gelombang Laut</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Tinggi Gelombang Maks (m)</th>
            <th>Tinggi Gelombang Min (m)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($gelombang as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
            <td>{{ $item->tinggi_gelombang_max }}</td>
            <td>{{ $item->tinggi_gelombang_min }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="color:red; font-weight:bold;">🚫 Data tidak ditemukan untuk periode ini</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
