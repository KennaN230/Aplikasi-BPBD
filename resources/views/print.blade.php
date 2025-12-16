<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kejadian Bencana</title>
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

        /* --- TABEL DATA KEJADIAN --- */
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

        /* --- INFORMASI DETAIL --- */
        .info-section {
            margin: 20px 0;
        }

        .info-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .info-item {
            border: 1px solid #ccc;
            padding: 8px;
            background: #f8fafc;
        }

        .info-label {
            font-weight: bold;
            font-size: 13px;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 13px;
        }

        /* --- DAMPAK KORBAN --- */
        .dampak-table {
            margin: 20px 0;
        }

        /* --- TANDA TANGAN DI TENGAH --- */
        .ttd-container {
            margin-top: 60px;
            text-align: right;
            width: 100%;
        }

        .ttd {
            display: inline-block;
            text-align: right;
        }

        .ttd-space {
            height: 60px;
            margin: 10px 0;
        }

        /* --- PRINT CONTROL --- */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .print-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .print-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .print-btn.primary {
            background: #1e40af;
            color: white;
        }

        .print-btn.danger {
            background: #dc2626;
            color: white;
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Print Controls - Only visible on screen -->
    <div class="no-print">
        <h3 style="margin: 0 0 10px 0; font-size: 14px;">Cetak Laporan</h3>
        <div class="print-controls">
            <button onclick="window.print()" class="print-btn primary">🖨️ Cetak</button>
            <button onclick="window.close()" class="print-btn danger">✖ Tutup</button>
        </div>
    </div>

    @forelse($kejadian as $k)
        {{-- KOP SURAT --}}
        <table class="kop-table">
            <tr>
                <td style="width: 15%; text-align: left;">
                    @if($logo2)
                        <img src="data:image/png;base64,{{ $logo2 }}" alt="Logo" class="kop-logo">
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
            <h2>LAPORAN KEJADIAN BENCANA</h2>
            @php
                $tanggalKejadian = $k->tanggal ?? now();
            @endphp
            <p>Periode: {{ \Carbon\Carbon::parse($tanggalKejadian)->translatedFormat('d F Y') }}</p>
        </div>

        {{-- TABEL INFORMASI UTAMA --}}
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Bencana</th>
                    <th>Nama Kejadian</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                    <th>Sumber Info</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $k->jenisBencana->nama_bencana ?? 'Banjir' }}</td>
                    <td>{{ $k->namaKejadian->nama ?? 'Banjir Bandang' }}</td>
                    <td>{{ \Carbon\Carbon::parse($tanggalKejadian)->translatedFormat('d F Y') }}</td>
                    <td>{{ $k->waktu ?? '14:30 WIB' }}</td>
                    <td>{{ $k->desa->nama ?? 'Bantur' }}, {{ $k->kecamatan->nama ?? 'Bantur' }}</td>
                    <td>{{ $k->sumber_info ?? 'TRC BPBD Kab. Malang' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- INFORMASI DETAIL --}}
        <div class="info-section">
            <div class="info-title">INFORMASI DETAIL</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Koordinat</div>
                    <div class="info-value">{{ $k->latitude ?? '-8.318510' }}, {{ $k->longitude ?? '112.515290' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Info Masuk</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($k->info_masuk ?? now())->translatedFormat('d F Y H:i') }} WIB</div>
                </div>
            </div>
            <div class="info-item" style="grid-column: 1 / -1; margin-top: 10px;">
                <div class="info-label">Kronologi</div>
                <div class="info-value">{{ $k->kronologi ?? 'Hujan dengan intensitas tinggi terjadi selama 3 jam dari pukul 11.00 hingga 14.00 WIB menyebabkan meluapnya Sungai Bantur dan menggenangi permukiman warga di sekitarnya.' }}</div>
            </div>
        </div>

        {{-- DAMPAK RUMAH --}}
        @php
            $rumah = $k->rumah ?? (object)[
                'rmh_rb' => 0,
                'rmh_rs' => 0,
                'rmh_rr' => 0,
                'terendam' => 0
            ];
        @endphp
        <div class="info-section">
            <div class="info-title">DAMPAK PADA RUMAH</div>
            <table class="dampak-table">
                <thead>
                    <tr>
                        <th>Rusak Berat</th>
                        <th>Rusak Sedang</th>
                        <th>Rusak Ringan</th>
                        <th>Terendam</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $rumah->rmh_rb ?? 0 }}</td>
                        <td>{{ $rumah->rmh_rs ?? 0 }}</td>
                        <td>{{ $rumah->rmh_rr ?? 0 }}</td>
                        <td>{{ $rumah->terendam ?? 0 }}</td>
                        <td>{{ ($rumah->rmh_rb ?? 0) + ($rumah->rmh_rs ?? 0) + ($rumah->rmh_rr ?? 0) + ($rumah->terendam ?? 0) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- DAMPAK KORBAN --}}
        @php
            $korbanData = $k->korban ?? [];
            $korbanByKategori = [];
            
            foreach($korbanData as $korban) {
                $kategoriId = $korban->id_kategori_korban;
                if (!isset($korbanByKategori[$kategoriId])) {
                    $korbanByKategori[$kategoriId] = [
                        'l' => 0,
                        'p' => 0
                    ];
                }
                $korbanByKategori[$kategoriId]['l'] += $korban->L;
                $korbanByKategori[$kategoriId]['p'] += $korban->P;
            }
            
            $kategoriKorban = [
                1 => 'Meninggal',
                2 => 'Luka Berat', 
                3 => 'Luka Ringan',
                4 => 'Mengungsi'
            ];
        @endphp
        <div class="info-section">
            <div class="info-title">DAMPAK KORBAN</div>
            <table class="dampak-table">
                <thead>
                    <tr>
                        <th>Kondisi</th>
                        <th>Laki-laki</th>
                        <th>Perempuan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategoriKorban as $id => $nama)
                    @php
                        $data = $korbanByKategori[$id] ?? ['l' => 0, 'p' => 0];
                        $total = $data['l'] + $data['p'];
                    @endphp
                    <tr>
                        <td style="text-align: left;">{{ $nama }}</td>
                        <td>{{ $data['l'] }}</td>
                        <td>{{ $data['p'] }}</td>
                        <td>{{ $total }}</td>
                    </tr>
                    @endforeach
                    @php
                        $totalL = array_sum(array_column($korbanByKategori, 'l'));
                        $totalP = array_sum(array_column($korbanByKategori, 'p'));
                        $grandTotal = $totalL + $totalP;
                    @endphp
                    <tr style="background: #f0f0f0; font-weight: bold;">
                        <td style="text-align: left;">TOTAL</td>
                        <td>{{ $totalL }}</td>
                        <td>{{ $totalP }}</td>
                        <td>{{ $grandTotal }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- TANDA TANGAN DI TENGAH --}}
        <div class="ttd-container">
            <div class="ttd">
                <p>Tanggal TTD: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                
                @if(isset($ttdPertama) && $ttdPertama)
                    {{-- Data dinamis dari database --}}
                    <p>{!! nl2br(e($ttdPertama->jabatan)) !!}</p>
                    
                    <div class="ttd-space"></div>
                    
                    <p><strong><u>{{ $ttdPertama->nama_pengawas }}</u></strong></p>
                    <p>NIP. {{ $ttdPertama->nip_pengawas }}</p>
                @else
                    {{-- Fallback statis --}}
                    <p>Kepala Bidang Pencegahan dan Kesiapsiagaan<br>
                       Manajer Pusdalops PB,</p>
                    
                    <div class="ttd-space"></div>
                    
                    <p><strong><u>ZAINUDDIN, S.H.</u></strong></p>
                    <p>Penata Tingkat 1</p>
                    <p>NIP. 19650101 199001 1 001</p>
                @endif
            </div>
        </div>

        @if(!$loop->last)
            <div style="page-break-before: always;"></div>
        @endif

    @empty
        <div style="text-align: center; padding: 40px; border: 1px solid #ccc; background: #f9f9f9;">
            <p style="font-size: 16px; color: #666;">Belum ada data kejadian bencana.</p>
        </div>
    @endforelse

</body>
</html>p