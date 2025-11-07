<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Data Hujan</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    body {
      display: flex;
      background: #f9f6f2;
      min-height: 100vh;
    }
    .sidebar {
      width: 250px;
      background-color: #0A1F44;
      color: white;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .sidebar-header {
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .sidebar-header img {
      width: 28px;
      height: auto;
    }
    .sidebar-header h2 {
      font-size: 13px;
      font-weight: 600;
      line-height: 1.3;
    }
    .sidebar-menu {
      display: flex;
      flex-direction: column;
      margin-top: 10px;
    }
    .sidebar-menu a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 20px;
      color: white;
      text-decoration: none;
      font-size: 14px;
      border-left: 4px solid transparent;
      transition: 0.3s;
    }
    .sidebar-menu a img {
      width: 20px;
      height: auto;
    }
    .sidebar-menu a:hover, .sidebar-menu a.active {
      background-color: #F6F1ED;
      border-left: 4px solid orange;
      color: #122453;
      font-weight: bold;
    }
    .sidebar-menu a:hover img, .sidebar-menu a.active img {
      filter: brightness(0) saturate(100%) invert(30%) sepia(100%) saturate(500%) hue-rotate(180deg);
    }
    .main {
      flex: 1;
      padding: 20px;
      display: flex;
      flex-direction: column;
    }
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    .welcome h1 {
      font-size: 20px;
      font-weight: 600;
      color: #122453;
    }
    .profile {
      display: flex;
      align-items: center;
      gap: 10px;
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
    .form-card {
      background: white;
      border-radius: 10px;
      padding: 30px 20px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      text-align: center;
    }
    .form-card h2 {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #122453;
    }
    .form-group {
      margin-bottom: 18px;
      text-align: left;
    }
    .form-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 6px;
      font-weight: 500;
    }
    .form-group input, 
    .form-group select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .actions {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      gap: 20px;
    }
    .btn {
      padding: 10px 20px;
      border: none;
      font-size: 14px;
      border-radius: 6px;
      cursor: pointer;
      color: white;
      font-weight: 600;
      text-decoration: none;
    }
    .btn-save { background: #28D82B; }
    .btn-cancel { background: #E30707; }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="/gambar/Logo1.png" alt="BPBD Kota Malang">
      <img src="/gambar/Logo_Kabupaten_Malang 1.png" alt="Kabupaten Malang">
      <h2>Informasi Kejadian <br>Kab Malang</h2>
    </div>
    <div class="sidebar-menu">
      <a href="#"><img src="/gambar/lg_rmh.png" alt="Icon">Beranda</a>
      <a href="#"><img src="/gambar/lg_kejadian.png" alt="Kejadian">Kejadian</a>
      <a href="#"><img src="/gambar/lg_gempaBumi.png" alt="Gempa Bumi">Gempa Bumi</a>
      <a href="#" class="active"><img src="/gambar/lg_hujan.png" alt="Hari Hujan">Hari Hujan & Tanpa Hujan</a>
      <a href="#"><img src="/gambar/lg_gelombang.png" alt="Gelombang">Tinggi Gelombang</a>
      <a href="#"><img src="/gambar/lg_destana.png" alt="DESTANA">DESTANA Kab. Malang</a>
      <a href="#"><img src="/gambar/lg_spab.png" alt="SPAB">SPAB Kab. Malang</a>
    </div>
  </div>

  <!-- Main content -->
  <div class="main">
    <!-- Topbar -->
    <div class="topbar">
      <div class="welcome">
        <h1>Selamat Datang!</h1>
      </div>
      <div class="profile">
        <img src="/gambar/profile.png" alt="Profile">
        <div class="profile-info">
          <div class="name">Wildatul Fajriyah</div>
          <div class="role">Admin</div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="form-card">
      <h2>Tambah Data Hujan dan Tidak Hujan</h2>
      <form action="{{ route('rain.store') }}" method="POST">
        @csrf
        <div class="form-group">
          <label for="hari_tanggal">Hari Tanggal</label>
          <input type="date" id="hari_tanggal" name="hari_tanggal" required>
        </div>
        <div class="form-group">
          <label for="kecamatan">Kecamatan</label>
          <select id="kecamatan" name="kecamatan" required>
            <option value="">-- Pilih Kecamatan --</option>
            <option value="Ngantang">Ngantang</option>
            <option value="Karangploso">Karangploso </option>
            <option value="Pujon">Pujon </option>
            <option value="Singosari">Singosari </option>
            <option value="Kasembon">Kasembon </option>
            <option value="Pakis">Pakis </option>
            <option value="Lawang">Lawang </option>
            <option value="Jabung">Jabung </option>
            <option value="Dau">Dau </option>
            <option value="Tumpang">Tumpang </option>
            <option value="Wagir">Wagir </option>
            <option value="Poncokusumo">Poncokusumo </option>
            <option value="Tajinan">Tajinan </option>
            <option value="Ngajum">Ngajum </option>
            <option value="Pakisaji">Pakisaji </option>
            <option value="Wonosari">Wonosari </option>
            <option value="Donomulyo">Donomulyo </option>
            <option value="Ampelgading">Ampelgading </option>
            <option value="Pagak">Pagak </option>
            <option value="Turen">Turen </option>
            <option value="Pagelaran">Pagelaran </option>
            <option value="Sumbermanjing Wetan">Sumbermanjing Wetan </option>
            <option value="Tirtoyudo">Tirtoyudo </option>
            <option value="Gedangan">Gedangan </option>
            <option value="Bululawang">Bululawang </option>
            <option value="Kepanjen">Kepanjen </option>
            <option value="Gondanglegi">Gondanglegi </option>
            <option value="Bantur">Bantur </option>
            <option value="Dampit">Dampit </option>
            <option value="Wajak">Wajak </option>
            <option value="Kalipare">Kalipare </option>
            <option value="Kromengan">Kromengan </option>
            <option value="Sumberpucung">Sumberpucung </option>
          </select>
        </div>
        <div class="form-group">
          <label for="hari_hujan">Hari Hujan</label>
          <input type="number" id="hari_hujan" name="hari_hujan" placeholder="Masukkan jumlah hari hujan" required>
        </div>
        <div class="form-group">
          <label for="hari_tidak_hujan">Hari Tidak Hujan</label>
          <input type="number" id="hari_tidak_hujan" name="hari_tidak_hujan" placeholder="Masukkan jumlah hari tidak hujan" required>
        </div>
        <div class="actions">
          <button type="submit" class="btn btn-save">Simpan</button>
          <a href="{{ route('rain.index') }}" class="btn btn-cancel">Batal</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>