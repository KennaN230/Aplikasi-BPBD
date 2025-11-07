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
      max-width: 1200px; /* diperlebar biar label muat */
      margin: 0 auto;
      text-align: center;
    }

    canvas {
      display: block;
      margin: 0 auto;
      width: 100% !important;
      height: 480px !important;
    }

    #chartImage {
      display: none;
      max-width: 900px;
      margin: 20px auto;
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

    @media print {
      canvas { display: none !important; }
      #chartImage { display: block !important; }
    }
  </style>
</head>
<body>

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
        use Carbon\Carbon;
        $startDate = null; $endDate = null;
        if (str_contains($tanggal, 's/d')) {
            [$start, $end] = explode(' s/d ', $tanggal);
            try {
                $startDate = Carbon::parse($start)->translatedFormat('d F Y');
                $endDate = Carbon::parse($end)->translatedFormat('d F Y');
            } catch (\Exception $e) {
                $startDate = $start;
                $endDate = $end;
            }
        } else {
            $startDate = $tanggal;
        }
      @endphp
      {{ $startDate }} @if($endDate) s/d {{ $endDate }} @endif
    </p>
  </div>

  {{-- CHART --}}
  <div class="chart-wrap">
    <canvas id="rainChart"></canvas>
    <img id="chartImage" alt="Grafik Hujan">
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
    // Label kabupaten (bukan tanggal)
    const labels = @json($kejadian->pluck('kecamatan'));
    const dataHujan = @json($kejadian->pluck('total_hari_hujan'));
    const dataTidakHujan = @json($kejadian->pluck('total_hari_tidak_hujan'));

    const ctx = document.getElementById('rainChart').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          { label: 'Hari Hujan', data: dataHujan, backgroundColor: '#859fe4' },
          { label: 'Hari Tidak Hujan', data: dataTidakHujan, backgroundColor: '#4636a2' }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Jumlah Hari Hujan per Kabupaten' }
        },
        scales: {
          x: { 
            beginAtZero: true,
            title: { display: true, text: 'Kabupaten' },
            ticks: {
              maxRotation: 60, // rotasi label
              minRotation: 45,
              autoSkip: false, // tampilkan semua nama kabupaten
              font: { size: 12 }
            }
          },
          y: { 
            beginAtZero: true,
            title: { display: true, text: 'Jumlah Hari' },
            ticks: { stepSize: 1 }
          }
        }
      }
    });

    // Konversi chart ke gambar PNG setelah render
    chart.options.animation.onComplete = () => {
      const img = document.getElementById('chartImage');
      img.src = chart.toBase64Image('image/png', 1.0);
    };

    // Cetak otomatis setelah chart selesai
    window.onload = () => {
      setTimeout(() => {
        window.print();
      }, 1200);
    };
  </script>

</body>
</html>
