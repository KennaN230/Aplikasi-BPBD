<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hari Hujan</title>
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
            margin-top: 50px;
            width: 100%;
            text-align: right;
        }

        .ttd p {
            margin: 5px 0;
        }
    </style>
</head>
<body onload="window.print()">

    {{-- KOP SURAT --}}
    <div class="kop-container">
    @if($logo1)
        <img src="data:image/png;base64,{{ $logo1 }}" alt="Logo Kiri" class="kop-logo">
    @endif
    <div class="kop-text">
        <h2>PEMERINTAH KABUPATEN MALANG</h2>
        <h3>BADAN PENANGGULANGAN BENCANA DAERAH</h3>
        <p>Jalan Trunojoyo Kepanjen, Kabupaten Malang, Jawa Timur</p>
        <p>Telepon/ Faksimile (0341) 392121 Laman : bpbd.malangkab.go.id</p>
        <p>Pos-el : bpbd@malangkab.go.id, Kode Pos : 65163</p>
    </div>
    @if($logo2)
        <img src="data:image/png;base64,{{ $logo2 }}" alt="Logo Kanan" class="kop-logo">
    @endif
</div>


    {{-- JUDUL --}}
    <div class="judul">
        <h2>LAPORAN HARI HUJAN DAN TIDAK HUJAN</h2>
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
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kecamatan</th>
                <th>Hari Hujan</th>
                <th>Hari Tidak Hujan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kejadian as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->hari_tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->kecamatan }}</td>
                <td>{{ $item->hari_hujan }}</td>
                <td>{{ $item->hari_tidak_hujan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Data tidak ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="ttd">
        <p>Malang, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala BPBD Kabupaten Malang</p>
        <br><br><br>
        <p><u>____________________</u></p>
        <p>NIP. 19650101 199001 1 001</p>
    </div>

</body>
</html>
