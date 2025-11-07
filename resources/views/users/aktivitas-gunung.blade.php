{{-- resources/views/aktivitas-gunung.blade.php --}}
@extends('layouts.app')
@section('title','Aktivitas Gunung Aktif')

@push('styles')
<style>
  /* ruang utama lebih rapat */
  main.app-main{ padding-top:6px !important; }

  /* util */
  .flex-1{flex:1 1 auto}
  .push-right{margin-left:auto}
  .nowrap{ white-space:nowrap; }
  .btn-pill-sm{border-radius:999px;padding:.35rem .9rem;font-weight:700}

  /* header */
  .dash-header{margin:4px 0 8px;display:flex;align-items:center;gap:16px}
  .dash-sub{color:#0B1C3F}
  .btn-cream{background:#efe8e0;border-color:#efe8e0;color:#0f2a4a}

  /* tombol utama */
  .btn-tambah{background:#EA620D;border-color:#EA620D;color:#fff}
  .btn-tambah:hover{background:#d45609;border-color:#d45609}
  .btn-hapus{background:#E30707;border-color:#E30707;color:#fff}
  .btn-hapus:hover{background:#C60A0A;border-color:#C60A0A}

  /* filter bar */
  .filter-bar .form-control,.filter-bar .form-select{border-radius:12px}
  .table-search{position:relative;max-width:250px}
  .table-search .bi-search{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#97a0b0}
  .table-search input{padding-left:40px}

  /* kartu & tabel */
  .soft-card{border:0;border-radius:16px;box-shadow:0 12px 28px rgba(0,0,0,.08);overflow:hidden}
  .table-wrap{max-height:460px;overflow:auto;border-radius:12px}
  .table-soft thead th{position:sticky;top:0;z-index:2;background:#0f2a4a;color:#fff;border:0!important}

  /* compact table */
  .table-soft.table-compact{table-layout:fixed;border-collapse:separate;border-spacing:0}
  .table-soft.table-compact th,
  .table-soft.table-compact td{padding:.45rem .60rem;vertical-align:middle}
  .table-soft.table-compact thead th{padding:.55rem .60rem;font-size:.95rem}
  .clip-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word}

  /* lebar kolom */
  .col-check{width:44px}
  .col-tgl{width:96px}
  .col-gng{width:160px}
  .col-doc{width:140px}
  .col-aksi{width:210px}

  /* judul halaman */
  .page-title-wrap{display:flex;align-items:center;justify-content:center;margin:2px 0 26px}
  .page-title{font-weight:800;color:#132d55;margin:0}

  /* dokumentasi pada tabel */
  .thumb-doc{height:44px;border-radius:6px;object-fit:cover;border:1px solid #e6e8ee}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Str;

  // ---- Null-safe user block ----
  $me          = auth()->user();
  $displayName = $me?->nama ?? $me?->name ?? 'Pengguna';
  $roleText    = $me?->role ? ucfirst(strtolower($me->role)) : 'User';

  $avatarUrl   = $me?->photo ? asset('storage/'.$me->photo) : asset('gambar/profile.png');
  $updatedTs   = optional($me?->updated_at)->timestamp;
  $avatarUrl  .= '?t=' . ($updatedTs ?: time());
  // --------------------------------

  $pendingUsers = $pendingUsers ?? collect();
  $n = (int)($pendingCount ?? $pendingUsers->count());

  $isPaginator  = is_object($items ?? null) && method_exists($items,'total');
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
                  <form method="POST" action="{{ route('users.approve',$u->id_user) }}">@csrf
                    <button class="btn btn-sm btn-success"><i class="bi bi-check2 me-1"></i>Setujui</button>
                  </form>
                  <form method="POST" action="{{ route('users.reject',$u->id_user) }}">@csrf
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- Profil (null-safe) --}}
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
  <h2 class="page-title">Aktivitas Gunung Aktif</h2>
</div>

{{-- FILTER BAR --}}
<form class="filter-bar mb-2" method="GET" action="{{ route('aktivitas-gunung.index') }}">
  <div class="d-flex flex-wrap align-items-center gap-2">
    <div class="table-search flex-1">
      <i class="bi bi-search"></i>
      <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari gunung / kata kunci…">
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

      {{-- CETAK EXCEL (ikut filter aktif) --}}
      <a class="btn btn-success btn-pill-sm"
         href="{{ route('aktivitas-gunung.export', request()->query()) }}">
        <i class="bi bi-file-earmark-excel me-1"></i> Excel
      </a>

      <button type="button" class="btn btn-tambah btn-pill-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="bi bi-plus-circle me-1"></i> Tambah
      </button>
      <button type="button" id="btnDeleteSelected" class="btn btn-hapus btn-pill-sm">
        <i class="bi bi-trash me-1"></i> Hapus
      </button>
    </div>
  </div>
</form>

{{-- FLASH (satu, auto-hide) --}}
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
        <col class="col-gng">
        <col><!-- Meteorologi -->
        <col><!-- Visual -->
        <col><!-- Aktivitas -->
        <col><!-- Rekomendasi -->
        <col class="col-doc">
        <col class="col-aksi">
      </colgroup>
      <thead>
      <tr>
        <th><input type="checkbox" id="checkAll"></th>
        <th class="nowrap">Tanggal</th>
        <th class="nowrap">Gunung</th>
        <th>Meteorologi</th>
        <th>Visual</th>
        <th>Aktivitas Vulkanik</th>
        <th>Rekomendasi</th>
        <th class="nowrap">Dokumentasi</th>
        <th class="nowrap">Aksi</th>
      </tr>
      </thead>
      <tbody>
      @forelse($items as $it)
        @php
          $mime   = $it->dokumentasi_mime ?? '';
          $isImg  = Str::startsWith($mime,'image/');
          $docUrl = !empty($it->dokumentasi_path) ? asset('storage/'.$it->dokumentasi_path) : null;
        @endphp
        <tr>
          <td><input type="checkbox" class="row-check" value="{{ $it->id }}"></td>
          <td class="nowrap">{{ $it->tanggal ? \Carbon\Carbon::parse($it->tanggal)->format('d/m/Y') : '' }}</td>
          <td class="nowrap">{{ $it->gunung ?: '-' }}</td>
          <td><div class="clip-2">{{ $it->meteorologi }}</div></td>
          <td><div class="clip-2">{{ $it->visual }}</div></td>
          <td><div class="clip-2">{{ $it->aktivitas_vulkanik }}</div></td>
          <td><div class="clip-2">{{ $it->rekomendasi }}</div></td>

          <td class="nowrap">
            @if($docUrl)
              @if($isImg)
                <a href="{{ $docUrl }}" target="_blank"><img class="thumb-doc" src="{{ $docUrl }}" alt="doc"></a>
              @else
                <a class="btn btn-outline-primary btn-sm" href="{{ $docUrl }}" target="_blank">
                  <i class="bi bi-file-earmark-text me-1"></i> Lihat
                </a>
              @endif
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td class="text-nowrap">
            {{-- DETAIL --}}
            <button class="btn btn-info btn-sm btn-pill-sm me-1"
              data-bs-toggle="modal" data-bs-target="#modalDetail"
              data-tanggal="{{ $it->tanggal ? \Carbon\Carbon::parse($it->tanggal)->format('d/m/Y') : '' }}"
              data-gunung="{{ e($it->gunung) }}"
              data-meteorologi="{{ e($it->meteorologi) }}"
              data-visual="{{ e($it->visual) }}"
              data-aktivitas="{{ e($it->aktivitas_vulkanik) }}"
              data-rekomendasi="{{ e($it->rekomendasi) }}"
              data-docurl="{{ $docUrl ?? '' }}"
              data-docisimg="{{ $isImg ? '1' : '0' }}"
            >
              <i class="bi bi-eye me-1"></i>
            </button>

            {{-- EDIT --}}
            <button class="btn btn-warning btn-sm btn-pill-sm"
              data-bs-toggle="modal" data-bs-target="#modalEdit"
              data-id="{{ $it->id }}"
              data-tanggal="{{ $it->tanggal ? \Carbon\Carbon::parse($it->tanggal)->toDateString() : '' }}"
              data-gunung="{{ e($it->gunung) }}"
              data-meteorologi="{{ e($it->meteorologi) }}"
              data-visual="{{ e($it->visual) }}"
              data-aktivitas_vulkanik="{{ e($it->aktivitas_vulkanik) }}"
              data-rekomendasi="{{ e($it->rekomendasi) }}"
              data-docurl="{{ $docUrl ?? '' }}"
              data-docisimg="{{ $isImg ? '1' : '0' }}"
            >
              <i class="bi bi-pencil-square me-1"></i>
            </button>

            {{-- HAPUS --}}
            <form action="{{ route('aktivitas-gunung.destroy',$it->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Hapus data ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-hapus btn-sm btn-pill-sm"><i class="bi bi-trash me-1"></i></button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted">Belum ada data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-body py-2">{{ $isPaginator ? $items->links() : '' }}</div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" method="POST" action="{{ route('aktivitas-gunung.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Gunung</label><input name="gunung" class="form-control"></div>
          <div class="col-12"><label class="form-label">Meteorologi</label><textarea name="meteorologi" rows="2" class="form-control"></textarea></div>
          <div class="col-12"><label class="form-label">Visual</label><textarea name="visual" rows="2" class="form-control"></textarea></div>
          <div class="col-12"><label class="form-label">Aktivitas Vulkanik</label><textarea name="aktivitas_vulkanik" rows="2" class="form-control"></textarea></div>
          <div class="col-12"><label class="form-label">Rekomendasi</label><textarea name="rekomendasi" rows="2" class="form-control"></textarea></div>

          {{-- Dokumentasi --}}
          <div class="col-12">
            <label class="form-label">Dokumentasi (gambar/PDF) <span class="text-muted">(opsional)</span></label>
            <input type="file" name="dokumentasi" class="form-control" accept="image/*,application/pdf">
            <div class="form-text">Boleh gambar (jpg/png/webp) atau PDF.</div>
          </div>
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
    <form id="formEdit" class="modal-content" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Gunung</label><input name="gunung" class="form-control"></div>
          <div class="col-12"><label class="form-label">Meteorologi</label><textarea name="meteorologi" rows="2" class="form-control"></textarea></div>
          <div class="col-12"><label class="form-label">Visual</label><textarea name="visual" rows="2" class="form-control"></textarea></div>
          <div class="col-12"><label class="form-label">Aktivitas Vulkanik</label><textarea name="aktivitas_vulkanik" rows="2" class="form-control"></textarea></div>
          <div class="col-12"><label class="form-label">Rekomendasi</label><textarea name="rekomendasi" rows="2" class="form-control"></textarea></div>

          {{-- Dokumentasi (ganti) --}}
          <div class="col-12">
            <label class="form-label">Dokumentasi (ganti – opsional)</label>
            <input type="file" name="dokumentasi" class="form-control" accept="image/*,application/pdf">
            <div id="currentDocWrap" class="mt-2" style="display:none">
              <span class="small text-muted me-2">Saat ini:</span>
              <a id="currentDocLink" href="#" target="_blank" class="align-middle"></a>
            </div>
          </div>
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
        <h5 class="modal-title">Detail Aktivitas Gunung</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="small text-muted">Tanggal</div>
            <div id="dTanggal" class="fw-semibold"></div>
          </div>
          <div class="col-md-6">
            <div class="small text-muted">Gunung</div>
            <div id="dGunung" class="fw-semibold"></div>
          </div>

          <div class="col-12"><hr class="my-2"></div>

          <div class="col-12">
            <div class="small text-muted">Meteorologi</div>
            <div id="dMeteorologi"></div>
          </div>
          <div class="col-12">
            <div class="small text-muted">Visual</div>
            <div id="dVisual"></div>
          </div>
          <div class="col-12">
            <div class="small text-muted">Aktivitas Vulkanik</div>
            <div id="dAktivitas"></div>
          </div>
          <div class="col-12">
            <div class="small text-muted">Rekomendasi</div>
            <div id="dRekomendasi"></div>
          </div>

          <div class="col-12">
            <div class="small text-muted mb-1">Dokumentasi</div>
            <div id="dDocWrap" style="display:none">
              {{-- anchor untuk gambar --}}
              <a id="dDocLink" href="#" target="_blank" class="d-inline-block">
                <img id="dDocImg" src="#" alt="dokumentasi"
                     style="max-width:100%;height:auto;max-height:320px;border-radius:8px;border:1px solid #e6e8ee;object-fit:cover">
              </a>
              {{-- tombol untuk file non-gambar --}}
              <a id="dDocFile" href="#" target="_blank" class="btn btn-outline-primary btn-sm mt-2" style="display:none">
                <i class="bi bi-file-earmark-text me-1"></i> Lihat dokumen
              </a>
            </div>
            <div id="dDocNone" class="text-muted">—</div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
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

  // Auto-hide flash success (satu notif)
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
      f.method='POST'; f.action=`{{ url('/aktivitas-gunung') }}/${id}`;
      f.innerHTML=`@csrf @method('DELETE')`;
      document.body.appendChild(f); f.submit();
    });
  });

  // Modal Edit
  document.getElementById('modalEdit')?.addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget, f = document.getElementById('formEdit'), id = b.getAttribute('data-id');
    f.action = `{{ url('/aktivitas-gunung') }}/${id}`;
    f.tanggal.value            = b.getAttribute('data-tanggal') || '';
    f.gunung.value             = b.getAttribute('data-gunung') || '';
    f.meteorologi.value        = b.getAttribute('data-meteorologi') || '';
    f.visual.value             = b.getAttribute('data-visual') || '';
    f.aktivitas_vulkanik.value = b.getAttribute('data-aktivitas_vulkanik') || '';
    f.rekomendasi.value        = b.getAttribute('data-rekomendasi') || '';

    const url = b.getAttribute('data-docurl') || '';
    const isImg = b.getAttribute('data-docisimg') === '1';
    const wrap = document.getElementById('currentDocWrap');
    const link = document.getElementById('currentDocLink');
    if (wrap && link){
      if (url){
        wrap.style.display = 'block';
        if (isImg){
          link.innerHTML = `<img class="thumb-doc" src="${url}" alt="dokumentasi">`;
          link.href = url;
        }else{
          link.textContent = 'Lihat dokumen';
          link.href = url;
        }
      }else{
        wrap.style.display = 'none';
        link.removeAttribute('href');
        link.textContent = '';
      }
    }
  });

  // ===== Modal DETAIL =====
  const nl2br = (s) => (s || '').replace(/\r?\n/g, '<br>');
  document.getElementById('modalDetail')?.addEventListener('show.bs.modal', (e) => {
    const b   = e.relatedTarget;
    const get = (name) => b.getAttribute(name) || '';

    document.getElementById('dTanggal').textContent   = get('data-tanggal');
    document.getElementById('dGunung').textContent    = get('data-gunung');
    document.getElementById('dMeteorologi').innerHTML = nl2br(get('data-meteorologi'));
    document.getElementById('dVisual').innerHTML      = nl2br(get('data-visual'));
    document.getElementById('dAktivitas').innerHTML   = nl2br(get('data-aktivitas'));
    document.getElementById('dRekomendasi').innerHTML = nl2br(get('data-rekomendasi'));

    const url  = get('data-docurl');
    const isImg = get('data-docisimg') === '1';
    const wrap = document.getElementById('dDocWrap');
    const none = document.getElementById('dDocNone');
    const link = document.getElementById('dDocLink');  // anchor berisi IMG
    const img  = document.getElementById('dDocImg');
    const file = document.getElementById('dDocFile');  // anchor tombol file

    if (url){
      none.style.display = 'none';
      wrap.style.display = 'block';
      link.href = url;
      file.href = url;

      if (isImg){
        img.src = url;
        img.style.display = '';
        link.style.display = 'inline-block';
        file.style.display = 'none';
      }else{
        img.removeAttribute('src');
        img.style.display = 'none';
        link.style.display = 'none';
        file.style.display = 'inline-block';
      }
    }else{
      wrap.style.display = 'none';
      none.style.display = 'block';
      img.removeAttribute('src');
      link.removeAttribute('href');
      file.removeAttribute('href');
      file.style.display = 'none';
    }
  });
</script>
@endpush