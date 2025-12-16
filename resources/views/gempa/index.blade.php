@extends('layouts.app')

@section('title', 'Dashboard Gempa Bumi')

@section('content')
<div class="container-fluid py-4">
  <!-- Topbar -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="fw-bold text-primary">Dashboard Gempa Bumi</h1>
      <p id="current-date" class="text-muted small"></p>
    </div>
    <div class="dash-user d-flex align-items-center gap-2">
  <!-- Foto Profil -->
  <img class="avatar rounded-circle" 
       src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('gambar/profile.png') }}" 
       alt="Foto {{ $user->nama ?? $user->name }}" 
       width="42" height="42">

  <!-- Nama dan Role -->
  <div class="flex-grow-1">
    <div class="fw-semibold">{{ $user->nama ?? $user->name }}</div>
    <div class="small text-muted text-capitalize">{{ ucfirst(strtolower($user->role ?? 'User')) }}</div>
  </div>

  <!-- Dropdown Menu -->
  <div class="dropdown ms-auto">
    <button type="button" class="btn btn-cream btn-pill-sm" data-bs-toggle="dropdown" aria-label="Menu profil">
      <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end dropdown-cream shadow-sm">
      @if (Route::has('profile.edit'))
        <li>
          <a class="dropdown-item" href="{{ route('profile.edit') }}">
            <i class="bi bi-person me-2"></i> Edit Profil
          </a>
        </li>
        <li><hr class="dropdown-divider" style="margin:.25rem 0;border-color:var(--cream-hover)"></li>
      @endif
      <li>
        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">@csrf
          <button class="dropdown-item text-danger" type="submit">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
          </button>
        </form>
      </li>
    </ul>
  </div>
</div>
  </div>

  <!-- Charts -->
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card bg-dark text-white shadow">
        <div class="card-body">
          <h5 class="card-title">Jumlah SR Gempa</h5>
          <div id="totalGempa" class="badge bg-primary position-absolute top-0 end-0 m-3">Jumlah Gempa 0</div>
          <canvas id="srChart" height="180"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card bg-dark text-white shadow">
        <div class="card-body">
          <h5 class="card-title">Catatan Gempa per Bulan</h5>
          <canvas id="bulanChart" height="180"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="d-flex justify-content-between align-items-center flex-wrap my-4">
    <a href="{{ route('gempa.create') }}" class="btn btn-primary">+ Tambah Data</a>
    <div class="d-flex flex-wrap gap-2">
      <select id="bulanFilter" class="form-select border-warning rounded-pill" style="width:130px">
        <option value="">Bulan</option>
        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
          <option value="{{ $i+1 }}">{{ $bulan }}</option>
        @endforeach
      </select>
      <select id="tahunFilter" class="form-select border-warning rounded-pill" style="width:100px">
        <option value="">Tahun</option>
        @for($i=2023; $i<=2025; $i++)
          <option value="{{ $i }}">{{ $i }}</option>
        @endfor
      </select>
      <input type="date" id="startDate" class="form-control border-warning rounded-pill">
      <input type="date" id="endDate" class="form-control border-warning rounded-pill">
      <a href="{{ route('gempa.cetak.pdf') }}" class="btn btn-danger" target="_blank">Cetak PDF</a>
    </div>
  </div>

  <!-- Table -->
  <div class="card shadow">
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>SR</th>
            <th>Waktu</th>
            <th>Lokasi Gempa</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="infoTitle">Detail Gempa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="infoBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="mt-4 text-center text-muted small">
    &copy; BPBD Kabupaten Malang | +62 822 4409 4886 | @bpbd_malangkab
  </footer>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function updateDateTime(){
    const d = new Date();
    const day = d.toLocaleString('id-ID',{ weekday:'long' });
    const month = d.toLocaleString('id-ID',{ month:'long' });
    document.getElementById('current-date').textContent =
      `${day}, ${d.getDate()} ${month} ${d.getFullYear()}, ${d.toLocaleTimeString('id-ID')}`;
  }
  setInterval(updateDateTime,1000);
  updateDateTime();

  const dataGempa = @json($gempa);
  let srChartInstance, bulanChartInstance;

  // === FILTER BERDASARKAN TANGGAL ===
