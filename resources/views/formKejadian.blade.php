@extends('layouts.app')

@section('title', 'Dashboard Kejadian')

@section('content')

{{-- ====== PAGE HEADER ====== --}}
{{-- ====== HEADER SELAMAT DATANG & PROFIL USER ====== --}}
<div class="card shadow-sm border-0 mb-4">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

    {{-- Bagian kiri: ucapan selamat datang --}}
    <div>
      <h2 class="mb-1">Kejadian</h2>
      <div class="dash-sub text-muted">
        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }} —
        <span id="clock">--:--:--</span>
      </div>
    </div>

    {{-- Bagian kanan: profil user --}}
    <div class="dash-user d-flex align-items-center gap-2">
      <img class="avatar rounded-circle border"
           src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('gambar/profile.png') }}"
           alt="Foto {{ $user->nama ?? $user->name }}"
           width="48" height="48">

      <div>
        <div class="fw-semibold">{{ $user->nama ?? $user->name }}</div>
        <div class="small text-muted">{{ ucfirst(strtolower($user->role ?? 'User')) }}</div>
      </div>

      <div class="dropdown ms-2">
        <button type="button" class="btn btn-cream btn-pill-sm" data-bs-toggle="dropdown" aria-label="Menu profil">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end dropdown-cream">
          @if (Route::has('profile.edit'))
            <li>
              <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="bi bi-person me-2"></i> Edit Profil
              </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
          @endif
          <li>
            <form action="{{ route('logout') }}" method="POST" class="m-0">@csrf
              <button class="dropdown-item text-danger" type="submit">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
              </button>
            </form>
          </li>
        </ul>
      </div>
    </div>

  </div>
</div>
<script>
  // Jam Digital
  function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('clock').textContent = `${h}:${m}:${s}`;
  }
  setInterval(updateClock, 1000);
  updateClock();
</script>


{{-- ====== CONTENT ====== --}}
<div class="row g-4">

    <div class="row g-4">
    {{-- === KARTU: PETA === --}}
    <div class="col-lg-6">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Sebaran Jumlah Kejadian Bencana per Kecamatan</h6>
                <span class="badge bg-light text-primary">2025</span>
            </div>
            <div class="card-body p-0">
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>

    {{-- === KARTU: TABEL DATA === --}}
    <div class="col-lg-6">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Tabel Kejadian Bencana</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('kejadian.create') }}" class="btn btn-warning btn-sm fw-bold">+ Tambah</a>
                    <button class="btn btn-success btn-sm fw-bold">Unduh</button>
                </div>
            </div>
            <div class="card-body">
                {{-- Filter tanggal --}}
                <form action="{{ route('kejadian.filter') }}" method="GET" class="d-flex gap-2 mb-3">
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request('tanggal'))
                        <a href="{{ route('kejadian.printByTanggal', ['tanggal' => request('tanggal')]) }}" target="_blank"
                           class="btn btn-success btn-sm">🖨️ Print</a>
                    @endif
                </form>

                {{-- Tabel data --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>Nama Kejadian</th>
                                <th>Hari</th>
                                <th>Tanggal</th>
                                <th>Kecamatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kejadian as $k)
                                <tr>
                                    <td>{{ $k->nama_kejadian ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($k->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d F Y') }}</td>
                                    <td>{{ $k->kecamatan->kecamatan ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column gap-2">
                                            <a href="{{ route('kejadian.show', $k->id_kejadian) }}" class="btn btn-primary btn-sm">ℹ Info</a>
                                            <a href="{{ route('kejadian.edit', $k->id_kejadian) }}" class="btn btn-warning btn-sm">✎ Edit</a>
                                            <a href="{{ route('kejadian.print', ['id_kejadian' => $k->id_kejadian]) }}" target="_blank" class="btn btn-success btn-sm">🖨 Print</a>
                                            <form action="{{ route('kejadian.destroy', $k->id_kejadian) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">🗑 Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>


    {{-- Chart Tahun --}}
<div class="card shadow border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Grafik Jumlah Kejadian per Tahun</h6>
        <div>
            <select id="filterTahun" class="form-select form-select-sm bg-light border-0">
                @for($y = date('Y'); $y >= 2021; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="card-body">
        <canvas id="chartTahun" style="height: 300px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Ambil data kejadian dari backend
    {
    const kejadianList = @json($kejadian);

    const tahunCounts = {};
    kejadianList.forEach(k => {
        const year = new Date(k.tanggal).getFullYear();
        tahunCounts[year] = (tahunCounts[year] || 0) + 1;
    });

    const labels = Object.keys(tahunCounts).sort();
    const data = Object.values(tahunCounts);

    const ctx = document.getElementById('chartTahun').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Kejadian',
                data: data,
                backgroundColor: 'rgba(37,99,235,0.8)',
                borderRadius: 6
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
</script>
</div>
</div>

{{-- ====== ASSETS (CDN) ====== --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

{{-- ====== SCRIPTS ====== --}}
<script>
    const kejadianList = @json($kejadian);

    // ==== Leaflet Map ====
    const map = L.map('map').setView([-7.976597203645272, 112.63315165870563], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // marker
    kejadianList.forEach(k => {
        if (k.latitude && k.longitude) {
            L.marker([k.latitude, k.longitude])
                .addTo(map)
                .bindPopup(`<b>Kejadian:</b> ${k.nama_kejadian?.nama_kejadian ?? '-'}<br>
                            <b>Deskripsi:</b> ${k.deskripsi ?? '-'}<br>
                            <b>Kecamatan:</b> ${k.kecamatan?.kecamatan ?? '-'}`);
        }
    });

    // heatmap
    const heatData = kejadianList
        .filter(k => k.latitude && k.longitude)
        .map(k => [k.latitude, k.longitude, 0.7]);

    L.heatLayer(heatData, {
        radius: 25, blur: 15, maxZoom: 17,
        gradient: {0.1: 'red', 0.3: 'red', 0.6: 'red', 1.0: 'red'}
    }).addTo(map);

    // Chart Tahun
    new Chart(document.getElementById('chartTahun').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['2021','2022','2023','2024','2025'],
            datasets: [{
                label: 'Jumlah Kejadian',
                data: [57,165,73,140,98],
                backgroundColor: 'rgba(37,99,235,0.8)',
                borderRadius: 6
            }]
        },
        options: {
            scales: {y: {beginAtZero: true}},
            plugins: {legend: {display: false}},
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Chart Bulan
    new Chart(document.getElementById('chartBulan').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGU','SEP','OKT','NOV','DES'],
            datasets: [{
                label: 'Jumlah Kejadian',
                data: [2,4,3,6,8,5,7,6,4,3,2,1],
                borderColor: 'rgba(37,99,235,1)',
                backgroundColor: 'rgba(37,99,235,0.15)',
                fill: true,
                tension: .35
            }]
        },
        options: {
            plugins: {legend: {display: false}},
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection
