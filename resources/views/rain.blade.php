<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Hujan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background: #F6F1ED;
        }
        /* Sidebar */
        .sidebar {
            width: 230px;
            background-color: #0a2c66;
            color: white;
            height: 100vh;
            position: fixed;
            padding:20px;
        }
        .sidebar h2 {
            margin-bottom: 25px;
            font-size: 20px;
            text-align: center;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
        }
        .sidebar a {
            display:block;
            color:white;
            text-decoration:none;
            margin:12px 0;
            padding:8px 10px;
            border-radius:6px;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Main */
        .main {
            margin-left:230px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:20px 30px;
            background:white;
            border-bottom:1px solid #F6F1ED;
        }
        .topbar-left h1 {
            margin:0;
            font-size:22px;
            color:#1e3a8a;
        }
        .topbar-left span {
            font-size:14px;
            color:#666;
        }
        .topbar-right {
            display:flex;
            align-items:center;
            gap:15px;
        }
        .search-box input {
            padding:6px 10px;
            border:1px solid #ccc;
            border-radius:8px;
        }
        .avatar {
            display:flex;
            align-items:center;
            gap:8px;
        }
        .avatar img {
            width:40px;
            height:40px;
            border-radius:50%;
        }
        .avatar span {
            font-size:14px;
            font-weight:bold;
        }

        /* Content */
        .content {
            flex: 1;
            padding:30px;
        }
        .content h2 {
            font-size:22px;
            margin-bottom:20px;
            color:#1e3a8a;
            text-align: center;
        }

        /* Filter bar */
        .filter-bar {
            margin-bottom:20px;
            display:flex;
            justify-content:flex-start;
            gap:10px;
            align-items:center;
        }
        .filter-bar select, 
        .filter-bar input {
            padding:6px 10px;
            border:1px solid #ccc;
            border-radius:8px;
        }

        /* Layout 2 kolom */
        .grid {
            display:grid;
            grid-template-columns: 2fr 1.2fr;
            gap:20px;
            align-items: flex-start;
        }

        /* Table */
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius:8px;
            overflow:hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            border:1px solid #ddd;
            padding:10px;
            text-align:center;
        }
        th {
            background:#1e3a8a;
            color:white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Tombol Edit */
        .btn-edit {
            background: #16a34a;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-edit:hover {
            background: #15803d;
        }
        /* Tombol Tambah */
        .btn-tambah {
            background: #d7300bff;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-tambah:hover {
            background: #b22200;
        }

        /* Chart container */
        .chart-container {
            background: white;
            padding:20px;
            border-radius:8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Dropdown chart */
        .chart-header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:10px;
        }
        .chart-header select {
            padding:6px 10px;
            border:1px solid #ccc;
            border-radius:8px;
        }

        /* Footer */
        .footer {
            background:#0a2c66;
            color:white;
            text-align:center;
            padding:15px;
            font-size:14px;
            margin-top: auto;
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
    
    <!-- Main -->
    <div class="main">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <h1>Selamat Datang!</h1>
                <span>Senin, 04 Agustus 2025</span>
            </div>
            <div class="topbar-right">
                <div class="search-box">
                    <input type="text" placeholder="Cari...">
                </div>
                <div class="avatar">
                    <img src="https://i.pravatar.cc/40" alt="Avatar">
                    <span>Wildatul Fajriyah <br><small>Admin</small></span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Informasi Hari Hujan dan Tanpa Hujan</h2>

            <!-- Filter global -->
            <div class="filter-bar">
                <input type="text" placeholder="Cari...">
                <select>
                    <option>Januari</option>
                    <option>Februari</option>
                    <option>Maret</option>
                    <option>April</option>
                    <option>Mei</option>
                    <option>Juni</option>
                    <option>Juli</option>
                    <option selected>Agustus</option>
                    <option>September</option>
                    <option>Oktober</option>
                    <option>November</option>
                    <option>Desember</option>
                </select>
                <select>
                    <option>2023</option>
                    <option>2024</option>
                    <option selected>2025</option>
                </select>
            </div>

            <!-- Grid -->
            <div class="grid">
                <!-- Table Hari Hujan -->
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kecamatan</th>
                                <th>Hari Hujan</th>
                                <th>Hari Tidak Hujan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $item)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $item->kecamatan }}</td>
                                <td>{{ $item->hari_hujan }}</td>
                                <td>{{ $item->hari_tidak_hujan }}</td>
                                <td>
                                    <a href="{{ route('rain.edit', $item->id) }}" class="btn-edit">Edit</a>
                                    <a href="{{ route('rain.tambah', $item->id) }}" class="btn-tambah">Tambah</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Chart -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Grafik</h3>
                        <select id="dataType">
                            <option value="hujan" selected>Hari Hujan</option>
                            <option value="tidak_hujan">Hari Tidak Hujan</option>
                            <option value="keduanya">Keduanya</option>
                        </select>
                    </div>
                    <h4 id="chartTitle" style="text-align:center; color:#1e3a8a; margin-bottom:10px;">
                        Hari Hujan Per Kecamatan
                    </h4>
                    <canvas id="rainChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; 2025 BPBD Kabupaten Malang | +62 822 4409 4886 | @bpbd_malangkab
        </div>
    </div>

    <script>
        const ctx = document.getElementById('rainChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($data->pluck('kecamatan')),
                datasets: [{
                    label: 'Hari Hujan',
                    data: @json($data->pluck('hari_hujan')),
                    backgroundColor: '#2563eb'
                },{
                    label: 'Hari Tidak Hujan',
                    data: @json($data->pluck('hari_tidak_hujan')),
                    backgroundColor: '#16a34a'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        document.getElementById('dataType').addEventListener('change', function() {
            const val = this.value;
            chart.data.datasets[0].hidden = (val === 'tidak_hujan');
            chart.data.datasets[1].hidden = (val === 'hujan');
            chart.update();
        });
    </script>
</body>
</html>
