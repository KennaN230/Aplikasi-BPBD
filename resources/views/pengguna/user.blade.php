@extends('layouts.app')

<!-- Dashboard Pengguna -->
 <div
<div class="container mt-5">
  <h2 class="mb-4">👤 Dashboard Pengguna</h2>

  <!-- Ringkasan / Stats -->
  <div class="row g-4">
    <!-- Profil -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <img src="https://via.placeholder.com/80" class="rounded-circle mb-3" alt="Avatar">
          <h5 class="card-title">Nama Pengguna</h5>
          <p class="card-text">email@example.com</p>
          <a href="#" class="btn btn-primary btn-sm">Edit Profil</a>
        </div>
      </div>
    </div>

    <!-- Statistik Aktivitas -->
    <div class="col-md-4">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <h6 class="card-subtitle mb-2 text-muted">Jumlah Aktivitas</h6>
          <h3>24</h3>
          <a href="#" class="btn btn-outline-primary btn-sm mt-2">Lihat Detail</a>
        </div>
      </div>
    </div>

    <!-- Titik Panas / Notifikasi -->
    <div class="col-md-4">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <h6 class="card-subtitle mb-2 text-muted">Titik Panas Terkini</h6>
          <h3>5</h3>
          <a href="/titikpanas" class="btn btn-outline-danger btn-sm mt-2">Cek Sekarang</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Navigasi Cepat -->
  <div class="mt-5">
    <h5>Menu Cepat</h5>
    <div class="row g-3">
      <div class="col-md-3 col-6">
        <a href="/aktivitas" class="btn btn-light w-100 shadow-sm text-start">
          <i class="bi bi-list-task me-2"></i> Aktivitas
        </a>
      </div>
      <div class="col-md-3 col-6">
        <a href="/laporan" class="btn btn-light w-100 shadow-sm text-start">
          <i class="bi bi-file-text me-2"></i> Laporan
        </a>
      </div>
      <div class="col-md-3 col-6">
        <a href="/settings" class="btn btn-light w-100 shadow-sm text-start">
          <i class="bi bi-gear me-2"></i> Pengaturan
        </a>
      </div>
      <div class="col-md-3 col-6">
        <a href="/logout" class="btn btn-light w-100 shadow-sm text-start text-danger">
          <i class="bi bi-box-arrow-right me-2"></i> Keluar
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
