{{-- resources/views/formDashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Kejadian')

@section('content')
    {{-- Header --}}
    <header class="bg-blue-600 text-white p-4 shadow">
        <h1 class="text-xl font-bold">Dashboard Kejadian</h1>
    </header>

    {{-- Content --}}
    <main class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Peta --}}
            <div class="bg-white rounded-xl shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Peta Kejadian</h2>
                <div id="map" class="w-full h-80 rounded"></div>
            </div>

            {{-- Chart Tahun --}}
            <div class="bg-white rounded-xl shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Grafik Kejadian per Tahun</h2>
                <canvas id="chartTahun" class="w-full h-80"></canvas>
            </div>

            {{-- Chart Bulan --}}
            <div class="bg-white rounded-xl shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Grafik Kejadian per Bulan</h2>
                <canvas id="chartBulan" class="w-full h-80"></canvas>
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

    {{-- Script --}}
    <script>
        // Leaflet Map
        const map = L.map('map').setView([-7.9666, 112.6326], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        L.marker([-7.9666, 112.6326]).addTo(map).bindPopup("Malang");

        // Chart Tahun
        const ctx1 = document.getElementById('chartTahun').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['2021','2022','2023','2024','2025'],
                datasets: [{
                    label: 'Jumlah Kejadian',
                    data: [12, 19, 7, 15, 10],
                    backgroundColor: 'rgba(37, 99, 235, 0.7)'
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        // Chart Bulan
        const ctx2 = document.getElementById('chartBulan').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                datasets: [{
                    label: 'Jumlah Kejadian',
                    data: [2, 4, 3, 6, 8, 5, 7, 6, 4, 3, 2, 1],
                    borderColor: 'rgba(37, 99, 235, 1)',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    </script>
@endsection
