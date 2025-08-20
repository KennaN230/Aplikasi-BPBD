<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gempa Bumi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #F6F1ED;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #0a2c66;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar-header h2 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            color: white;
            transition: background 0.3s;
        }

        .sidebar-menu a:hover {
            background-color: #123b85;
        }

        /* Main Content */
        .main {
            flex: 1;
            padding: 20px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .topbar h1 {
            font-size: 24px;
            margin: 0;
            color: #0a2c66;
        }

        .topbar span {
            color: #555;
            font-size: 14px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #0a2c66;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Notifikasi */
        .notif {
            position: relative;
            cursor: pointer;
        }
        .notif svg {
            width: 24px;
            height: 24px;
            color: #0a2c66;
        }
        .notif span {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 9999px;
            font-weight: bold;
        }

        /* Judul Section */
        .section-title {
            background: #e5e7eb;
            padding: 10px 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
            color: #0a2c66;
        }

        /* Statistik */
        .stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }

        .stat-card {
            flex: 1;
            background: white;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .stat-card h2 {
            font-size: 28px;
            margin: 0;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background: #0a2c66;
            color: white;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-info {
            background-color: #0a2c66 ;
        }

        .btn-edit {
            background-color: #28a745;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding: 10px;
            background: #0a2c66;
            color: white;
            text-align: center;
            border-radius: 6px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end; 
            gap: 10px;
            margin: 15px 0;
        }

        /* tambahan biar grafik tidak terlalu besar */
        .chart-box {
            background: #1e1e2f; 
            padding: 20px; 
            border-radius: 8px;
            max-width: 300px;
            max-height: 250px;
        }

        .chart-box canvas {
            max-width: 100%;
            max-height: 220px;
        }

        /* 🔹 tambahan khusus */
        #sr-box {
            max-width: 400px;
            max-height: 300px;
        }

        #catatan-box {
            max-width: 400px;
            max-height: 300px;
        }

        /* 🔹 tombol tambah data geser kiri */
        .toolbar button:first-child {
            margin-right: auto;
        }

        /* 🔹 search box menyatu */
        .search-box {
            display: inline-flex;
            align-items: center;
            border: 2px solid #0a2c66;
            border-radius: 25px;
            overflow: hidden;
        }

        .search-box input {
            border: none;
            outline: none;
            padding: 8px 12px;
            font-size: 14px;
        }

        .search-box button {
            background-color: #0a2c66;
            color: white;
            border: none;
            padding: 8px 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 0;
        }

        /* 🔹 dropdown tahun */
        .toolbar .tahun-select {
            border: 2px solid #f59e0b; 
            border-radius: 9999px; 
            padding: 6px 28px 6px 16px; 
            font-weight: bold;
            background-color: #fff;
            color: #000;
            cursor: pointer;
            appearance: none; 
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='blue'><path d='M7 10l5 5 5-5'/></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Informasi Kejadian Kab Malang</h2>
        </div>
        <div class="sidebar-menu">
            <a href="#">Beranda</a>
            <a href="#">Kejadian</a>
            <a href="#">Gempa Bumi</a>
            <a href="#">Hari Hujan & Tanpa Hujan</a>
            <a href="#">Tinggi Gelombang</a>
            <a href="#">DESTANA Kab. Malang</a>
            <a href="#">SPAB Kab. Malang</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="topbar">
            <div>
                <h1>Selamat Datang!</h1>
                <span id="tanggal-sekarang"></span>
            </div>
            <div class="topbar-right">
                <!-- Notifikasi -->
                <div class="notif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 17h5l-1.405-1.405A2.032 2.032 
                                 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 
                                 2 0 10-4 0v.341C7.67 6.165 
                                 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 
                                 1.436L4 17h5m6 0v1a3 3 0 
                                 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>3</span>
                </div>
                <!-- Avatar -->
                <div class="avatar">
                    <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="profil">
                </div>
                <span style="font-weight:bold;">Wildatul Fajariyah</span>
            </div>
        </div>

        <!-- Judul Section -->
        <h2 class="section-title">Informasi Catatan Gempa Bumi</h2> 

        <!-- 🔹 Tambahan Grafik -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
           <!-- Grafik Jumlah SR Gempa -->
            <div class="chart-box" id="sr-box">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <h3 style="color:white; margin:0;">Jumlah SR Gempa</h3>
            <div style="color:orange; font-size:30px; font-weight:bold;">127</div>
        </div>
        <canvas id="srGempaChart"></canvas>
    </div>

            <!-- Grafik Catatan Gempa Bumi -->
            <div class="chart-box" id="catatan-box">
                <h3 style="color:white; text-align:center;">Catatan Gempa Bumi</h3>
                <canvas id="catatanGempaChart"></canvas>
            </div>
        </div>
        <!-- 🔹 End Grafik -->

        <!-- 🔹 Toolbar dipindah ke atas tabel -->
        <div class="toolbar">
            <button class="btn btn-info">+ Tambah Data Kejadian</button>
            <div class="search-box">
                <input type="text" placeholder="Cari..." class="input-cari">
                <button class="btn btn-info">Cari</button>
            </div>
            <select class="tahun-select">
                <option>2025</option>
                <option>2024</option>
                <option>2023</option>
            </select>
        </div>

        <table>
            <thead>
                 <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Gempa</th>
                    <th>SR</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>4 Agustus 2025</td>
                    <td>04.46.20</td>
                    <td>104 km BaratDaya KAB-</td>
                    <td>2.6</td>
                    <td>Info Gempa Mag:2.6, 4-Agus</td>
                    <td>
                        <button class="btn btn-info">Info</button>
                        <button class="btn btn-edit">Edit</button>
                        <button class="btn btn-delete">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            &copy; 2025 BPBD Kabupaten Malang | +62 822 4409 4886 | @bpbd_malangkab
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Tanggal otomatis
        const bulan = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        const hari = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
        const t = new Date();
        document.getElementById("tanggal-sekarang").textContent =
            hari[t.getDay()]+", "+t.getDate()+" "+bulan[t.getMonth()]+" "+t.getFullYear();

        // Grafik Jumlah SR Gempa
        const srCtx = document.getElementById('srGempaChart').getContext('2d');
        new Chart(srCtx, {
            type: 'bar',
            data: {
                labels: ['2','2.2','2.3','2.4','2.5','2.6','2.7','2.8','2.9','3.0','3.1','3.2','3.3','3.4','3.5','3.6','3.7','3.8','3.9','4.0','4.1','4.2','4.5'],
                datasets: [{
                    label: 'Jumlah',
                    data: [1,1,2,8,4,9,8,13,20,11,8,9,7,4,2,4,4,2,1,3,1,1,1],
                    backgroundColor: 'dodgerblue'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // Grafik Catatan Gempa Bumi
        const catatanCtx = document.getElementById('catatanGempaChart').getContext('2d');
        new Chart(catatanCtx, {
            type: 'bar',
            data: {
                labels: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
                datasets: [{
                    label: 'Jumlah Gempa',
                    data: [19,17,26,17,11,26,8,3,0,0],
                    backgroundColor: 'dodgerblue'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>