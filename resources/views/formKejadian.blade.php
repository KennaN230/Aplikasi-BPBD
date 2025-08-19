<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BPBD Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <!-- CSS -->
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
  </style>
</head>
<body class="flex bg-gray-100">

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
  <main class="flex-1 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Informasi Kejadian Kabupaten Malang</h2>
      <div class="flex items-center space-x-3">
        <span>Admin</span>
        <img src="https://via.placeholder.com/40" class="rounded-full" alt="User">
      </div>
    </div>

    <!-- Grid Content -->
    <div class="grid grid-cols-2 gap-6">
      <!-- Peta -->
      <div id="map" class="h-64 rounded-xl shadow"></div>

      <!-- Grafik -->
      <canvas id="chartTahun" class="bg-white rounded-xl shadow p-4"></canvas>
    </div>

    <!-- Grafik Per Bulan + Tabel -->
    <div class="grid grid-cols-2 gap-6 mt-6">
      <canvas id="chartBulan" class="bg-white rounded-xl shadow p-4"></canvas>
      <div class="bg-white rounded-xl shadow p-4">
        <h3 class="font-bold mb-2">Tabel Kejadian</h3>
        <table class="w-full text-sm border">
          <thead class="bg-blue-200">
            <tr>
              <th class="border px-2">No</th>
              <th class="border px-2">Kecamatan</th>
              <th class="border px-2">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border px-2">1</td>
              <td class="border px-2">Klojen</td>
              <td class="border px-2">12</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Infografis -->
    <div class="bg-white rounded-xl shadow p-4 mt-6">
      <h3 class="font-bold mb-2">Infografis Sebaran Kejadian</h3>
      <img src="https://via.placeholder.com/600x300" alt="Infografis" class="rounded-lg">
    </div>
  </main>

  <!-- Script Chart -->
  <script>
    const ctx1 = document.getElementById('chartTahun');
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: ['2021', '2022', '2023', '2024'],
        datasets: [{
          label: 'Jumlah Kejadian',
          data: [20, 35, 50, 40],
          backgroundColor: 'rgba(37, 99, 235, 0.8)'
        }]
      }
    });

    const ctx2 = document.getElementById('chartBulan');
    new Chart(ctx2, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [{
          label: 'Kejadian',
          data: [5, 10, 7, 15],
          borderColor: 'rgba(37, 99, 235, 1)',
          fill: false
        }]
      }
    });

    // Peta Leaflet
    var map = L.map('map').setView([-7.98, 112.63], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([-7.98, 112.63]).addTo(map).bindPopup('Kejadian di Malang');
  </script>

</body>
</html>
