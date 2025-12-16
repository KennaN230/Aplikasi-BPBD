<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Grafik Gelombang Laut</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: DejaVu Sans, sans-serif; margin: 40px; color: #111; }

    /* Header */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
    }
    .header .logo img {
      max-height: 70px;
      width: auto;
      display: block;
    }
    .header .center {
      text-align: center;
      flex: 1;
    }
    .header .center div {
      font-size: 14px;
      font-weight: bold;
    }
    .periode {
      font-size: 12px;
      font-weight: normal;
      margin-top: 4px;
    }

    /* Chart area */
    .chart-wrap {
      width: 100%;
      text-align: center;
      margin-top: 30px;
    }
    canvas { width: 100% !important; height: 400px !important; }
    #chartImage { display: none; max-width: 900px; margin: 20px auto; }

    @media print {
      canvas { display: none !important; }
      #chartImage { display: block !important; }
    }

    .judul {
      text-align: center;
      margin: 25px 0;
      font-weight: bold;
      font-size: 16px;
      text-decoration: underline;
    }

    .ttd {
      margin-top: 20px;
      text-align: right;
      font-size: 13px;
    }
    .ttd p {
            margin: 5px 0;
        }
  </style>
</head>
<body>

  {{-- HEADER --}}
  <div class="header">
    <div class="logo">
        @if($logo2)
            <img src="data:image/png;base64,{{ $logo2 }}" alt="Logo Kanan">
        @endif
    </div>
    <div class="center">
        <div>BADAN PENANGGULANGAN BENCANA DAERAH</div>
        <div class="periode">Periode: {{ $periode }}</div>
    </div>
    <div class="logo">
        
    </div>
  </div>

  {{-- JUDUL --}}
  <div class="judul">LAPORAN GRAFIK RATA-RATA TINGGI GELOMBANG</div>

  {{-- CHART --}}
  <div class="chart-wrap">
    <canvas id="rataChart"></canvas>
    <img id="chartImage" alt="Grafik Gelombang">
  </div>

  {{-- TANDA TANGAN --}}
<div class="ttd">
    <p>Malang, {{ now()->translatedFormat('d F Y') }}</p>
    
    @if($templateTTD)
        {{-- Jika menggunakan single template --}}
        <p>{{ $templateTTD->jabatan }}</p>
        <br><br><br>
        <p><strong><u>{{ strtoupper($templateTTD->nama_pengawas) }}</u></strong></p>
        <p>{{ $templateTTD->jabatan }}</p>
        <p>NIP. {{ $templateTTD->nip_pengawas }}</p>
    @elseif($ttdKepala)
        {{-- Jika mencari berdasarkan jabatan tertentu --}}
        <p>{{ $ttdKepala->jabatan }}</p>
        <br><br><br>
        <p><strong><u>{{ strtoupper($ttdKepala->nama_pengawas) }}</u></strong></p>
        <p>{{ $ttdKepala->jabatan }}</p>
        <p>NIP. {{ $ttdKepala->nip_pengawas }}</p>
    @elseif($templatesTTD->count() > 0)
        {{-- Jika ada multiple template, ambil yang pertama --}}
        @php
            $firstTTD = $templatesTTD->first();
        @endphp
        <p>{{ $firstTTD->jabatan }}</p>
        <br><br><br>
        <p><strong><u>{{ strtoupper($firstTTD->nama_pengawas) }}</u></strong></p>
        <p>{{ $firstTTD->jabatan }}</p>
        <p>NIP. {{ $firstTTD->nip_pengawas }}</p>
    @else
        {{-- Fallback ke hardcoded --}}
        <p>Kepala BPBD Kabupaten Malang</p>
        <br><br><br>
        <p><strong><u>ZAINUDDIN, S.H.</u></strong></p>
        <p>Penata Tingkat 1</p>
        <p>NIP. 19650101 199001 1 001</p>
    @endif
</div>

  {{-- SCRIPT CHART --}}
  <script>
  const ctx = document.getElementById('rataChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($rataBulan->pluck('nama_bulan')) !!},
      datasets: [
        {
          label: 'GEL. MAX',
          data: {!! json_encode($rataBulan->pluck('rata_max')) !!},
          backgroundColor: '#122453'
        },
        {
          label: 'GEL. MIN',
          data: {!! json_encode($rataBulan->pluck('rata_min')) !!},
          backgroundColor: '#5e81f4'
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'top' } },
      scales: {
        x: { 
          title: { display: true, text: 'Bulan' } 
        },
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Tinggi Gelombang (m)' },
          ticks: {
            stepSize: 50, // ✅ interval 10 meter
            callback: v => Number.isInteger(v) ? v : null
          },
          suggestedMax: 50 // ✅ batas atas otomatis, ubah sesuai data (misal 50m)
        }
      }
    }
  });

  chart.options.animation.onComplete = () => {
    document.getElementById('chartImage').src = chart.toBase64Image('image/png', 1.0);
  };

  window.onload = () => {
    setTimeout(() => window.print(), 1200);
  };
</script>
</body>
</html>
