<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Informasi Tinggi Gelombang</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Poppins',sans-serif; background:#f3ece5; display:flex; min-height:100vh; }

    .sidebar { width:240px; background:#122453; color:white; display:flex; flex-direction:column; padding:20px 15px; }
    .sidebar-header { display:flex; align-items:center; margin-bottom:20px; }
    .sidebar-header h2 { font-size:14px; line-height:1.2; margin-left:8px; }
    .sidebar-menu { display:flex; flex-direction:column; gap:12px; margin-top:10px; }
    .sidebar-menu a { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:6px; color:white; font-size:14px; text-decoration:none; transition:background 0.2s; }
    .sidebar-menu a:hover, .sidebar-menu a.active { background:#F6F1ED; border-left:4px solid orange; color:#122453; font-weight:bold; }

    .main { flex:1; display:flex; flex-direction:column; }
    .topbar { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; }
    .welcome h1 { font-size:24px; color:#122453; }

    .profile { display:flex; align-items:center; gap:8px; }
    .profile img { width:40px; height:40px; border-radius:50%; }
    .profile-info .name { font-weight:600; }
    .profile-info .role { font-size:13px; color:gray; }

    .judul { color:#122453; font-size:22px; font-weight:700; text-align:center; margin:10px 0 20px; }

    .filter-bar { display:flex; align-items:center; gap:10px; padding:15px 20px; background:#fff; border-radius:8px; margin:0 20px 20px; box-shadow:0 2px 6px rgba(0,0,0,0.05); flex-wrap:wrap; }

    .custom-date, select.custom-date { border:2px solid orange; border-radius:8px; padding:8px 12px; font-weight:600; font-size:14px; cursor:pointer; color:#122453; background:white; }

    .btn { padding:8px 14px; border:none; color:white; font-size:13px; border-radius:6px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-weight:600; }
    .btn-add { background:#122453; }
    .btn-edit { background:#28D82B; }
    .btn-delete { background:#E30707; }
    .btn-pdf { background:#fd7e14; }

    table { width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; }
    thead { background:#122453; color:white; }
    th, td { padding:10px; font-size:14px; text-align:left; }
    tbody tr:nth-child(even) { background:#f8f8f8; }

    .table-wrap { padding:0 20px; margin-bottom:20px; }

    .chart-container { background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin:0 20px 20px; }
    .footer { background:#122453; color:white; padding:15px 20px; font-size:12px; display:flex; justify-content:space-between; align-items:center; margin-top:auto; }

    .pagination { display:flex; justify-content:center; gap:8px; margin-top:10px; }
    .pagination a, .pagination span { padding:6px 10px; border-radius:6px; background:#fff; text-decoration:none; color:#122453; font-size:13px; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
    .pagination .active span { background:#122453; color:white; }
  </style>
</head>

<body>
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="/gambar/Logo1.png" style="width:20px;">
      <img src="/gambar/Logo_Kabupaten_Malang 1.png" style="width:20px; margin-left:5px;">
      <h2>Informasi Kejadian<br>Kab Malang</h2>
    </div>
    <div class="sidebar-menu">
      <a href="#"><img src="/gambar/lg_rmh.png" style="width:20px;">Beranda</a>
      <a href="#"><img src="/gambar/lg_kejadian.png" style="width:20px;">Kejadian</a>
      <a href="#"><img src="/gambar/lg_gempaBumi.png" style="width:20px;">Gempa Bumi</a>
      <a href="#"><img src="/gambar/lg_hujan.png" style="width:20px;">Hari Hujan</a>
      <a href="#" class="active"><img src="/gambar/lg_gelombang.png" style="width:20px;">Tinggi Gelombang</a>
      <a href="#"><img src="/gambar/lg_destana.png" style="width:20px;">DESTANA</a>
      <a href="#"><img src="/gambar/lg_spab.png" style="width:20px;">SPAB</a>
    </div>
  </div>

  <div class="main">
    <div class="topbar">
      <div class="welcome">
        <h1>Selamat Datang!</h1>
        <p id="current-date" style="font-size:13px; color:gray;"></p>
      </div>
      <div class="profile">
        <img src="/gambar/profile.png">
        <div class="profile-info">
          <div class="name">Wildatul Fajriyah</div>
          <div class="role">Admin</div>
        </div>
      </div>
    </div>

    <h2 class="judul">Informasi Tinggi Gelombang di Kabupaten Malang</h2>

    @if(session('success'))
      <div id="notif-success" style="background:#28a745; color:white; padding:10px; margin:10px 20px; border-radius:6px;">
        ✅ {{ session('success') }}
      </div>
    @endif

    <form method="GET" action="{{ route('gelombang.index') }}" class="filter-bar">
      <select name="bulan" class="custom-date">
        <option value="">Semua Bulan</option>
        @foreach([
          1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
          5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
          9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ] as $num => $nama)
          <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
            {{ $nama }}
          </option>
        @endforeach
      </select>

      <select name="tahun" class="custom-date">
        <option value="">Semua Tahun</option>
        @for($y = date('Y'); $y >= 2015; $y--)
          <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>

      <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" class="custom-date">
      <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="custom-date">

      <button type="submit" class="btn btn-add">Filter</button>

      <a href="{{ route('gelombang.create') }}" class="btn btn-add" style="margin-left:auto;">
        <img src="/gambar/tambah.png" style="width:15px;"> Tambah
      </a>

      <a href="{{ route('gelombang.cetakpdf', request()->query()) }}" class="btn btn-pdf">Cetak PDF</a>

      <a href="{{ route('gelombang.pdfgrafik', request()->query()) }}" class="btn btn-pdf">Cetak Grafik</a>
    </form>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Tinggi Gelombang Maks</th>
            <th>Tinggi Gelombang Min</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($gelombang as $item)
          <tr>
            <td>{{ $loop->iteration + ($gelombang->currentPage() - 1) * $gelombang->perPage() }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
            <td>{{ $item->tinggi_gelombang_max }} m</td>
            <td>{{ $item->tinggi_gelombang_min }} m</td>
            <td>
              <button type="button" class="btn btn-edit"
                onclick="openEditModal('{{ $item->id }}', '{{ $item->tanggal }}', '{{ $item->tinggi_gelombang_min }}', '{{ $item->tinggi_gelombang_max }}')">Edit</button>
              <form action="{{ route('gelombang.destroy',$item->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" style="text-align:center; color:#E30707; font-weight:bold; padding:10px;">
              🚫 Tidak ada data untuk filter yang dipilih.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div class="pagination">
        {{ $gelombang->links('pagination::bootstrap-5') }}
      </div>
    </div>

    <!-- === MODAL EDIT === -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
      <div style="background:white; padding:20px; border-radius:8px; width:350px; box-shadow:0 4px 8px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom:10px; text-align:center; color:#122453;">Edit Data Gelombang</h3>
        <form id="editForm" method="POST">
          @csrf
          @method('PUT')
          <label>Tanggal:</label><br>
          <input type="date" id="editTanggal" name="tanggal" required style="width:100%; padding:6px; margin:5px 0 10px;"><br>
          <label>Tinggi Gelombang Maks (m):</label><br>
          <input type="number" id="editMax" name="tinggi_gelombang_max" step="0.1" required style="width:100%; padding:6px; margin:5px 0 10px;"><br>
          <label>Tinggi Gelombang Min (m):</label><br>
          <input type="number" id="editMin" name="tinggi_gelombang_min" step="0.1" required style="width:100%; padding:6px; margin:5px 0 15px;"><br>
          <div style="text-align:right;">
            <button type="button" onclick="closeEditModal()" style="background:#E30707; color:white; border:none; padding:6px 12px; border-radius:6px;">Batal</button>
            <button type="submit" style="background:#28D82B; color:white; border:none; padding:6px 12px; border-radius:6px;">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <div class="chart-container">
      <h3 style="text-align:center; color:#122453;">Grafik Tinggi Gelombang per-Bulan</h3>
      <canvas id="gelombangChart" height="120"></canvas>
      <hr style="margin:20px 0;">
      <h4 style="text-align:center; color:#122453;">Rata-Rata Tinggi Gelombang Harian</h4>
      <canvas id="rataChart" height="100"></canvas>
    </div>

    <div class="footer">
      <div>+62 822 4409 4886 | @bpbd_malangkab | BPBD KABUPATEN MALANG</div>
    </div>
  </div>

  <script>
  document.getElementById('current-date').textContent = new Date().toLocaleDateString('id-ID', {
    weekday:'long', year:'numeric', month:'long', day:'numeric'
  });

  const notif = document.getElementById('notif-success');
  if (notif) setTimeout(() => notif.style.display = 'none', 3000);

  function openEditModal(id, tanggal, min, max) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('editTanggal').value = tanggal;
    document.getElementById('editMin').value = min;
    document.getElementById('editMax').value = max;
    document.getElementById('editForm').action = `/gelombang/${id}`;
  }
  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  // 📊 GRAFIK 1 — Tinggi Gelombang Harian (per tanggal)
  new Chart(document.getElementById('gelombangChart'), {
    type: 'line',
    data: {
      labels: {!! json_encode($grafik->pluck('tanggal')->map(fn($t) => (int)\Carbon\Carbon::parse($t)->format('d'))) !!},
      datasets: [
        {
          label: 'TINGGI GELOMBANG MINIMUM',
          data: {!! json_encode($grafik->pluck('tinggi_gelombang_min')) !!},
          borderColor: '#00bcd4',
          backgroundColor: 'transparent',
          borderWidth: 2,
          tension: 0.3
        },
        {
          label: 'TINGGI GELOMBANG MAKSIMUM',
          data: {!! json_encode($grafik->pluck('tinggi_gelombang_max')) !!},
          borderColor: '#2196f3',
          backgroundColor: 'transparent',
          borderWidth: 2,
          tension: 0.3
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'top', labels: { color: '#122453', font: { weight: 'bold' } } } },
      scales: {
        x: {
          title: { display: true, text: 'Tanggal (Hari Dalam Bulan)', color: '#122453', font: { weight: 'bold' } },
          ticks: { color: '#122453', stepSize: 1 }
        },
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Tinggi Gelombang (m)', color: '#122453', font: { weight: 'bold' } },
          ticks: { color: '#122453' }
        }
      }
    }
  });

  // 📈 GRAFIK 2 — Rata-Rata Bulanan (nama bulan di sumbu X)
new Chart(document.getElementById('rataChart'), {
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
    plugins: {
      legend: { 
        position: 'top', 
        labels: { color: '#122453', font: { weight: 'bold' } } 
      }
    },
    scales: {
      x: {
        title: { 
          display: true, 
          text: 'Bulan', 
          color: '#122453', 
          font: { weight: 'bold' } 
        },
        ticks: { color: '#122453' }
      },
      y: {
        beginAtZero: true,
        title: { 
          display: true, 
          text: 'Tinggi Gelombang (m)', 
          color: '#122453', 
          font: { weight: 'bold' } 
        },
        ticks: {
          color: '#122453',
          // 🧩 hanya tampilkan angka bulat (tanpa desimal)
          callback: function(value) {
            if (Number.isInteger(value)) {
              return value;
            }
          },
          stepSize: 1 // 🧩 interval antar angka di sumbu Y
        }
      }
    }
  }
});

</script>
</body>
</html>