function applyFilters() {
    const bulan  = document.getElementById('bulanFilter').value;
    const tahun  = document.getElementById('tahunFilter').value;
    const start  = document.getElementById('startDate').value;
    const end    = document.getElementById('endDate').value;

    let filtered = dataGempa;

    // === FILTER BERDASARKAN BULAN ===
    if (bulan) {
        filtered = filtered.filter(g => {
            const t = new Date(g.tanggal);
            return (t.getMonth() + 1) == bulan;
        });
    }

    // === FILTER BERDASARKAN TAHUN ===
    if (tahun) {
        filtered = filtered.filter(g => {
            const t = new Date(g.tanggal);
            return t.getFullYear() == tahun;
        });
    }

    // === FILTER RANGE TANGGAL ===
    if (start) {
        filtered = filtered.filter(g => new Date(g.tanggal) >= new Date(start));
    }
    if (end) {
        filtered = filtered.filter(g => new Date(g.tanggal) <= new Date(end));
    }

    // Render ulang tabel & chart
    renderTable(filtered);
    renderCharts(filtered);

    // Update link PDF
    updatePdfLink();
}

// Event Listener
document.getElementById('bulanFilter').addEventListener('change', applyFilters);
document.getElementById('tahunFilter').addEventListener('change', applyFilters);
document.getElementById('startDate').addEventListener('change', applyFilters);
document.getElementById('endDate').addEventListener('change', applyFilters);

  function renderTable(data){
    const tbody=document.querySelector("table tbody");
    tbody.innerHTML="";
    data.forEach((g,i)=>{
      const tgl=new Date(g.tanggal);
      const tanggalPendek=tgl.toLocaleDateString('id-ID',{day:'2-digit',month:'short'}).replace('.','');
      tbody.insertAdjacentHTML("beforeend",`
        <tr>
          <td>${i+1}</td><td>${tanggalPendek}</td><td>${g.sr}</td><td>${g.waktu.slice(0,5)}</td>
          <td>${g.gempa}</td><td>${g.latitude??'-'}</td><td>${g.longitude??'-'}</td><td>${g.keterangan}</td>
          <td>
            <button class="btn btn-info btn-sm" onclick="showInfo(${g.id})" data-bs-toggle="modal" data-bs-target="#infoModal">Info</button>
            <a href="/gempa/${g.id}/edit" class="btn btn-success btn-sm">Edit</a>
            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(${g.id})">Hapus</button>
            <form id="delete-form-${g.id}" action="/gempa/${g.id}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
          </td>
        </tr>`);
    });
  }

  function showInfo(id){
    const g=dataGempa.find(x=>x.id==id);
    if(!g)return;
    const tglFull=new Date(g.tanggal).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'});
    document.getElementById('infoTitle').textContent="Detail Gempa - "+tglFull;
    document.getElementById('infoBody').innerHTML=`
      <p><b>Tanggal:</b> ${tglFull}</p>
      <p><b>Waktu:</b> ${g.waktu}</p>
      <p><b>Lokasi:</b> ${g.gempa}</p>
      <p><b>SR:</b> ${g.sr}</p>
      <p><b>Latitude:</b> ${g.latitude??'-'}</p>
      <p><b>Longitude:</b> ${g.longitude??'-'}</p>
      <p><b>Keterangan:</b> ${g.keterangan}</p>`;
  }

  function confirmDelete(id){
    Swal.fire({
      title:'Hapus data ini?',
      text:'Data yang dihapus tidak dapat dikembalikan!',
      icon:'warning',
      showCancelButton:true,
      confirmButtonColor:'#d33',
      cancelButtonColor:'#aaa',
      confirmButtonText:'Ya, hapus',
      cancelButtonText:'Batal'
    }).then(res=>{
      if(res.isConfirmed) document.getElementById('delete-form-'+id).submit();
    });
  }

  function renderCharts(data){
    document.getElementById('totalGempa').textContent="Jumlah Gempa "+data.length;
    if(srChartInstance) srChartInstance.destroy();
    if(bulanChartInstance) bulanChartInstance.destroy();

    const srCount={};
    data.forEach(g=>{ const lbl="SR "+g.sr; srCount[lbl]=(srCount[lbl]||0)+1; });

    srChartInstance=new Chart(document.getElementById('srChart'),{
      type:'bar',
      data:{ labels:Object.keys(srCount), datasets:[{ data:Object.values(srCount), backgroundColor:'#3B82F6' }] },
      options:{ indexAxis:'y', plugins:{ legend:{display:false} } }
    });

    const bulanCount={};
    data.forEach(g=>{ const bln=new Date(g.tanggal).toLocaleString('id-ID',{month:'long'}); bulanCount[bln]=(bulanCount[bln]||0)+1; });

    bulanChartInstance=new Chart(document.getElementById('bulanChart'),{
      type:'bar',
      data:{ labels:Object.keys(bulanCount), datasets:[{ label:'Jumlah Gempa', data:Object.values(bulanCount), backgroundColor:'#38bdf8' }] },
      options:{ plugins:{ legend:{ labels:{ color:'#fff' } } } }
    });
  }

  renderTable(dataGempa);
  renderCharts(dataGempa);
</script>
@endpush  