<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Informasi Hujan</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Poppins', sans-serif; background:#f3ece5; display:flex; min-height:100vh; }

    /* Sidebar */
    .sidebar { width:240px; background:#122453; color:white; display:flex; flex-direction:column; padding:20px 15px; }
    .sidebar-header { display:flex; align-items:center; margin-bottom:20px; }
    .sidebar-header h2 { font-size:14px; line-height:1.2; }
    .sidebar-menu { display:flex; flex-direction:column; gap:12px; }
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
    .search { display:flex; align-items:center; background:#fff; padding:6px 12px; border-radius:20px; }
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
    .filter-bar { display:flex; align-items:center; gap:10px; padding:15px 20px; border-radius:8px; margin:0 20px 20px; box-shadow:0 2px 6px rgba(0,0,0,0.05); flex-wrap:wrap; }
    .filter-bar input, .filter-bar select { padding:6px 10px; border-radius:6px; font-family:inherit; }
    .filter-bar .btn-right { margin-left:auto; }

    /* Table */
    table { width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; }
    thead { background:#122453; color:white; }
    th, td { padding:10px; font-size:14px; text-align:left; }
    tbody tr:nth-child(even) { background:#f8f8f8; }

    /* Buttons */
    .btn { padding:5px 10px; border:none; color:white; font-size:12px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:5px; text-decoration:none; }
    .btn-add { background:#122453; }
    .btn-edit { background:#28D82B; }
    .btn-delete { background:#E30707; }

    /* Chart */
    .chart-container { background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
    .chart-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }

    /* Pastikan body full tinggi layar */
    body {
      font-family: 'Poppins', sans-serif;
      background: #f3e6e5ff;
      display: flex;
      min-height: 120vh;
    }

    /* Bagian utama agar flex dan isi penuh */
    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    /* Footer menempel di bawah */
    .footer {
      background: #122453;
      color: white;
      padding: 15px 20px;
      font-size: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: auto;
    }

    .footer .social-icons {
      display: flex;
      gap: 8px;
    }

    .footer .social-icons img {
      width: 20px;
    }

    /* Modal */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; }
    .modal-content { background:white; padding:20px; border-radius:8px; width:400px; }
    .modal-content h2 { margin-bottom:15px; }
    .modal-content label { display:block; margin-top:10px; font-size:14px; }
    .modal-content input { width:100%; padding:8px; margin-top:5px; border-radius:6px; border:1px solid #ccc; }
    .modal-footer { margin-top:15px; display:flex; justify-content:flex-end; gap:10px; }
    .btn-close { background:#ccc; color:black; }
  </style>
</head>
<body>
  
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="/gambar/Logo 1 1.png" style="width:20px;">
      <img src="/gambar/Logo_Kabupaten_Malang 1.png" style="width:20px; margin-left:5px;">
      <h2 style="margin-left:8px;">Informasi Kejadian Kab Malang</h2>
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
        <p id="current-date"></p>
      </div>
      <div class="topbar-right">
        <div class="notification-icon"><img src="/gambar/notifikasi.png"></div>
        <div class="profile">
          <img src="/gambar/profile.png">
          <div class="profile-info">
            <div class="name">Wildatul Fajriyah</div>
            <div class="role">Admin</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Judul -->
    <div class="content"><h2 class="judul">Informasi Hari Hujan dan Tanpa Hujan</h2></div>
<!-- Notifikasi -->
@if(session('success'))
  <div id="notif" style="
      background:#fd7e14;   /* warna oranye */
      color:white;
      padding:10px 15px;
      border-radius:6px;
      margin:10px 20px;
      font-size:14px;
      animation: fadeIn 0.5s;
  ">
      ✅ {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div id="notif" style="
      background:#ff4d4f;   /* merah untuk error */
      color:white;
      padding:10px 15px;
      border-radius:6px;
      margin:10px 20px;
      font-size:14px;
      animation: fadeIn 0.5s;
  ">
      ⚠️ {{ session('error') }}
  </div>
@endif

<style>
@keyframes fadeIn {
  from { opacity:0; transform:translateY(-10px); }
  to { opacity:1; transform:translateY(0); }
}
</style>

<script>
  // Notifikasi otomatis hilang setelah 3 detik
  setTimeout(() => {
    let notif = document.getElementById('notif');
    if(notif){
      notif.style.transition = "opacity 0.5s";
      notif.style.opacity = "0";
      setTimeout(()=> notif.remove(), 500);
    }
  }, 3000);
</script>

<!-- Filter + Tambah -->
<form method="GET" action="{{ route('rain.index') }}" class="filter-bar">
  <div class="search">
    🔍 <input type="text" name="cari" placeholder="Cari Kecamatan..." value="{{ request('cari') }}">
    <button type="submit" style="display:none;"></button>
  </div>

<!-- Filter Bulan & Tahun -->
<style>
  /* Bungkus dropdown */
  .select-wrapper {
    position: relative;
    display: inline-block;
    margin-right: 8px; /* jarak antar dropdown */
  }

  /* Style dropdown */
  .custom-select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background: #fff;
  border: 3px solid orange;
  border-radius: 999px;   /* bikin bentuk oval penuh */
  padding: 6px 45px 6px 20px; /* kanan diperbesar biar teks tidak menabrak panah */
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  color: black;
  cursor: pointer;
  line-height: 1.4;
}
  /*/* Panah biru */
.select-wrapper::after {
  content: "▼";
  font-size: 12px;
  color: blue;
  position: absolute;
  right: 3px;     /* posisikan lebih ke dalam supaya sejajar dengan padding kanan */
  top: 50%;        /* letakkan di tengah vertikal */
  transform: translateY(-50%); /* biar presisi di tengah */
  pointer-events: none;
}


  /* Efek hover & fokus */
  .custom-select:hover,
  .custom-select:focus {
    border-color: #ff8800;
    outline: none;
  }
  <style>
    /* Styling untuk select */
    .custom-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: white url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'><path fill='blue' d='M7 10l5 5 5-5z'/></svg>") no-repeat right 10px center;
        background-size: 16px;
        border: 2px solid orange;
        border-radius: 25px;
        padding: 8px 35px 8px 15px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
    }

    /* Styling untuk input date */
    .custom-date {
        border: 2px solid orange;
        border-radius: 25px;
        padding: 8px 15px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
    }


</style>

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
    <input type="date" name="tanggal_awal" class="custom-date"
           value="{{ request('tanggal_awal') }}">
  </div>
  <div class="select-wrapper">
    <input type="date" name="tanggal_akhir" class="custom-date"
           value="{{ request('tanggal_akhir') }}">
  </div>

  <!-- Tombol Filter -->
  <div class="select-wrapper">
    <button type="submit" class="btn btn-add">Filter</button>
  </div>

  <!-- Tombol Tambah -->
  <a href="{{ route('rain.create') }}" class="btn btn-add btn-right">
    <img src="/gambar/tambah.png" style="width:15px;"> Tambah
  </a>

  <!-- Tombol Cetak PDF (warna oranye) -->
  <a href="{{ route('rain.cetakpdf', request()->all()) }}" target="_blank" class="btn btn-pdf">
    Cetak PDF
  </a>
</form>

<style>
  /* Styling untuk tombol PDF */
  .btn-pdf {
      background: #fd7e14; /* oranye */
      color: #fff;
      font-weight: 500;
  }

 /* Select tanpa panah */
.custom-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: white; /* hapus background SVG panah */
    border: 2px solid orange;
    border-radius: 25px;
    padding: 8px 15px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
}

/* Input date tanpa icon kalender / panah */
.custom-date {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: white;
    border: 2px solid orange;
    border-radius: 25px;
    padding: 8px 15px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
}

/* Hilangkan pseudo-element panah yang pernah dibuat */
.select-wrapper::after {
    content: none !important;
}
background: white;

</style>

<!-- Tabel Data -->
<div style="padding:0 20px; margin-bottom:20px;">
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
            <!-- Tombol Edit -->
            <button type="button" class="btn btn-edit"
              onclick="openEditModal(
                {{ $item->id }},
                '{{ $item->kecamatan }}',
                '{{ $item->hari_hujan }}',
                '{{ $item->hari_tidak_hujan }}',
                '{{ \Carbon\Carbon::parse($item->hari_tanggal)->format('Y-m-d') }}'
              )">
              <img src="/gambar/edit.png" style="width:15px;"> Edit
            </button>

            <!-- Tombol Hapus -->
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
  <div id="table-pagination" style="padding:10px 0; display:flex; gap:6px; flex-wrap:wrap; justify-content:center;"></div>
</div>

<!-- Sedikit CSS untuk tombol pagination -->
<style>
  #table-pagination { padding:10px 20px; display:flex; gap:6px; flex-wrap:wrap; justify-content:center; }
  .btn-pg { padding:6px 12px; border:1px solid #ddd; background:#fff; border-radius:6px; cursor:pointer; font-size:14px; }
  .btn-pg.active { background:#122453; color:#fff; border-color:#122453; }
  .btn-pg:disabled { opacity:.5; cursor:not-allowed; }
</style>

<!-- Grafik sekarang di bawah tabel -->
<div class="chart-container" style="margin:0 20px 20px;">
  <div class="chart-header">
    <h3>Grafik</h3>
    <select id="dataType">
      <option value="hujan" selected>Hari Hujan</option>
      <option value="tidak_hujan">Hari Tidak Hujan</option>
      <option value="keduanya">Keduanya</option>
    </select>
  </div>
  <h4 id="chartTitle" style="text-align:center; color:#1e3a8a; margin-bottom:10px;">Hari Hujan Per Kecamatan</h4>
  <canvas id="rainChart" height="120"></canvas>
</div>

    <!-- Footer -->
    <div class="footer">
      <div>+62 822 4409 4886 | @bpbd_malangkab | BPBD KABUPATEN MALANG</div>
      <div class="social-icons">
        <img src="/gambar/Vector.png">
        <img src="/gambar/Vector (1).png">
        <img src="/gambar/Vector (2).png">
        <img src="/gambar/Vector (3).png">
      </div>
    </div>
  </div>

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
          <button type="button" class="btn btn-close" onclick="closeEditModal()">Batal</button>
          <button type="submit" class="btn btn-edit">Simpan</button>
        </div>
      </form>
    </div>
  </div>

<script>
  const labels = @json($grafik->pluck('kecamatan'));
  const hujan = @json($grafik->pluck('hari_hujan'));
  const tidak_hujan = @json($grafik->pluck('hari_tidak_hujan'));

  const ctx = document.getElementById('rainChart').getContext('2d');
  let rainChart = new Chart(ctx, {
    type: 'bar',
    data: { labels: labels, datasets: [
      { label: 'Hari Hujan', data: hujan, backgroundColor: '#3960cbff' },
      { label: 'Hari Tidak Hujan', data: tidak_hujan, backgroundColor: '#d35726ff' }
    ]},
    options: { responsive:true, plugins:{ legend:{ position:'top' } } }
  });

  document.getElementById('dataType').addEventListener('change', function() {
    if(this.value==='hujan'){ 
      rainChart.data.datasets=[{label:'Hari Hujan',data:hujan,backgroundColor:'#3960cbff'}]; 
      document.getElementById('chartTitle').innerText='Hari Hujan Per Kecamatan'; 
    }
    else if(this.value==='tidak_hujan'){ 
      rainChart.data.datasets=[{label:'Hari Tidak Hujan',data:tidak_hujan,backgroundColor:'#d35726ff'}]; 
      document.getElementById('chartTitle').innerText='Hari Tidak Hujan Per Kecamatan'; 
    }
    else{ 
      rainChart.data.datasets=[
        {label:'Hari Hujan',data:hujan,backgroundColor:'#3960cbff'},
        {label:'Hari Tidak Hujan',data:tidak_hujan,backgroundColor:'#d35726ff'}
      ]; 
      document.getElementById('chartTitle').innerText='Hari Hujan & Tidak Hujan Per Kecamatan'; 
    }
    rainChart.update();
  });

  function updateDateTime(){ 
    const d=new Date(); 
    const day=d.toLocaleString('id-ID',{weekday:'long'}); 
    const month=d.toLocaleString('id-ID',{month:'long'}); 
    const y=d.getFullYear(); 
    const h=d.getHours().toString().padStart(2,'0'); 
    const m=d.getMinutes().toString().padStart(2,'0'); 
    const s=d.getSeconds().toString().padStart(2,'0'); 
    document.getElementById('current-date').textContent=`${day}, ${d.getDate()} ${month} ${y}, ${h}:${m}:${s}`; 
  }
  setInterval(updateDateTime,1000); updateDateTime();

  // Modal Edit
  function openEditModal(id, kecamatan, hari_hujan, hari_tidak_hujan, hari_tanggal){
    document.getElementById('editId').value = id;
    document.getElementById('editKecamatan').value = kecamatan;
    document.getElementById('editHujan').value = hari_hujan;
    document.getElementById('editTidakHujan').value = hari_tidak_hujan;
    document.getElementById('editTanggal').value = hari_tanggal;

    document.getElementById('editForm').action = "/rain/" + id;
    document.getElementById('editModal').style.display = "flex";
  }
  function closeEditModal(){
    document.getElementById('editModal').style.display = "none";
  }

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
