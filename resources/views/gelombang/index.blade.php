{{-- resources/views/gelombang/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Informasi Tinggi Gelombang')

@push('styles')
<style>
  :root{
    --navy:#0f2a4a; --cream:#efe8e0; --cream-hover:#e6ddd3;
  }
  main.app-main { padding-top: 10px !important; }

  .dash-header{ margin:6px 0 18px; display:flex; align-items:center; gap:18px; padding:0 20px; }
  .dash-header h2{font-weight:800;margin:0;color:#132d55}
  .dash-sub{color:#0B1C3F}
  .dash-user{display:flex;align-items:center;gap:12px}
  .dash-user .avatar{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #f3e9d2;}
  .btn-cream{ background:var(--cream)!important; border-color:var(--cream)!important; color:#0f2a4a; box-shadow:none!important; }
  .btn-cream:hover{ background:var(--cream-hover)!important; border-color:var(--cream-hover)!important; }
  .dropdown-cream.dropdown-menu{ background:var(--cream); border:0; border-radius:14px; box-shadow:0 6px 16px rgba(0,0,0,0.1); overflow:hidden; }

  .judul { color:#122453; font-size:22px; font-weight:700; text-align:center; margin:10px 0 20px; }

  .filter-bar {
    display:flex; align-items:center; gap:10px; padding:15px 20px;
    background:#fff; border-radius:8px; margin:0 20px 20px;
    box-shadow:0 2px 6px rgba(0,0,0,0.05); flex-wrap:wrap;
  }

  .custom-date, select.custom-date {
    border:2px solid orange; border-radius:8px;
    padding:8px 12px; font-weight:600; font-size:14px;
    cursor:pointer; color:#122453; background:white;
  }

  .btn { padding:8px 14px; border:none; color:white; font-size:13px; border-radius:6px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-weight:600; }
  .btn-add { background:#122453; }
  .btn-edit { background:#28D82B; }
  .btn-delete { background:#E30707; }
  .btn-pdf { background:#fd7e14; }

  table { width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; }
  thead { background:#122453; color:white; }
  th, td { padding:10px; font-size:14px; text-align:left; }
  tbody tr:nth-child(even) { background:#f8f8f8; }

  .table-wrap { padding:0 20px; margin-bottom:20px; }

  .chart-container { background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin:0 20px 20px; }

  .pagination { display:flex; justify-content:center; gap:8px; margin-top:10px; }
  .pagination a, .pagination span { padding:6px 10px; border-radius:6px; background:#fff; text-decoration:none; color:#122453; font-size:13px; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
  .pagination .active span { background:#122453; color:white; }
</style>
@endpush


@section('content')

@php
  $me = auth()->user();
  $avatarUrl = ($me && $me->photo) ? asset('storage/'.$me->photo) : asset('gambar/profile.png');
  $avatarUrl .= '?t='.(optional($me->updated_at)->timestamp ?? time());
@endphp

{{-- ==== HEADER PENGGUNA ==== --}}
<div class="dash-header">
  <div>
    <h2>Informasi Tinggi Gelombang</h2>
    <div class="dash-sub">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
    <div class="dash-sub" id="clock">--:--:--</div>
  </div>

  <div class="ms-auto d-flex align-items-center">
    <div class="dash-user">
      <img class="avatar" src="{{ $avatarUrl }}" alt="Foto {{ $me->nama ?? $me->name }}">
      <div>
        <div class="fw-semibold">{{ $me->nama ?? $me->name }}</div>
        <div class="small text-muted">{{ ucfirst(strtolower($me->role ?? 'User')) }}</div>
      </div>
      <div class="dropdown">
        <button type="button" class="btn btn-cream btn-pill-sm" data-bs-toggle="dropdown" aria-label="Menu profil">
          <i class="bi bi-three-dots"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end dropdown-cream">
          @if (Route::has('profile.edit'))
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Edit Profil</a></li>
            <li><hr class="dropdown-divider"></li>
          @endif
          <li>
            <form action="{{ route('logout') }}" method="POST">@csrf
              <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

{{-- ======= ISI HALAMAN ======= --}}

@if(session('success'))
  <div id="notif-success" class="alert alert-success mx-4">
    ✅ {{ session('success') }}
  </div>
@endif

@php
  $query = array_filter(request()->only(['bulan','tahun','tanggal_awal','tanggal_akhir']));
@endphp

<form method="GET" action="{{ route('gelombang.index') }}" class="filter-bar">
  <select name="bulan" class="custom-date">
      <option value="">Semua Bulan</option>
      @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
          <option value="{{ $num }}" {{ ($query['bulan'] ?? '') == $num ? 'selected' : '' }}>
              {{ $nama }}
          </option>
      @endforeach
  </select>

  <select name="tahun" class="custom-date">
      <option value="">Semua Tahun</option>
      @for($y = date('Y'); $y >= 2015; $y--)
          <option value="{{ $y }}" {{ ($query['tahun'] ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
      @endfor
  </select>

  <input type="date" name="tanggal_awal" value="{{ $query['tanggal_awal'] ?? '' }}" class="custom-date">
  <input type="date" name="tanggal_akhir" value="{{ $query['tanggal_akhir'] ?? '' }}" class="custom-date">

  <button type="submit" class="btn btn-add">Filter</button>

  <a href="{{ route('gelombang.create') }}" class="btn btn-add ms-auto">
      <img src="/gambar/tambah.png" style="width:15px;"> Tambah
  </a>

  <a href="{{ route('gelombang.cetakpdf', $query) }}" class="btn btn-pdf">Cetak PDF</a>
  <a href="{{ route('gelombang.pdfgrafik', $query) }}" class="btn btn-pdf">Cetak Grafik</a>
</form>

<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Tinggi Gelombang Maks</th>
        <th>Tinggi Gelombang Min</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($gelombang as $item)
      <tr>
        <td>{{ $loop->iteration + ($gelombang->currentPage() - 1) * $gelombang->perPage() }}</td>
        <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
        <td>{{ $item->tinggi_gelombang_max }} m</td>
        <td>{{ $item->tinggi_gelombang_min }} m</td>
        <td>
          <button type="button" class="btn btn-edit"
            onclick="openEditModal('{{ $item->id }}', '{{ $item->tanggal }}', '{{ $item->tinggi_gelombang_min }}', '{{ $item->tinggi_gelombang_max }}')">Edit</button>
          <form action="{{ route('gelombang.destroy',$item->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center text-danger fw-bold py-3">
          🚫 Tidak ada data untuk filter yang dipilih.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">
    {{ $gelombang->links('pagination::bootstrap-5') }}
  </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
  <div style="background:white; padding:20px; border-radius:8px; width:350px; box-shadow:0 4px 8px rgba(0,0,0,0.2);">
    <h3 style="margin-bottom:10px; text-align:center; color:#122453;">Edit Data Gelombang</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <label>Tanggal:</label>
      <input type="date" id="editTanggal" name="tanggal" required class="form-control mb-2">
      <label>Tinggi Gelombang Maks (m):</label>
      <input type="number" id="editMax" name="tinggi_gelombang_max" step="0.1" required class="form-control mb-2">
      <label>Tinggi Gelombang Min (m):</label>
      <input type="number" id="editMin" name="tinggi_gelombang_min" step="0.1" required class="form-control mb-3">
      <div class="text-end">
        <button type="button" onclick="closeEditModal()" class="btn btn-delete">Batal</button>
        <button type="submit" class="btn btn-edit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="chart-container">
  <h3 style="text-align:center; color:#122453;">Grafik Tinggi Gelombang Harian</h3>
  <canvas id="gelombangChart" height="120"></canvas>
  <hr class="my-3">
  <h4 style="text-align:center; color:#122453;">Rata-Rata Tinggi Gelombang per-Bulan</h4>
  <canvas id="rataChart" height="100"></canvas>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const s = String(now.getSeconds()).padStart(2,'0');
    document.getElementById('clock').textContent = `${h}:${m}:${s}`;
  }
  setInterval(updateClock, 1000);
  updateClock();

  function openEditModal(id, tanggal, min, max) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('editTanggal').value = tanggal;
    document.getElementById('editMin').value = min;
    document.getElementById('editMax').value = max;
    document.getElementById('editForm').action = `/gelombang/${id}`;
  }
  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  // === Grafik ===
  new Chart(document.getElementById('gelombangChart'), {
    type: 'line',
    data: {
      labels: {!! json_encode($grafik->pluck('tanggal')->map(fn($t) => (int)\Carbon\Carbon::parse($t)->format('d'))) !!},
      datasets: [
        { label: 'TINGGI GELOMBANG MINIMUM', data: {!! json_encode($grafik->pluck('tinggi_gelombang_min')) !!}, borderColor: '#00bcd4', borderWidth: 2, tension: 0.3 },
        { label: 'TINGGI GELOMBANG MAKSIMUM', data: {!! json_encode($grafik->pluck('tinggi_gelombang_max')) !!}, borderColor: '#2196f3', borderWidth: 2, tension: 0.3 }
      ]
    },
    options: { plugins:{legend:{position:'top'}}, scales:{x:{title:{display:true,text:'Tanggal'}},y:{title:{display:true,text:'Tinggi Gelombang (m)'},beginAtZero:true}} }
  });

  new Chart(document.getElementById('rataChart'), {
    type: 'bar',
    data: {
      labels: {!! json_encode($rataBulan->pluck('nama_bulan')) !!},
      datasets: [
        { label: 'GEL. MAX', data: {!! json_encode($rataBulan->pluck('rata_max')) !!}, backgroundColor: '#122453' },
        { label: 'GEL. MIN', data: {!! json_encode($rataBulan->pluck('rata_min')) !!}, backgroundColor: '#5e81f4' }
      ]
    },
    options: { plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true}} }
  });
</script>
@endpush
