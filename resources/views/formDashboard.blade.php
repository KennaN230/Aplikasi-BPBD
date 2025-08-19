<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f9f9f9;
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
        }

        .topbar h1 {
            font-size: 24px;
            margin: 0;
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
            background-color: gray;
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
            <h1>Selamat Datang!</h1>
            <div class="topbar-right">
                <span>Senin, 04 Agustus 2025</span>
                <div class="avatar"></div>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card" style="border-left: 5px solid #0a2c66;">
                <h2>10</h2>
                <p>Data Admin</p>
            </div>
            <div class="stat-card" style="border-left: 5px solid #d35400;">
                <h2>05</h2>
                <p>Data User</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead> 
            <tbody>
                <tr>
                    <td>1</td><td>Wildatul Fajriyah</td><td>wildatul@gmail.com</td><td>Admin</td><td>Aktif</td>
                    <td>
                        <button class="btn btn-edit">Edit</button>
                        <button class="btn btn-delete">Hapus</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td><td>Junior Harianomang</td><td>junior@yahoo.com</td><td>Admin</td><td>Aktif</td>
                    <td>
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
</body>
</html>
