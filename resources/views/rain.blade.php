<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Informasi Hujan</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Poppins', sans-serif; background:#f3ece5; display:flex; min-height:100vh; }

    /* Sidebar */
    .sidebar { width:240px; background:#122453; color:white; display:flex; flex-direction:column; padding:20px 15px; }
    .sidebar-header { display:flex; align-items:center; margin-bottom:20px; }
    .sidebar-header h2 { font-size:14px; line-height:1.2; margin-left:8px; }
    .sidebar-menu { display:flex; flex-direction:column; gap:12px; margin-top:10px; }
    .sidebar-menu a { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:6px; color:white; font-size:14px; text-decoration:none; transition:background 0.2s; }
    .sidebar-menu a:hover, .sidebar-menu a.active { background:#F6F1ED; border-left:4px solid orange; color:#122453; font-weight:bold; }
    .sidebar-menu a:hover img, .sidebar-menu a.active img { filter:brightness(0) saturate(100%) invert(30%) sepia(100%) saturate(500%) hue-rotate(180deg); }

    /* Main */
    .main { flex:1; display:flex; flex-direction:column; }

    /* Topbar */
    .topbar { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; }
    .welcome h1 { font-size:24px; color:#122453; }
    .topbar-right { display:flex; align-items:center; gap:15px; }
    .notification-icon img { width:20px; cursor:pointer; }

    /* Search */
    .search { display:flex; align-items:center; background:#fff; padding:6px 12px; border-radius:20px;border: 2px solid orange;}
    .search input { border:none; outline:none; background:transparent; padding-left:5px; font-family:inherit; }

    /* Profile */
    .profile { display:flex; align-items:center; gap:8px; }
    .profile img { width:40px; height:40px; border-radius:50%; }
    .profile-info { font-size:12px; }
    .profile-info .name { font-weight:600; color:#122453; }
    .profile-info .role { color:gray; }

    /* Judul */
    .content { text-align:center; margin:10px 0; }
    .judul { color:black; padding:12px 30px; font-size:24px; font-weight:bold; display:inline-block; }

    /* Filter bar */
    .filter-bar { display:flex; align-items:center; gap:10px; padding:15px 20px; border-radius:8px; margin:0 20px 20px; box-shadow:0 2px 6px rgba(0,0,0,0.05); flex-wrap:wrap; background: #fff; }
    .filter-bar input, .filter-bar select { padding:6px 10px; border-radius:6px; font-family:inherit; }
    .filter-bar .btn-right { margin-left:auto; }

    /* Buttons */
    .btn { padding:6px 10px; border:none; color:white; font-size:13px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; text-decoration:none; }
    .btn-add { background:#122453; }
    .btn-edit { background:#28D82B; }
    .btn-delete { background:#E30707; }
    .btn-pdf { background:#fd7e14; color:#fff; font-weight:500; padding:6px 10px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }

    /* Table */
    .table-wrap { padding:0 20px; margin-bottom:20px; }
    table { width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; }
    thead { background:#122453; color:white; }
    th, td { padding:10px; font-size:14px; text-align:left; vertical-align:middle; }
    tbody tr:nth-child(even) { background:#f8f8f8; }
    td img { vertical-align:middle; }

    /* Chart */
    .chart-container { background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin:0 20px 20px; }
    .chart-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; gap:10px; }

    /* Footer */
    .footer { background:#122453; color:white; padding:15px 20px; font-size:12px; display:flex; justify-content:space-between; align-items:center; margin-top:auto; }
    .footer .social-icons { display:flex; gap:8px; }
    .footer .social-icons img { width:20px; }

    /* Modal */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999; }
    .modal-content { background:white; padding:20px; border-radius:8px; width:400px; }
    .modal-content h2 { margin-bottom:15px; }
    .modal-content label { display:block; margin-top:10px; font-size:14px; }
    .modal-content input { width:100%; padding:8px; margin-top:5px; border-radius:6px; border:1px solid #ccc; }
    .modal-footer { margin-top:15px; display:flex; justify-content:flex-end; gap:10px; }

    /* Styling untuk select (tanpa panah biru custom) */
    .custom-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: white;
        border: 2px solid orange;
        border-radius: 25px;
        padding: 8px 15px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
    }

    /* Styling untuk input date */
    .custom-date {
        border: 2px solid orange;
        border-radius: 25px;
        padding: 8px 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
    }

    /* Hapus panah custom pada wrapper */
    .select-wrapper::after { content:none; }

    /* Pagination */
    #table-pagination { padding:10px 20px; display:flex; gap:6px; flex-wrap:wrap; justify-content:center; }
    .btn-pg { padding:6px 12px; border:1px solid #ddd; background:#fff; border-radius:6px; cursor:pointer; font-size:14px; }
    .btn-pg.active { background:#122453; color:#fff; border-color:#122453; }
    .btn-pg:disabled { opacity:.5; cursor:not-allowed; }

    /* small responsiveness */
    @media (max-width:900px){
      .sidebar { display:none; }
      .filter-bar { margin:10px; }
      .chart-container, .table-wrap { margin:10px; }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
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
      <a href="#" class="active"><img src="/gambar/lg_hujan.png" style="width:20px;">Hari Hujan & Tanpa Hujan</a>
      <a href="#"><img src="/gambar/lg_gelombang.png" style="width:20px;">Tinggi Gelombang</a>
      <a href="#"><img src="/gambar/lg_destana.png" style="width:20px;">DESTANA Kab. Malang</a>
      <a href="#"><img src="/gambar/lg_spab.png" style="width:20px;">SPAB Kab. Malang</a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main">
    <!-- Topbar -->
    <div class="topbar">
      <div class="welcome">
        <h1>Selamat Datang!</h1>
        <p id="current-date" style="font-size:13px; color:gray;"></p>
      </div>
      <div class="topbar-right">
        <div class="notification-icon"><img src="/gambar/notifikasi.png" alt="notif"></div>
        <div class="profile">
          <img src="/gambar/profile.png" alt="profile">
          <div class="profile-info">
            <div class="name">Wildatul Fajriyah</div>
            <div class="role">Admin</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Judul -->
    <div class="content"><h2 class="judul">Informasi Hari Hujan dan Tanpa Hujan</h2></div>

    <!-- Notifikasi session (Blade) -->
    @if(session('success'))
  <div id="notif" style="background:#28a745; color:white; padding:10px 15px; border-radius:6px; margin:10px 20px; font-size:14px;">
    ✅ {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div id="notif" style="background:#ff4d4f; color:white; padding:10px 15px; border-radius:6px; margin:10px 20px; font-size:14px;">
    ⚠️ {{ session('error') }}
  </div>
@endif


    <!-- Filter + Tambah -->
    <form method="GET" action="{{ route('rain.index') }}" class="filter-bar">
      <div class="search">
        🔍 <input type="text" name="cari" placeholder="Cari Kecamatan..." value="{{ request('cari') }}">
        <button type="submit" style="display:none;"></button>
      </div>

      <!-- Filter Bulan -->
      <div class="select-wrapper">
        <select name="bulan" class="custom-select" onchange="this.form.submit()">
          <option value="">Semua Bulan</option>
          @foreach ([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
              {{ $nama }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Filter Tahun -->
      <div class="select-wrapper">
        <select name="tahun" class="custom-select" onchange="this.form.submit()">
          <option value="">Semua Tahun</option>
          @for ($y = 2023; $y <= now()->year; $y++)
            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
              {{ $y }}
            </option>
          @endfor
        </select>
      </div>

      <!-- Filter Rentang Tanggal -->
      <div class="select-wrapper">
        <input type="date" name="tanggal_awal" class="custom-date" value="{{ request('tanggal_awal') }}">
      </div>
      <div class="select-wrapper">
        <input type="date" name="tanggal_akhir" class="custom-date" value="{{ request('tanggal_akhir') }}">
      </div>

      <!-- Tombol Filter -->
      <div class="select-wrapper">
        <button type="submit" class="btn btn-add">Filter</button>
      </div>

      <!-- Tombol Tambah -->
      <a href="{{ route('rain.create') }}" class="btn btn-add btn-right" style="margin-left:auto;">
        <img src="/gambar/tambah.png" style="width:15px;"> Tambah
      </a>

      <!-- Tombol Cetak PDF -->
      <a href="{{ route('rain.cetakpdf', request()->all()) }}" target="_blank" class="btn-pdf">
        Cetak PDF
      </a>
    </form>

    <!-- Tabel Data -->
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Kecamatan</th>
            <th>Hari Hujan</th>
            <th>Hari Tidak Hujan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($data as $index => $item)
            <tr>
              <td>{{ $index+1 }}</td>
              <td>{{ \Carbon\Carbon::parse($item->hari_tanggal)->translatedFormat('d F Y') }}</td>
              <td>{{ $item->kecamatan }}</td>
              <td>{{ $item->hari_hujan }}</td>
              <td>{{ $item->hari_tidak_hujan }}</td>
              <td>
                <button type="button" class="btn btn-edit"
                  onclick="openEditModal(
                    {{ $item->id }},
                    '{{ addslashes($item->kecamatan) }}',
                    '{{ $item->hari_hujan }}',
                    '{{ $item->hari_tidak_hujan }}',
                    '{{ \Carbon\Carbon::parse($item->hari_tanggal)->format('Y-m-d') }}'
                  )">
                  <img src="/gambar/edit.png" style="width:15px;"> Edit
                </button>

                <form action="{{ route('rain.destroy', $item->id) }}" method="POST" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">
                    <img src="/gambar/sampah.png" style="width:15px;"> Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center; color:gray;">Tidak ada data</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <!-- PAGINATION (Client-side, 10 baris per halaman) -->
      <div id="table-pagination" style="margin-top:12px;"></div>
    </div>

   <!-- Grafik -->
<div class="chart-container">
  <div class="chart-header">
    <h3>Grafik</h3>
    <div style="display:flex; gap:10px; align-items:center;">
      <select id="dataType" class="custom-select" style="width:200px;">
        <option value="hujan" selected>Hari Hujan</option>
        <option value="tidak_hujan">Hari Tidak Hujan</option>
        <option value="keduanya">Keduanya</option>
      </select>
      <a href="{{ route('rainpdfgrafik', request()->all()) }}" target="_blank" class="btn-pdf">
  Cetak PDF Grafik
</a>

    </div>
  </div>

  <h4 id="chartTitle" style="text-align:center; color:#1e3a8a; margin-bottom:10px;">
    Hari Hujan Per Kecamatan
  </h4>
  <canvas id="rainChart" height="120"></canvas>
</div>


    <!-- Footer -->
    <div class="footer">
      <div>+62 822 4409 4886 | @bpbd_malangkab | BPBD KABUPATEN MALANG</div>
      <div class="social-icons">
        <img src="/gambar/Vector.png" alt="">
        <img src="/gambar/Vector (1).png" alt="">
        <img src="/gambar/Vector (2).png" alt="">
        <img src="/gambar/Vector (3).png" alt="">
      </div>
    </div>
  </div>
<!-- jsPDF + html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
  document.getElementById("downloadChartPDF").addEventListener("click", function() {
    const chartContainer = document.querySelector(".chart-container");

    html2canvas(chartContainer, {
      scale: 2, // biar hasilnya tajam
    }).then(canvas => {
      const imgData = canvas.toDataURL("image/png");
      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF("landscape", "mm", "a4");
      const pdfWidth = pdf.internal.pageSize.getWidth();
      const pdfHeight = pdf.internal.pageSize.getHeight();

      const imgWidth = pdfWidth - 20;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;

      pdf.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);
      pdf.save("grafik_hujan.pdf");
    });
  });
</script>

  <!-- Modal Edit -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <h2>Edit Data</h2>
      <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="editId" name="id">

        <label>Tanggal</label>
        <input type="date" id="editTanggal" name="hari_tanggal" required>

        <label>Kecamatan</label>
        <input type="text" id="editKecamatan" name="kecamatan" required>

        <label>Hari Hujan</label>
        <input type="number" id="editHujan" name="hari_hujan" required>

        <label>Hari Tidak Hujan</label>
        <input type="number" id="editTidakHujan" name="hari_tidak_hujan" required>

        <div class="modal-footer">
          <button type="button" class="btn btn-delete" onclick="closeEditModal()">Batal</button>
          <button type="submit" class="btn btn-edit">Simpan</button>
        </div>
      </form>
    </div>
  </div>

<script>
  // Data untuk chart (dari server)
  // Data mentah dari server
const rawLabels = @json($grafik->pluck('kecamatan'));
const rawHujan = @json($grafik->pluck('hari_hujan'));
const rawTidakHujan = @json($grafik->pluck('hari_tidak_hujan'));

// Gabungkan nilai berdasarkan nama kecamatan
const mergedData = {};
rawLabels.forEach((kec, i) => {
  if (!mergedData[kec]) {
    mergedData[kec] = { hujan: 0, tidak_hujan: 0 };
  }
  mergedData[kec].hujan += parseFloat(rawHujan[i]) || 0;
  mergedData[kec].tidak_hujan += parseFloat(rawTidakHujan[i]) || 0;
});

// Siapkan ulang data untuk Chart.js
const labels = Object.keys(mergedData);
const hujan = labels.map(kec => mergedData[kec].hujan);
const tidak_hujan = labels.map(kec => mergedData[kec].tidak_hujan);

  // Inisialisasi Chart.js
 const ctx = document.getElementById('rainChart').getContext('2d');
let rainChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [
      { 
        label: 'Hari Hujan', 
        data: hujan, 
        backgroundColor: '#859fe4ff',
        barPercentage: 1.0,
        categoryPercentage: 1.0
      },
      { 
        label: 'Hari Tidak Hujan', 
        data: tidak_hujan, 
        backgroundColor: '#4636a2ff',
        barPercentage: 1.0,
        categoryPercentage: 1.0
      }
    ]
  },
  options: {
    responsive: true,
    plugins: { 
      legend: { position: 'top' } 
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { color: '#122453' }
      },
      y: {
        beginAtZero: true,
        grid: { color: '#eee' },
        ticks: { 
          color: '#122453',
          precision: 0, // ← ini mencegah desimal
          callback: function(value) {
            // pastikan hanya angka bulat yang ditampilkan
            if (Number.isInteger(value)) {
              return value;
            }
          }
        }
      }
    }
  }
});


  // Ubah dataset berdasarkan pilihan
  document.getElementById('dataType').addEventListener('change', function() {
    const val = this.value;
    if (val === 'hujan') {
      rainChart.data.datasets = [{ label: 'Hari Hujan', data: hujan, backgroundColor: '#859fe4ff' }];
      document.getElementById('chartTitle').innerText = 'Hari Hujan Per Kecamatan';
    } else if (val === 'tidak_hujan') {
      rainChart.data.datasets = [{ label: 'Hari Tidak Hujan', data: tidak_hujan, backgroundColor: '#4636a2ff' }];
      document.getElementById('chartTitle').innerText = 'Hari Tidak Hujan Per Kecamatan';
    } else {
      rainChart.data.datasets = [
        { label: 'Hari Hujan', data: hujan, backgroundColor: '#859fe4ff' },
        { label: 'Hari Tidak Hujan', data: tidak_hujan, backgroundColor: '#4636a2ff' }
      ];
      document.getElementById('chartTitle').innerText = 'Hari Hujan & Tidak Hujan Per Kecamatan';
    }
    rainChart.update();
  });

  // Update waktu realtime di topbar
  function updateDateTime() {
    const d = new Date();
    const day = d.toLocaleString('id-ID', { weekday: 'long' });
    const month = d.toLocaleString('id-ID', { month: 'long' });
    const y = d.getFullYear();
    const h = String(d.getHours()).padStart(2, '0');
    const m = String(d.getMinutes()).padStart(2, '0');
    const s = String(d.getSeconds()).padStart(2, '0');
    document.getElementById('current-date').textContent = `${day}, ${d.getDate()} ${month} ${y}, ${h}:${m}:${s}`;
  }
  setInterval(updateDateTime, 1000);
  updateDateTime();

  // Modal Edit functions
  function openEditModal(id, kecamatan, hari_hujan, hari_tidak_hujan, hari_tanggal) {
    document.getElementById('editId').value = id;
    document.getElementById('editKecamatan').value = kecamatan;
    document.getElementById('editHujan').value = hari_hujan;
    document.getElementById('editTidakHujan').value = hari_tidak_hujan;
    document.getElementById('editTanggal').value = hari_tanggal;

    // set action untuk form (sesuaikan route jika perlu)
    document.getElementById('editForm').action = "/rain/" + id;
    document.getElementById('editModal').style.display = "flex";
  }
  function closeEditModal() {
    document.getElementById('editModal').style.display = "none";
  }

  // Hide notif after 3s if present
  setTimeout(() => {
    const n = document.getElementById('notif');
    if (n) {
      n.style.transition = "opacity 0.5s";
      n.style.opacity = 0;
      setTimeout(() => n.remove(), 500);
    }
  }, 3000);

  /* ===== Pagination Client-side: 10 baris per halaman ===== */
  (function(){
    const rowsPerPage = 10;
    const tbody = document.querySelector('table tbody');
    if(!tbody) return;
    const allRows = Array.from(tbody.querySelectorAll('tr'));
    // Jika jumlah baris <= 10, tidak perlu pagination
    if (allRows.length <= rowsPerPage) return;

    const pager = document.getElementById('table-pagination');
    if(!pager) return;

    function renderPage(page){
      const totalPages = Math.ceil(allRows.length / rowsPerPage);
      if(page < 1) page = 1;
      if(page > totalPages) page = totalPages;
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      allRows.forEach((tr, i) => {
        tr.style.display = (i >= start && i < end) ? '' : 'none';
      });
      buildPager(totalPages, page);
    }

    function makeBtn(label, page, disabled=false, active=false){
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'btn-pg' + (active ? ' active' : '');
      b.textContent = label;
      b.disabled = disabled;
      b.addEventListener('click', () => renderPage(page));
      return b;
    }

    function buildPager(totalPages, currentPage){
      pager.innerHTML = '';
      pager.appendChild(makeBtn('«', 1, currentPage===1));
      pager.appendChild(makeBtn('‹', currentPage-1, currentPage===1));

      const windowSize = 7;
      let start = Math.max(1, currentPage - Math.floor(windowSize/2));
      let end = Math.min(totalPages, start + windowSize - 1);
      start = Math.max(1, end - windowSize + 1);

      for(let p = start; p <= end; p++){
        pager.appendChild(makeBtn(String(p), p, false, p===currentPage));
      }

      pager.appendChild(makeBtn('›', currentPage+1, currentPage===totalPages));
      pager.appendChild(makeBtn('»', totalPages, currentPage===totalPages));
    }

    // Render awal
    renderPage(1);
  })();
</script>
</body>
</html>
