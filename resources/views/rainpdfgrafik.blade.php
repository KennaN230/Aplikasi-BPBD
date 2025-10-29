<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Grafik Hari Hujan</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      margin: 40px;
      color: #111;
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

    /* --- CHART --- */
    .chart-wrap {
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
    }

    canvas {
      width: 100% !important;
      height: 420px !important;
    }

    /* --- TANDA TANGAN --- */
    .ttd {
      margin-top: 50px;
      width: 100%;
      text-align: right;
      font-size: 13px;
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
    <h2>LAPORAN GRAFIK HARI HUJAN DAN TIDAK HUJAN</h2>
    <p>
      Periode:
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
      {{ $startDate }} @if($endDate) s/d {{ $endDate }} @endif
    </p>
  </div>

  {{-- CHART --}}
  <div class="chart-wrap">
    <canvas id="rainChart"></canvas>
  </div>

  {{-- TANDA TANGAN --}}
  <div class="ttd">
    <p>Malang, {{ now()->translatedFormat('d F Y') }}</p>
    <p>Kepala BPBD Kabupaten Malang</p>
    <br><br><br>
    <p><u>____________________</u></p>
    <p>NIP. 19650101 199001 1 001</p>
  </div>

  <script>
    const labels = @json($kejadian->pluck('kecamatan'));
    const dataHujan = @json($kejadian->pluck('hari_hujan'));
    const dataTidakHujan = @json($kejadian->pluck('hari_tidak_hujan'));

    const ctx = document.getElementById('rainChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Hari Hujan',
            data: dataHujan,
            backgroundColor: '#859fe4ff', 
          },
          {
            label: 'Hari Tidak Hujan',
            data: dataTidakHujan,
            backgroundColor: '#4636a2ff',
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Hari Hujan per Kecamatan' }
        },
        scales: {
          x: { beginAtZero: true },
          y: { beginAtZero: true }
        }
      }
    });
  </script>

</body>
</html>
