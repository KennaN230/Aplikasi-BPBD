<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Gempa</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
        }

        /* --- KOP SURAT --- */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            border-bottom: 3px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
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
        .ttd-container {
            margin-top: 50px;
            text-align: right;
            width: 100%;
        }

        .ttd {
            display: inline-block;
            text-align: right;
            min-width: 300px;
        }

        .ttd p {
            margin: 5px 0;
            line-height: 1.4;
        }

        .ttd-space {
            height: 60px; /* Dikurangi dari 80px */
            margin: 15px 0; /* Dikurangi dari 20px */
        }

        .signature-line {
            width: 250px;
            height: 1px;
            border-bottom: 1px solid #000;
            margin: 15px auto;
        }
        
    </style>
</head>
<body onload="window.print()">

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
                <p>Telepon/ Faksimile (0341) 392121 Laman : bpbd.malangkab.go.id</p>
                <p>Pos-el : bpbd@malangkab.go.id, Kode Pos : 65163</p>
            </td>
            <td style="width: 15%; text-align: right;">
                {{-- Kosong atau logo tambahan --}}
            </td>
        </tr>
    </table>

    {{-- JUDUL --}}
    <div class="judul">
        <h2>LAPORAN KEJADIAN GEMPA BUMI</h2>
        @php
            use Carbon\Carbon;
            // Jika format "2025-11-16 s/d 2025-11-20"
            if (preg_match('/s\/d/', $tanggal)) {
                [$start, $end] = preg_split('/\s*s\/d\s*/', $tanggal);
                $startDate = Carbon::parse($start)->translatedFormat('d F Y');
                $endDate   = Carbon::parse($end)->translatedFormat('d F Y');
            } 
            // Jika format "2025-11-16"
            else {
                $startDate = Carbon::parse($tanggal)->translatedFormat('d F Y');
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
                <th>SR</th>
                <th>Waktu</th>
                <th>Lokasi Gempa</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kejadian as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->sr ?? '-' }}</td>
                <td>{{ $item->waktu ?? '-' }}</td>
                <td>{{ $item->lokasi_gempa ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6">Data tidak ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="ttd-container">
        <div class="ttd">
            <p>Tanggal TTD: {{ $tglTtd }}</p>
            
            @if(isset($ttdPertama) && $ttdPertama)
                {{-- Data dinamis dari database --}}
                <p>{!! nl2br(e($ttdPertama->jabatan)) !!}</p>
                
                {{-- JARAK UNTUK TANDA TANGAN (LEBIH KECIL) --}}
                <div class="ttd-space"></div>
                {{-- Atau gunakan garis: --}}
                {{-- <div class="signature-line"></div> --}}
                
                <p><strong><u>{{ $ttdPertama->nama_pengawas }}</u></strong></p>
                <p>NIP. {{ $ttdPertama->nip_pengawas }}</p>
            @else
                {{-- Fallback statis --}}
                <p>Kepala Bidang Pencegahan dan Kesiapsiagaan<br>
                   Manajer Pusdalops PB,</p>
                
                {{-- JARAK UNTUK TANDA TANGAN (LEBIH KECIL) --}}
                <div class="ttd-space"></div>
                {{-- Atau gunakan garis: --}}
                {{-- <div class="signature-line"></div> --}}
                
                <p><strong><u>ZAINUDDIN, S.H.</u></strong></p>
                <p>Penata Tingkat 1</p>
                <p>NIP. 19650101 199001 1 001</p>
            @endif
        </div>
    </div>

</body>
</html>