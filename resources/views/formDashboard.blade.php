<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <!-- Ganti font ke Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f3ece5; 
            display: flex; 
            min-height: 100vh; /* Pastikan tinggi minimal layar penuh */
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #122453;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar-header img {
            width: 30px; /* Ukuran logo */
        }

        .sidebar-header h2 {
            font-size: 15px;
            line-height: 1.3;
            font-weight: 600;
            margin-left: 15px; /* Jarak antara logo dan teks */
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        /* Gaya untuk ikon saat hover dan aktif */
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #F6F1ED;
            border-left: 4px solid orange;
            color: #122453; /* Mengubah warna teks menu aktif menjadi gelap */
            font-weight: bold; /* Menebalkan teks menu aktif */
        }

        /* Mengubah warna gambar pada menu hover dan active */
        .sidebar-menu a:hover img, .sidebar-menu a.active img {
            filter: brightness(0) saturate(100%) invert(30%) sepia(100%) saturate(500%) hue-rotate(180deg);
        }


        /* Main */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-top: 1px; /* Memberi ruang di atas */
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;

        }
        .notification-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-icon img {
            width: 20px; /* Ukuran ikon notifikasi */
            cursor: pointer;
        }
                .welcome h1 {
            font-size: 24px;
            color: #122453;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 2px; /* Menambahkan jarak di bawah tombol */
        }  

        /* Styling search bar */
        .search {
            display: flex;
            align-items: center;
            background: #ffff;
            padding: 6px 12px;
            border-radius: 20px;
            margin-right: 1px; /* Memberikan jarak antara tombol dan search bar */
        }

        .search input {
            border: none;
            outline: none;
            background: transparent;
            padding-left: 5px;
            font-family: inherit;
        }
        .profile {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .profile-info {
            font-size: 12px;
        }
        .profile-info .name {
            font-weight: 600;
            color: #122453;
        }
        .profile-info .role {
            color: gray;
        }

        /* Stats Card */
        .stats {
            display: flex;
            gap: 100px;
            padding: 30px; /* Padding lebih simetris */
            justify-content: flex-start; /* Menyusun kartu lebih rapat */
        }

        .stat-card {
            width: 200px; /* Lebar kartu sedikit lebih besar agar nyaman dibaca */
            background: white;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center; /* Teks terpusat di dalam kartu */
        }

        .stat-card.blue { border-top: 8px solid #aecaf7; }
        .stat-card.orange { border-top: 8px solid #f7d7b0; }

        .stat-title {
            font-weight: 600;
            font-size: 16px; /* Ukuran teks yang pas untuk judul */
            margin-bottom: 8px; /* Memberi jarak antara judul dan angka */
            color: #122453;
        }

        .stat-number {
            font-size: 30px; /* Ukuran angka lebih besar untuk fokus perhatian */
            font-weight: bold;
            margin: 5px 0;
            color: #122453;
        }

        .progress {
            background: #e6e6e6;
            height: 0px; /* Sedikit lebih besar untuk progress bar */
            border-radius: 5px;
            overflow: hidden;
            margin-top: 1px; /* Memberikan jarak antara angka dan progress */
        }

        .progress-fill.blue { background: #122453; width: 80%; }
        .progress-fill.orange { background: #FCE7D9; width: 50%; }

        /* Action Buttons */
        .actions {
            padding: 10px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            color: white;
            font-size: 14px;
            border-radius: 10px;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center; /* Center the text and icon */
        }

        .btn img {
            width: 20px; /* Adjust the size of the icon */
            margin-right: 5px; /* Space between the icon and text */
        }

        .btn-add { background-color: #122453; }
        .btn-edit { background-color: #28D82B; }
        .btn-delete { background-color: #E30707; }


        /* Table */
        .table-container {
            padding: 0 20px 20px;
            flex: 1; /* Biar table bisa mendorong footer ke bawah */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        thead {
            background: #122453;
            color: white;
        }
        th, td {
            padding: 10px;
            font-size: 14px;
            text-align: left;
        }
        tbody tr:nth-child(even) {
            background: #f8f8f8;
        }
        td button {
            padding: 4px 10px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            color: white;
            font-family: inherit;
        }

        /* Footer - sticky di bawah */
        .footer {
            background: #122453;
            color: white;
            padding: 8px 20px;
            font-size: 12px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .footer .left {
            display: flex;
            gap: 10px;
            align-items: center;

        }

        .footer .social-icons {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .footer .social-icons img {
            width: 20px; /* Ukuran ikon */
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div>
        <div class="sidebar-header">
            <div style="display: flex; align-items: center;">
                <img src="/gambar/Logo 1 1.png" alt="BPBD Kota Malang" style="width: 20px;">
                <img src="/gambar/Logo_Kabupaten_Malang 1.png" alt="Kabupaten Malang" style="width: 20px; margin-left: 1px;">
            </div>
            <h2 style="margin-left: 8px;">Informasi Kejadian Kab Malang</h2>
        </div>

<div class="sidebar-menu">
    <a href="#" class="active">
        <img src="/gambar/lg_rmh.png" alt="Icon" style="width: 20px; margin-right: 10px;">
        Beranda
    </a>
    <a href="#">
        <img src="/gambar/lg_kejadian.png" alt="Kejadian" style="width: 20px; margin-right: 10px;">
        Kejadian
    </a>
    <a href="#">
        <img src="/gambar/lg_gempaBumi.png" alt="Gempa Bumi" style="width: 20px; margin-right: 10px;">
        Gempa Bumi
    </a>
    <a href="#">
        <img src="/gambar/lg_hujan.png" alt="Hari Hujan" style="width: 20px; margin-right: 10px;">
        Hari Hujan & Tanpa Hujan
    </a>
    <a href="#">
        <img src="/gambar/lg_gelombang.png" alt="Tinggi Gelombang" style="width: 20px; margin-right: 10px;">
        Tinggi Gelombang
    </a>
    <a href="#">
        <img src="/gambar/lg_destana.png" alt="DESTANA" style="width: 20px; margin-right: 10px;">
        DESTANA Kab. Malang
    </a>
    <a href="#">
        <img src="/gambar/lg_spab.png" alt="SPAB" style="width: 20px; margin-right: 10px;">
        SPAB Kab. Malang
    </a>
</div>

    </div>
</div>

<!-- Main Content -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div class="welcome">
            <h1>Selamat Datang!</h1>
            <p id="current-date"></p> <!-- Tanggal dan waktu akan ditampilkan di sini -->
        </div>
        <div class="topbar-right">
            <div class="search">
                🔍 <input type="text" placeholder="Cari...">
            </div>
            <!-- Ikon Notifikasi -->
        <div class="notification-icon">
            <img src="/gambar/notifikasi.png" alt="Notifikasi" style="width: 24px;">
        </div>
            <div class="profile">
            <img src="/gambar/profile.png" alt="Profile">
                <div class="profile-info">
                    <div class="name">Wildatul Fajriyah</div>
                    <div class="role">Admin</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card blue">
            <div class="stat-title">
                <span>Data Admin</span> 👤
            </div>
            <div class="stat-number">10</div>
            <div class="progress"><div class="progress-fill blue"></div></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-title">
                <span>Data User</span> 👥
            </div>
            <div class="stat-number">05</div>
            <div class="progress"><div class="progress-fill orange"></div></div>
        </div>
    </div>
    <!-- Action Buttons -->
    <div class="actions">
        <div class="search">
        🔍 <input type="text" placeholder="Cari...">
         </div>
        <button class="btn btn-add">
            <img src="/gambar/tambah.png" alt="Tambah" style="width: 15px; margin-right: 1px;"> Tambah
        </button>
        <button class="btn btn-edit">
            <img src="/gambar/edit.png" alt="Edit" style="width: 15px; margin-right: 1px;"> Edit
        </button>
        <button class="btn btn-delete">
            <img src="/gambar/sampah.png" alt="Hapus" style="width: 15px; margin-right: 1px;"> Hapus
        </button>
    </div>


    <!-- Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th></th>
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
                    <td><input type="checkbox"></td>
                    <td>1</td>
                    <td>Wildatul Fajriyah</td>
                    <td>wildatul.fajriyah@gmail.com</td>
                    <td>Admin</td>
                    <td>Online</td>
                    <td>
                        <button class="btn btn-edit">
                            <img src="/gambar/edit.png" alt="Edit" style="width: 20px; margin-right: 1px;">
                        </button>
                        <button class="btn btn-delete">
                            <img src="/gambar/sampah.png" alt="Hapus" style="width: 20px; margin-right: 1px;">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>2</td>
                    <td>Junior Herlambang</td>
                    <td>juniorherlambang@yahoo.com</td>
                    <td>Admin</td>
                    <td>Offline</td>
                    <td>
                        <button class="btn btn-edit">
                            <img src="/gambar/edit.png" alt="Edit" style="width: 10px; margin-right: 1px;"> Edit
                        </button>
                        <button class="btn btn-delete">
                            <img src="/gambar/sampah.png" alt="Hapus" style="width: 10px; margin-right: 1px;"> Hapus
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="left">
            <span>+62 822 4409 4886 | @bpbd_malangkab | BPBD KABUPATEN MALANG </span>
            <span></span>
        </div>
        <div class="social-icons">
            <img src="/gambar/Vector.png" alt="WhatsApp">
            <img src="/gambar/Vector (1).png" alt="Instagram">
            <img src="/gambar/Vector (2).png" alt="YouTube">
            <img src="/gambar/Vector (3).png" alt="Twitter">
        </div>
    </div>

    </div>

</div>

<!-- JavaScript untuk menampilkan tanggal dan waktu saat ini -->
<script>
    function updateDateTime() {
        const currentDate = new Date();
        const day = currentDate.toLocaleString('id-ID', { weekday: 'long' });
        const month = currentDate.toLocaleString('id-ID', { month: 'long' });
        const year = currentDate.getFullYear();
        const hours = currentDate.getHours().toString().padStart(2, '0');
        const minutes = currentDate.getMinutes().toString().padStart(2, '0');
        const seconds = currentDate.getSeconds().toString().padStart(2, '0');
        const formattedDate = `${day}, ${currentDate.getDate()} ${month} ${year}, ${hours}:${minutes}:${seconds}`;
        document.getElementById('current-date').textContent = formattedDate;
    }

    setInterval(updateDateTime, 1000); // Update setiap detik
    updateDateTime(); // Panggil fungsi saat pertama kali dimuat
</script>

</body>
</html>
