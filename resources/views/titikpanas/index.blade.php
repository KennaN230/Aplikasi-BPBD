{{-- resources/views/titikpanas/index.blade.php --}}
@extends('layouts.app')
@section('title','Titik Panas')

@push('styles')
<style>
  main.app-main{ padding-top:6px !important; }

  .flex-1{flex:1 1 auto}
  .push-right{margin-left:auto}
  .nowrap{ white-space:nowrap; }
  .btn-pill-sm{border-radius:999px;padding:.35rem .9rem;font-weight:700}

  .dash-header{margin:4px 0 8px;display:flex;align-items:center;gap:16px}
  .dash-sub{color:#0B1C3F}
  .btn-cream{background:#efe8e0;border-color:#efe8e0;color:#0f2a4a}

  .btn-tambah{background:#EA620D;border-color:#EA620D;color:#fff}
  .btn-tambah:hover{background:#d45609;border-color:#d45609}
  .btn-hapus{background:#E30707;border-color:#E30707;color:#fff}
  .btn-hapus:hover{background:#C60A0A;border-color:#C60A0A}

  .filter-bar .form-control,.filter-bar .form-select{border-radius:12px}
  .table-search{position:relative;max-width:250px}
  .table-search .bi-search{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#97a0b0}
  .table-search input{padding-left:40px}

  .soft-card{border:0;border-radius:16px;box-shadow:0 12px 28px rgba(0,0,0,.08);overflow:hidden}
  /* judul halaman */
  .page-title-wrap{display:flex;align-items:center;justify-content:center;margin:2px 0 26px}
  .page-title{font-weight:800;color:#132d55;margin:0}
  .table-soft thead th{position:sticky;top:0;z-index:2;background:#0f2a4a;color:#fff;border:0!important}

  .table-soft.table-compact{table-layout:fixed;border-collapse:separate;border-spacing:0}
  .table-soft.table-compact th,
  .table-soft.table-compact td{padding:.45rem .60rem;vertical-align:middle}
  .table-soft.table-compact thead th{padding:.55rem .60rem;font-size:.95rem}
  .clip-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word}

  .col-check{width:44px}
  .col-tgl{width:110px}
  .col-aksi{width:210px}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Str;

  $me          = auth()->user();
  $displayName = $me?->nama ?? $me?->name ?? 'Pengguna';
  $roleText    = $me?->role ? ucfirst(strtolower($me->role)) : 'User';
  $avatarUrl   = $me?->photo ? asset('storage/'.$me->photo) : asset('gambar/profile.png');
  $updatedTs   = optional($me?->updated_at)->timestamp;
  $avatarUrl  .= '?t=' . ($updatedTs ?: time());

  $pendingUsers = $pendingUsers ?? collect();
  $n = (int)($pendingCount ?? $pendingUsers->count());
@endphp

{{-- HEADER --}}
<div class="dash-header">
  <div>
    <h2 class="mb-1" style="font-weight:800;color:#132d55">Selamat Datang!</h2>
    <div class="dash-sub">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
    <div class="dash-sub" id="clock">--:--:--</div>
  </div>

  <div class="ms-auto d-flex align-items-center gap-2">
    {{-- Notifikasi --}}
    <div class="dropdown">
      <button class="btn btn-cream btn-pill-sm position-relative" data-bs-toggle="dropdown" aria-label="Notifikasi">
        <i class="bi bi-bell"></i>
        @if($n>0)
          <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">{{ $n>99?'99+':$n }}</span>
        @endif
      </button>
      <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:320px;border-radius:14px;overflow:hidden;border:0;box-shadow:0 12px 28px rgba(0,0,0,.12)">
        <div class="px-3 py-2 fw-bold" style="background:#efe8e0;color:#0f2a4a;border-bottom:1px solid #e6ddd3">Notifikasi</div>
        @if($pendingUsers->isEmpty())
          <div class="px-3 py-3 text-muted">Tidak ada akun pending.</div>
        @else
          <div class="list-group list-group-flush" style="max-height:320px;overflow:auto">
            @foreach($pendingUsers as $u)
              <div class="list-group-item">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="me-3">
                    <div class="fw-semibold">{{ $u->nama }}</div>
                    <div class="small text-muted">{{ $u->email }}</div>
                  </div>
                  <span class="badge text-bg-warning">Pending</span>
                </div>
                <div class="mt-2 d-flex gap-2">
                  @if (Route::has('users.approve'))
                  <form method="POST" action="{{ route('users.approve',$u->id_user) }}">@csrf
                    <button class="btn btn-sm btn-success"><i class="bi bi-check2 me-1"></i>Setujui</button>
                  </form>
                  @endif
                  @if (Route::has('users.reject'))
                  <form method="POST" action="{{ route('users.reject',$u->id_user) }}">@csrf
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                  </form>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- Profil --}}
    <div class="d-flex align-items-center">
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
</div>

{{-- JUDUL --}}
<div class="page-title-wrap">
  <h2 class="page-title">Informasi Titik Panas</h2>
</div>

{{-- FILTER BAR --}}
<form class="filter-bar mb-2" method="GET" action="{{ route('titikpanas.index') }}">
  <div class="d-flex flex-wrap align-items-center gap-2">
    <div class="table-search flex-1">
      <i class="bi bi-search"></i>
      <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari kecamatan / keterangan…">
    </div>

    <select name="bulan" class="form-select" style="width:150px">
      <option value="">Semua Bulan</option>
      @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $v=>$t)
        <option value="{{ $v }}" @selected((int)request('bulan')===$v)>{{ $t }}</option>
      @endforeach
    </select>

    <select name="tahun" class="form-select" style="width:160px">
      <option value="">Semua Tahun</option>
      @for($y=now()->year;$y>=now()->year-10;$y--)
        <option value="{{ $y }}" @selected((int)request('tahun')===$y)>{{ $y }}</option>
      @endfor
    </select>

    <input type="date" name="from" class="form-control" style="width:170px" value="{{ request('from') }}">
    <input type="date" name="to"   class="form-control" style="width:170px" value="{{ request('to') }}">

    <div class="push-right d-flex align-items-center gap-2">
      <button class="btn btn-outline-primary btn-pill-sm">Terapkan</button>

      {{-- CETAK PDF (ikut filter aktif) --}}
      @if (Route::has('titikpanas.cetak.pdf'))
      <a class="btn btn-success btn-pill-sm"
         href="{{ route('titikpanas.cetak.pdf', request()->query()) }}"
         target="_blank">
        <i class="bi bi-printer me-1"></i> PDF
      </a>
      @endif

      <button type="button" class="btn btn-tambah btn-pill-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="bi bi-plus-circle me-1"></i> Tambah
      </button>
      <button type="button" id="btnDeleteSelected" class="btn btn-hapus btn-pill-sm">
        <i class="bi bi-trash me-1"></i> Hapus
      </button>
    </div>
  </div>
</form>

{{-- FLASH --}}
@if(session('ok'))
  <div class="alert alert-success">{{ session('ok') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

{{-- TABEL --}}
<div class="card soft-card">
  <div class="table-wrap">
    <table class="table table-hover align-middle mb-0 table-soft table-compact">
      <colgroup>
        <col class="col-check">
        <col class="col-tgl">
        <col><!-- Titik Panas -->
        <col><!-- Latitude -->
        <col><!-- Longitude -->
        <col><!-- Kecamatan -->
        <col><!-- Satelit -->
        <col><!-- Waktu -->
        <col><!-- Keterangan -->
        <col class="col-aksi">
      </colgroup>
      <thead>
      <tr>
        <th><input type="checkbox" id="checkAll"></th>
        <th class="nowrap">Tanggal</th>
        <th class="nowrap">Titik Panas</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Kecamatan</th>
        <th>Satelit</th>
        <th>Waktu</th>
        <th>Keterangan</th>
        <th class="nowrap">Aksi</th>
      </tr>
      </thead>
      <tbody>
      @forelse($items as $it)
        <tr>
          <td><input type="checkbox" class="row-check" value="{{ $it->id }}"></td>
          <td class="nowrap">{{ $it->tanggal ? \Carbon\Carbon::parse($it->tanggal)->format('d/m/Y') : '' }}</td>
          <td class="nowrap">{{ $it->titik_panas ?? '-' }}</td>
          <td>{{ $it->latitude ?? '-' }}</td>
          <td>{{ $it->longitude ?? '-' }}</td>
          <td class="nowrap">{{ $it->kecamatan ?? '-' }}</td>
          <td class="nowrap">{{ $it->satelit ?? '-' }}</td>
          <td class="nowrap">{{ $it->waktu ?? '-' }}</td>
          <td><div class="clip-2">{{ $it->keterangan ?? '-' }}</div></td>

          <td class="text-nowrap">
            {{-- DETAIL --}}
            <button class="btn btn-info btn-sm btn-pill-sm me-1"
              data-bs-toggle="modal" data-bs-target="#modalDetail"
              data-tanggal="{{ $it->tanggal ? \Carbon\Carbon::parse($it->tanggal)->format('d/m/Y') : '' }}"
              data-titik="{{ $it->titik_panas }}"
              data-lat="{{ $it->latitude }}"
              data-lon="{{ $it->longitude }}"
              data-kecamatan="{{ e($it->kecamatan) }}"
              data-satelit="{{ e($it->satelit) }}"
              data-waktu="{{ $it->waktu }}"
              data-keterangan="{{ e($it->keterangan) }}"
            >
              <i class="bi bi-eye me-1"></i>
            </button>

            {{-- EDIT --}}
            <button class="btn btn-warning btn-sm btn-pill-sm"
              data-bs-toggle="modal" data-bs-target="#modalEdit"
              data-id="{{ $it->id }}"
              data-tanggal="{{ $it->tanggal ? \Carbon\Carbon::parse($it->tanggal)->toDateString() : '' }}"
              data-titik="{{ $it->titik_panas }}"
              data-lat="{{ $it->latitude }}"
              data-lon="{{ $it->longitude }}"
              data-kecamatan="{{ e($it->kecamatan) }}"
              data-satelit="{{ e($it->satelit) }}"
              data-waktu="{{ $it->waktu }}"
              data-keterangan="{{ e($it->keterangan) }}"
            >
              <i class="bi bi-pencil-square me-1"></i>
            </button>

            

            {{-- HAPUS --}}
            @if (Route::has('titikpanas.destroy'))
            <form action="{{ route('titikpanas.destroy',$it->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Hapus data ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-hapus btn-sm btn-pill-sm"><i class="bi bi-trash me-1"></i></button>
            </form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="10" class="text-center text-muted">Belum ada data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-body py-2">{{ method_exists($items,'links') ? $items->links() : '' }}</div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" method="POST" action="{{ route('titikpanas.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Titik Panas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Titik Panas</label><input type="number" name="titik_panas" class="form-control" min="0" required></div>
          <div class="col-md-4"><label class="form-label">Waktu</label><input type="time" name="waktu" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Latitude</label><input name="latitude" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Longitude</label><input name="longitude" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Kecamatan</label><input name="kecamatan" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Satelit</label><input name="satelit" class="form-control" value="Snpp/VIIRS"></div>
          <div class="col-12"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success"><i class="bi bi-check2 me-1"></i> Simpan</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Batal</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form id="formEdit" class="modal-content" method="POST">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Titik Panas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Titik Panas</label><input type="number" name="titik_panas" class="form-control" min="0" required></div>
          <div class="col-md-4"><label class="form-label">Waktu</label><input type="time" name="waktu" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Latitude</label><input name="latitude" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Longitude</label><input name="longitude" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Kecamatan</label><input name="kecamatan" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Satelit</label><input name="satelit" class="form-control"></div>
          <div class="col-12"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success"><i class="bi bi-save me-1"></i> Update</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Batal</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Titik Panas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-4"><div class="small text-muted">Tanggal</div><div id="dTanggal" class="fw-semibold"></div></div>
          <div class="col-md-4"><div class="small text-muted">Waktu</div><div id="dWaktu" class="fw-semibold"></div></div>
          <div class="col-md-4"><div class="small text-muted">Titik Panas</div><div id="dTitik" class="fw-semibold"></div></div>

          <div class="col-md-4"><div class="small text-muted">Latitude</div><div id="dLat"></div></div>
          <div class="col-md-4"><div class="small text-muted">Longitude</div><div id="dLon"></div></div>
          <div class="col-md-4"><div class="small text-muted">Kecamatan</div><div id="dKecamatan"></div></div>

          <div class="col-md-6"><div class="small text-muted">Satelit</div><div id="dSatelit"></div></div>
          <div class="col-12"><div class="small text-muted">Keterangan</div><div id="dKeterangan"></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Jam hidup
  const clockEl = document.getElementById('clock');
  if (clockEl){
    const pad = n => String(n).padStart(2,'0');
    const tick = () => { const d = new Date(); clockEl.textContent = `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`; };
    tick(); setInterval(tick,1000);
  }

  // Auto-hide flash success
  document.querySelectorAll('.alert-success').forEach(el=>{
    setTimeout(()=>{ el.style.transition='opacity .4s'; el.style.opacity='0'; setTimeout(()=>el.remove(),400); }, 2500);
  });

  // Pilih semua
  const checkAll = document.getElementById('checkAll');
  const rowChecks = () => Array.from(document.querySelectorAll('.row-check'));
  checkAll?.addEventListener('change', e => rowChecks().forEach(cb => cb.checked = e.target.checked));

  // Hapus banyak
  document.getElementById('btnDeleteSelected')?.addEventListener('click', () => {
    const ids = rowChecks().filter(cb => cb.checked).map(cb => cb.value);
    if (!ids.length) return alert('Pilih minimal satu baris.');
    if (!confirm(`Hapus ${ids.length} data terpilih?`)) return;
    ids.forEach(id => {
      const f = document.createElement('form');
      f.method='POST'; f.action=`{{ url('/titikpanas') }}/${id}`;
      f.innerHTML=`@csrf @method('DELETE')`;
      document.body.appendChild(f); f.submit();
    });
  });

  // Modal Edit
  document.getElementById('modalEdit')?.addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget, f = document.getElementById('formEdit'), id = b.getAttribute('data-id');
    f.action = `{{ url('/titikpanas') }}/${id}`;
    f.tanggal.value      = b.getAttribute('data-tanggal') || '';
    f.titik_panas.value  = b.getAttribute('data-titik') || '';
    f.latitude.value     = b.getAttribute('data-lat') || '';
    f.longitude.value    = b.getAttribute('data-lon') || '';
    f.kecamatan.value    = b.getAttribute('data-kecamatan') || '';
    f.satelit.value      = b.getAttribute('data-satelit') || '';
    f.waktu.value        = b.getAttribute('data-waktu') || '';
    f.keterangan.value   = b.getAttribute('data-keterangan') || '';
  });

  // Modal Detail
  const nl2br = (s) => (s || '').replace(/\r?\n/g, '<br>');
  document.getElementById('modalDetail')?.addEventListener('show.bs.modal', (e) => {
    const b = e.relatedTarget, get = (n)=> b.getAttribute(n) || '';
    document.getElementById('dTanggal').textContent   = get('data-tanggal');
    document.getElementById('dWaktu').textContent     = get('data-waktu');
    document.getElementById('dTitik').textContent     = get('data-titik');
    document.getElementById('dLat').textContent       = get('data-lat');
    document.getElementById('dLon').textContent       = get('data-lon');
    document.getElementById('dKecamatan').textContent = get('data-kecamatan');
    document.getElementById('dSatelit').textContent   = get('data-satelit');
    document.getElementById('dKeterangan').innerHTML  = nl2br(get('data-keterangan'));
  });
</script>
@endpush
