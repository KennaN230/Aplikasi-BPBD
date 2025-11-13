<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Titik Panas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
            font-size: 12px;
        }

        /* --- KOP SURAT --- */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid #000;
            margin-bottom: 20px;
        }

        .kop-table td {
            border: none !important;
            vertical-align: middle;
            text-align: center;
        }

        .kop-logo {
            width: auto;
            height: 90px;
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
            margin: 30px 0 15px 0;
        }

        .judul h2 {
            text-decoration: underline;
            margin-bottom: 5px;
        }

        /* --- TABEL DATA --- */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            word-wrap: break-word;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        th:nth-child(1), td:nth-child(1) { width: 25px; }
        th:nth-child(2), td:nth-child(2) { width: 90px; }
        th:nth-child(3), td:nth-child(3) { width: 70px; }
        th:nth-child(4), td:nth-child(4) { width: 70px; }
        th:nth-child(5), td:nth-child(5) { width: 70px; }
        th:nth-child(6), td:nth-child(6) { width: 120px; }
        th:nth-child(7), td:nth-child(7) { width: 70px; }
        th:nth-child(8), td:nth-child(8) { width: 70px; }
        th:nth-child(9), td:nth-child(9) { width: 100px; }
        th:nth-child(10), td:nth-child(10) { width: 100px; }

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
<body>

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            <td style="width: 15%; text-align: left;">
                @if($logo1)
                    <img src="data:image/png;base64,{{ $logo1 }}" alt="Logo Kiri" class="kop-logo">
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
                @if($logo2)
                    <img src="data:image/png;base64,{{ $logo2 }}" alt="Logo Kanan" class="kop-logo">
                @endif
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
    <table>
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
                        // Lebih fleksibel: terima format Y-m-d atau Y-m-d H:i:s tanpa ubah timezone
                        $tanggalEvent = \Carbon\Carbon::parse($item->hari_tanggal);
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
                    <td>{{ $item->kecamatan ?? '-' }}</td>
                    <td>{{ $item->satelit ?? '-' }}</td>
                    <td>{{ $item->waktu ?? '-' }}</td>
                    <td>{{ $item->tingkat_kepercayaan ?? '-' }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="ttd">
        <p>Malang, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala BPBD Kabupaten Malang</p>
        <br><br><br>
        <p><u></u></p>
        <p>NIP. 19650101 199001 1 001</p>
    </div>

</body>
</html>
