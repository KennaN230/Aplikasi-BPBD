{{-- resources/views/rain/index.blade.php --}}
@extends('layouts.app')
@section('title','Hari Hujan & Tanpa Hujan')

@push('styles')
<style>
  main.app-main{ padding-top:6px !important; }
  .btn-pill-sm{border-radius:999px;padding:.35rem .9rem;font-weight:700}
  .btn-cream{background:#efe8e0;border-color:#efe8e0;color:#0f2a4a}
  .page-title-wrap{display:flex;align-items:center;justify-content:center;margin:2px 0 20px}
  .page-title{font-weight:800;color:#132d55;margin:0}
  .dash-header{margin:4px 0 8px;display:flex;align-items:center;gap:16px}
  .dash-sub{color:#0B1C3F}
  .filter-bar .form-control,.filter-bar .form-select{border-radius:12px}
  .table-search{position:relative;max-width:260px}
  .table-search .bi-search{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#97a0b0}
  .table-search input{padding-left:40px}
  .soft-card{border:0;border-radius:16px;box-shadow:0 12px 28px rgba(0,0,0,.08);overflow:hidden}
  .table-wrap{max-height:480px;overflow:auto;border-radius:12px}
  .table-soft thead th{position:sticky;top:0;z-index:2;background:#0f2a4a;color:#fff;border:0!important}
  .table-soft.table-compact{table-layout:fixed;border-collapse:separate;border-spacing:0}
  .table-soft.table-compact th,.table-soft.table-compact td{padding:.45rem .60rem;vertical-align:middle}
  .table-soft.table-compact thead th{padding:.55rem .60rem;font-size:.95rem}
  .nowrap{white-space:nowrap}
  .clip-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word}
  .col-no{width:56px}
  .col-tgl{width:120px}
  .col-aksi{width:220px}
  .chart-card{border:0;border-radius:16px;box-shadow:0 12px 28px rgba(0,0,0,.08)}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Str;
  $me          = auth()->user();
  $displayName = $me?->nama ?? $me?->name ?? 'Pengguna';
  $roleText    = $me?->role ? ucfirst(strtolower($me->role)) : 'User';
  $avatarUrl   = $me?->photo ? asset('storage/'.$me->photo) : asset('gambar/profile.png');
  $avatarUrl  .= '?t='.(optional($me?->updated_at)->timestamp ?: time());
  $isPaginator = is_object($data ?? null) && method_exists($data,'total');
@endphp

{{-- HEADER --}}
<div class="dash-header">
  <div>
    <h2 class="mb-1" style="font-weight:800;color:#132d55">Selamat Datang!</h2>
    <div class="dash-sub">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
    <div class="dash-sub" id="clock">--:--:--</div>
  </div>

  <div class="ms-auto d-flex align-items-center">
    <img class="me-2" src="{{ $avatarUrl }}" alt="Foto {{ $displayName }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover">
    <div class="me-2">
      <div class="fw-semibold">{{ $displayName }}</div>
      <div class="small text-muted">{{ $roleText }}</div>
    </div>
    <div class="dropdown">
      <button type="button" class="btn btn-cream btn-pill-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
      <ul class="dropdown-menu dropdown-menu-end" style="border-radius:14px;overflow:hidden">
        @if(Route::has('profile.edit'))
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

{{-- JUDUL --}}
<div class="page-title-wrap">
  <h2 class="page-title">Informasi Hari Hujan & Tanpa Hujan</h2>
</div>

{{-- FLASH (cukup satu) --}}
@if(session('ok') || session('success') || session('error'))
  @php
    $msg = session('ok') ?? session('success') ?? session('error');
    $cls = session('error') ? 'alert-danger' : 'alert-success';
  @endphp
  <div class="alert {{ $cls }}" id="flashOne">{{ $msg }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

{{-- FILTER BAR --}}
<form class="filter-bar mb-2" method="GET" action="{{ route('rain.index') }}">
  <div class="d-flex flex-wrap align-items-center gap-2">
    <div class="table-search">
      <i class="bi bi-search"></i>
      <input type="search" name="cari" class="form-control" value="{{ request('cari') }}" placeholder="Cari kecamatan…">
    </div>

    <select name="bulan" class="form-select" style="width:160px" onchange="this.form.submit()">
      <option value="">Semua Bulan</option>
      @foreach ([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
        <option value="{{ $num }}" @selected((string)request('bulan')===(string)$num)>{{ $nama }}</option>
      @endforeach
    </select>

    <select name="tahun" class="form-select" style="width:160px" onchange="this.form.submit()">
      <option value="">Semua Tahun</option>
      @for ($y = now()->year; $y >= now()->year-10; $y--)
        <option value="{{ $y }}" @selected((string)request('tahun')===(string)$y)>{{ $y }}</option>
      @endfor
    </select>

    <input type="date" name="tanggal_awal" class="form-control" style="width:170px" value="{{ request('tanggal_awal') }}">
    <input type="date" name="tanggal_akhir" class="form-control" style="width:170px" value="{{ request('tanggal_akhir') }}">

    <div class="ms-auto d-flex align-items-center gap-2">
      <button class="btn btn-outline-primary btn-pill-sm">Terapkan</button>

      {{-- Tambah --}}
      @if(Route::has('rain.create'))
        <a href="{{ route('rain.create') }}" class="btn btn-primary btn-pill-sm">
          <i class="bi bi-plus-circle me-1"></i> Tambah
        </a>
      @endif

      {{-- Cetak PDF (ikut filter aktif) --}}
      @if(Route::has('rain.cetakpdf'))
        <a class="btn btn-warning btn-pill-sm"
           href="{{ route('rain.cetakpdf', request()->query()) }}" target="_blank">
          <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
      @endif

      {{-- Export Excel opsional: jika ada route rain.export --}}
      @if(Route::has('rain.export'))
        <a class="btn btn-success btn-pill-sm"
           href="{{ route('rain.export', request()->query()) }}">
          <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </a>
      @endif
    </div>
  </div>
</form>

{{-- TABEL --}}
<div class="card soft-card mb-3">
  <div class="table-wrap">
    <table class="table table-hover align-middle mb-0 table-soft table-compact">
      <colgroup>
        <col class="col-no">
        <col class="col-tgl">
        <col>
        <col>
        <col>
        <col class="col-aksi">
      </colgroup>
      <thead>
      <tr>
        <th class="nowrap">No</th>
        <th class="nowrap">Tanggal</th>
        <th>Kecamatan</th>
        <th class="nowrap">Hari Hujan</th>
        <th class="nowrap">Hari Tidak Hujan</th>
        <th class="nowrap">Aksi</th>
      </tr>
      </thead>
      <tbody>
      @forelse($data as $i => $item)
        <tr>
          <td class="nowrap">{{ $isPaginator ? ($data->firstItem()+$i) : ($i+1) }}</td>
          <td class="nowrap">{{ \Carbon\Carbon::parse($item->hari_tanggal)->translatedFormat('d F Y') }}</td>
          <td class="clip-2">{{ $item->kecamatan }}</td>
          <td class="nowrap">{{ $item->hari_hujan }}</td>
          <td class="nowrap">{{ $item->hari_tidak_hujan }}</td>
          <td class="text-nowrap">
            <button type="button" class="btn btn-sm btn-success btn-pill-sm me-1"
              onclick="openEditModal(
                {{ $item->id }},
                @js($item->kecamatan),
                '{{ $item->hari_hujan }}',
                '{{ $item->hari_tidak_hujan }}',
                '{{ \Carbon\Carbon::parse($item->hari_tanggal)->toDateString() }}'
              )">
              <i class="bi bi-pencil-square me-1"></i> Edit
            </button>

            <form action="{{ route('rain.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger btn-pill-sm"><i class="bi bi-trash me-1"></i> Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted">Tidak ada data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-body py-2">
    {{ $isPaginator ? $data->withQueryString()->links() : '' }}
  </div>
</div>

{{-- GRAFIK --}}
<div class="card chart-card p-3 mb-4">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h5 class="mb-0">Grafik</h5>
    <div class="d-flex align-items-center gap-2">
      <select id="dataType" class="form-select" style="width:220px">
        <option value="hujan" selected>Hari Hujan</option>
        <option value="tidak_hujan">Hari Tidak Hujan</option>
        <option value="keduanya">Keduanya</option>
      </select>
      <button id="btnChartPdf" class="btn btn-warning btn-pill-sm"><i class="bi bi-file-earmark-pdf me-1"></i> PDF Grafik</button>
    </div>
  </div>
  <h6 id="chartTitle" class="text-center text-primary mb-2">Hari Hujan Per Kecamatan</h6>
  <canvas id="rainChart" height="110"></canvas>
</div>

{{-- MODAL EDIT --}}
<div id="editModal" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" class="modal-content" method="POST">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Hujan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editId" name="id">
        <div class="mb-2">
          <label class="form-label">Tanggal</label>
          <input type="date" id="editTanggal" name="hari_tanggal" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Kecamatan</label>
          <input type="text" id="editKecamatan" name="kecamatan" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Hari Hujan</label>
          <input type="number" id="editHujan" name="hari_hujan" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Hari Tidak Hujan</label>
          <input type="number" id="editTidakHujan" name="hari_tidak_hujan" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-pill-sm" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success btn-pill-sm">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
  // Jam hidup
  const clockEl = document.getElementById('clock');
  if (clockEl){
    const pad = n => String(n).padStart(2,'0');
    const tick = () => { const d = new Date(); clockEl.textContent = `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`; };
    tick(); setInterval(tick,1000);
  }

  // Auto-hide 1 flash
  const one = document.getElementById('flashOne');
  if(one){ setTimeout(()=>{ one.style.transition='opacity .4s'; one.style.opacity='0'; setTimeout(()=>one.remove(),400); }, 2500); }

  // ===== Chart data (null-safe) =====
  const rawLabels = @json(($grafik ?? collect())->pluck('kecamatan') ?? []);
  const rawHujan  = @json(($grafik ?? collect())->pluck('hari_hujan') ?? []);
  const rawTH     = @json(($grafik ?? collect())->pluck('hari_tidak_hujan') ?? []);

  const merged = {};
  (rawLabels || []).forEach((k,i)=>{
    if(!merged[k]) merged[k] = {h:0, t:0};
    merged[k].h += parseFloat(rawHujan[i]||0);
    merged[k].t += parseFloat(rawTH[i]||0);
  });
  const labels = Object.keys(merged);
  const hujan  = labels.map(k=>merged[k].h);
  const tHujan = labels.map(k=>merged[k].t);

  const ctx = document.getElementById('rainChart')?.getContext('2d');
  let rainChart;
  if(ctx){
    rainChart = new Chart(ctx,{
      type:'bar',
      data:{ labels, datasets:[
        { label:'Hari Hujan', data:hujan, backgroundColor:'#859fe4' },
        { label:'Hari Tidak Hujan', data:tHujan, backgroundColor:'#4636a2' }
      ]},
      options:{
        responsive:true,
        plugins:{ legend:{ position:'top' } },
        scales:{
          x:{ grid:{ display:false }, ticks:{ color:'#122453' } },
          y:{ beginAtZero:true, grid:{ color:'#eee' }, ticks:{ color:'#122453' } }
        }
      }
    });
  }

  document.getElementById('dataType')?.addEventListener('change', e=>{
    const v = e.target.value, ttl = document.getElementById('chartTitle');
    if(!rainChart) return;
    if(v==='hujan'){
      rainChart.data.datasets = [{ label:'Hari Hujan', data:hujan, backgroundColor:'#859fe4' }];
      ttl.textContent = 'Hari Hujan Per Kecamatan';
    }else if(v==='tidak_hujan'){
      rainChart.data.datasets = [{ label:'Hari Tidak Hujan', data:tHujan, backgroundColor:'#4636a2' }];
      ttl.textContent = 'Hari Tidak Hujan Per Kecamatan';
    }else{
      rainChart.data.datasets = [
        { label:'Hari Hujan', data:hujan, backgroundColor:'#859fe4' },
        { label:'Hari Tidak Hujan', data:tHujan, backgroundColor:'#4636a2' }
      ];
      ttl.textContent = 'Hari Hujan & Tidak Hujan Per Kecamatan';
    }
    rainChart.update();
  });

  // Export grafik ke PDF (client-side)
  document.getElementById('btnChartPdf')?.addEventListener('click', async ()=>{
    const wrap = document.querySelector('.chart-card');
    if(!wrap) return;
    const canvas = await html2canvas(wrap, {scale:2});
    const img = canvas.toDataURL('image/png');
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('landscape','mm','a4');
    const w = pdf.internal.pageSize.getWidth(), h = pdf.internal.pageSize.getHeight();
    const imgW = w - 20, imgH = canvas.height * (imgW/canvas.width);
    pdf.addImage(img, 'PNG', 10, 10, imgW, Math.min(imgH, h-20));
    pdf.save('grafik_hujan.pdf');
  });

  // Modal Edit
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));
  window.openEditModal = function(id, kec, hh, hth, tgl){
    document.getElementById('editId').value = id;
    document.getElementById('editKecamatan').value = kec;
    document.getElementById('editHujan').value = hh;
    document.getElementById('editTidakHujan').value = hth;
    document.getElementById('editTanggal').value = tgl;
    document.getElementById('editForm').action = `{{ url('/rain') }}/${id}`;
    editModal.show();
  }
</script>
@endpush
