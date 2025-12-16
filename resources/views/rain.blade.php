{{-- resources/views/hujan/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Informasi Hari Hujan')

@push('styles')
<style>
  :root{
    --navy:#0f2a4a; --cream:#efe8e0; --cream-hover:#e6ddd3;
    --tile-blue:#eef4ff; --tile-orange:#fff1e6;
    --accent:#ff7a00; --shadow:0 12px 28px rgba(0,0,0,.08);
    --radius:18px;
  }
  main.app-main { padding-top: 10px !important; }

  .dash-header{ margin:6px 0 18px; display:flex; align-items:center; gap:18px }
  .dash-header h2{font-weight:800;margin:0;color:#132d55}
  .dash-sub{color:#0B1C3F}
  .dash-user{display:flex;align-items:center;gap:12px}
  .dash-user .avatar{width:44px;height:44px;border-radius:50%;object-fit:cover}
  .btn-cream{ background:var(--cream)!important; border-color:var(--cream)!important; color:#0f2a4a; box-shadow:none!important; }
  .btn-cream:hover{ background:var(--cream-hover)!important; border-color:var(--cream-hover)!important; }
  .dropdown-cream.dropdown-menu{ background:var(--cream); border:0; border-radius:14px; box-shadow:var(--shadow); overflow:hidden; }

  .judul { color:#122453; font-size:22px; font-weight:700; margin:20px 0; text-align:center; }

  .filter-bar { display:flex; align-items:center; gap:10px; padding:15px 20px; border-radius:8px;
    margin-bottom:20px; box-shadow:0 2px 6px rgba(0,0,0,0.05); flex-wrap:wrap; background:#fff; }

  .custom-select, .custom-date {
    border:2px solid orange; border-radius:25px; padding:8px 15px;
    font-weight:600; font-size:14px; cursor:pointer;
  }

  .btn { padding:6px 10px; border:none; border-radius:6px; cursor:pointer; font-size:13px; }
  .btn-add { background:#122453; color:white; }
  .btn-edit { background:#28D82B; color:white; }
  .btn-delete { background:#E30707; color:white; }
  .btn-pdf { background:#fd7e14; color:#fff; font-weight:500; padding:6px 10px; border-radius:6px; }

  .table-wrap { background:white; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
  table { width:100%; border-collapse:collapse; }
  thead { background:#122453; color:white; }
  th, td { padding:10px; font-size:14px; text-align:left; }
  tbody tr:nth-child(even){ background:#f8f8f8; }

  .chart-container { background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin-top:20px; }
  .chart-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }

  #table-pagination { padding:10px; display:flex; gap:6px; flex-wrap:wrap; justify-content:center; }
  .btn-pg { padding:6px 12px; border:1px solid #ddd; background:#fff; border-radius:6px; cursor:pointer; font-size:14px; }
  .btn-pg.active { background:#122453; color:#fff; border-color:#122453; }
</style>
@endpush

@section('content')
@php
  $me = auth()->user();
  $avatarUrl = ($me && $me->photo) ? asset('storage/'.$me->photo) : asset('gambar/profile.png');
  $avatarUrl .= '?t='.(optional($me->updated_at)->timestamp ?? time());
@endphp

{{-- ===== HEADER ===== --}}
<div class="dash-header">
  <div>
    <h2>Informasi Hari Hujan</h2>
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
  {{-- Notifikasi --}}
  @if(session('error'))
    <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
  @endif

  {{-- Filter Bar --}}
  <form method="GET" action="{{ route('rain.index') }}" class="filter-bar">
    <input type="text" name="cari" class="form-control w-auto" placeholder="Cari Kecamatan..." value="{{ request('cari') }}">

    <select name="bulan" class="custom-select" onchange="this.form.submit()">
      <option value="">Semua Bulan</option>
      @foreach ([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
        <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $nama }}</option>
      @endforeach
    </select>

    <select name="tahun" class="custom-select" onchange="this.form.submit()">
      <option value="">Semua Tahun</option>
      @for ($y = 2023; $y <= now()->year; $y++)
        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
      @endfor
    </select>

    <input type="date" name="tanggal_awal" class="custom-date" value="{{ request('tanggal_awal') }}">
    <input type="date" name="tanggal_akhir" class="custom-date" value="{{ request('tanggal_akhir') }}">

    <button type="submit" class="btn btn-add">Filter</button>

    <a href="{{ route('rain.create') }}" class="btn btn-add ms-auto">
      <i class="bi bi-plus-lg"></i> Tambah
    </a>

    <a href="{{ route('rain.cetakpdf', request()->all()) }}" target="_blank" class="btn-pdf">
      Cetak PDF
    </a>
  </form>

  {{-- Tabel --}}
  <div class="table-wrap">
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
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($item->hari_tanggal)->translatedFormat('d F Y') }}</td>
            <td>{{ $item->kecamatan }}</td>
            <td>{{ $item->hari_hujan }}</td>
            <td>{{ $item->hari_tidak_hujan }}</td>
            <td>
              <button type="button" class="btn btn-edit btn-sm"
                onclick="openEditModal(
                  {{ $item->id }},
                  '{{ addslashes($item->kecamatan) }}',
                  '{{ $item->hari_hujan }}',
                  '{{ $item->hari_tidak_hujan }}',
                  '{{ \Carbon\Carbon::parse($item->hari_tanggal)->format('Y-m-d') }}'
                )"
                >
                Edit
              </button>
              <form action="{{ route('rain.destroy', $item->id) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
    <div id="table-pagination"></div>
  </div>

  {{-- Grafik --}}
  <div class="chart-container">
    <div class="chart-header">
      <h5 id="chartTitle">Grafik Hari Hujan</h5>
      <div class="d-flex gap-2">
        <select id="dataType" class="custom-select">
          <option value="hujan" selected>Hari Hujan</option>
          <option value="tidak_hujan">Hari Tidak Hujan</option>
          <option value="keduanya">Keduanya</option>
        </select>
        <a href="{{ route('rainpdfgrafik') }}" target="_blank" class="btn-pdf">Cetak Grafik</a>
      </div>
    </div>
    <canvas id="rainChart" height="100"></canvas>
  </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <h5>Edit Data</h5>
      <form id="editForm" method="POST">
        @csrf @method('PUT')
        <input type="hidden" id="editId" name="id">

        <div class="mb-2">
          <label>Tanggal</label>
          <input type="date" id="editTanggal" name="hari_tanggal" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Kecamatan</label>
          <input type="text" id="editKecamatan" name="kecamatan" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Hari Hujan</label>
          <input type="number" id="editHujan" name="hari_hujan" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Hari Tidak Hujan</label>
          <input type="number" id="editTidakHujan" name="hari_tidak_hujan" class="form-control" required>
        </div>

        <div class="text-end">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const labels = @json($grafik->pluck('kecamatan'));
  const hujan = @json($grafik->pluck('hari_hujan'));
  const tidak_hujan = @json($grafik->pluck('hari_tidak_hujan'));

  const ctx = document.getElementById('rainChart');
  let chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { label: 'Hari Hujan', data: hujan, backgroundColor: '#859fe4ff' },
        { label: 'Hari Tidak Hujan', data: tidak_hujan, backgroundColor: '#4636a2ff' },
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            // Paksa tampilkan angka bulat
            callback: function(value) {
              return Math.round(value);
            },
            stepSize: 1  // Setiap step 1 unit
          }
        }
      }
    }
});


  document.getElementById('dataType').addEventListener('change', e => {
    const val = e.target.value;
    if (val === 'hujan') chart.data.datasets = [{ label: 'Hari Hujan', data: hujan, backgroundColor: '#859fe4ff' }];
    else if (val === 'tidak_hujan') chart.data.datasets = [{ label: 'Hari Tidak Hujan', data: tidak_hujan, backgroundColor: '#4636a2ff' }];
    else chart.data.datasets = [
      { label: 'Hari Hujan', data: hujan, backgroundColor: '#859fe4ff' },
      { label: 'Hari Tidak Hujan', data: tidak_hujan, backgroundColor: '#4636a2ff' }
    ];
    chart.update();
  });

  function openEditModal(id, kecamatan, hujan, tidakHujan, tanggal){
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    
    // Set input value
    document.getElementById('editId').value = id;
    document.getElementById('editKecamatan').value = kecamatan;
    document.getElementById('editHujan').value = hujan;
    document.getElementById('editTidakHujan').value = tidakHujan;
    document.getElementById('editTanggal').value = tanggal;

    // Set form action secara dinamis pakai route helper Laravel
    const form = document.getElementById('editForm');
    form.action = "{{ url('/hujan') }}/" + id;

    modal.show();
}
</script>
@endpush
</body>
</html>