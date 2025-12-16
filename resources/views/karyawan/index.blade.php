@extends('layouts.app')

@section('title', 'Data Petugas Piket')

@push('styles')
<style>
  main.app-main {
    background-color: #f8f5f0 !important;
  }

  .header-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(90deg, #fdf7ed, #f8f5f0);
    border-radius: 14px;
    padding: 15px 25px;
    margin: 15px 20px 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  }

  .judul {
    color:#122453;
    font-size:22px;
    font-weight:700;
    margin:0;
  }

  .dash-user {
    display:flex;
    align-items:center;
    gap:10px;
    background:#fff;
    border-radius:12px;
    padding:8px 14px;
    box-shadow:0 2px 6px rgba(0,0,0,0.05);
  }

  .dash-user .avatar {
    width:42px; height:42px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #f3e9d2;
  }

  .btn-cream {
    background-color:#fdf7ed;
    border:1px solid #f3e9d2;
    color:#444;
  }

  .btn-cream:hover {
    background-color:#f3e9d2;
  }

  .dropdown-cream .dropdown-item:hover {
    background-color:#f8f1e5;
  }

  .table-card {
    background:white;
    border-radius:10px;
    box-shadow:0 3px 8px rgba(0,0,0,0.05);
    padding:20px;
    margin:0 20px;
  }

  .btn-primary {
    background-color:#122453 !important;
    border:none;
  }

  .btn-primary:hover {
    background-color:#1a3470 !important;
  }

  .table th {
    background-color:#122453;
    color:white;
    vertical-align:middle;
  }

  .modal-header {
    background-color:#122453;
    color:white;
  }

  .alert-success {
    background:#e6ffed;
    border-left:5px solid #28a745;
    color:#155724;
  }
</style>
@endpush

@section('content')

{{-- ===== HEADER ===== --}}
<div class="header-bar">
  <h2 class="judul">📋 Data Petugas Piket</h2>

  <div class="dash-user">
    <div>
      <div class="fw-semibold">{{ Auth::user()->nama }}</div>
      <div class="small text-muted text-capitalize">{{ Auth::user()->role ?? 'User' }}</div>
    </div>
    <div class="dropdown ms-auto">
      <button type="button" class="btn btn-cream btn-pill-sm" data-bs-toggle="dropdown" aria-label="Menu profil">
        <i class="bi bi-three-dots-vertical"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end dropdown-cream">
        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Edit Profil</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form action="{{ route('logout') }}" method="POST">@csrf
            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</div>

{{-- ===== CONTENT ===== --}}
<div class="container mb-5">

  {{-- Notifikasi --}}
  @if(session('success'))
    <div class="alert alert-success mx-2">
      ✅ {{ session('success') }}
    </div>
  @endif

  {{-- Tombol Tambah --}}
  <div class="text-end mb-3 mx-2">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
      + Tambah Petugas Piket
    </button>
  </div>

  {{-- ===== CARD TABLE ===== --}}
  <div class="table-card">
    <div class="table-responsive">
      <table class="table table-hover table-bordered align-middle mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Tugas</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($karyawan as $index => $k)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $k->nip_pengawas }}</td>
            <td>{{ $k->nama_pengawas }}</td>
            <td>{{ $k->jabatan }}</td>
            <td>{{ $k->tugas }}</td>
            <td class="text-center">
              <form action="{{ route('karyawan.destroy', $k->nip_pengawas) }}" method="POST" onsubmit="return confirm('Yakin hapus karyawan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                  <i class="bi bi-trash"></i> Hapus
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-3">Belum ada data karyawan</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ===== MODAL TAMBAH ===== --}}
<div class="d-flex justify-content-between align-items-center my-3">
    <h5 class="mb-0">Status Darurat</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalStatus">
        <i class="bi bi-plus-circle"></i> Tambah Status
    </button>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Petugas Piket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form method="POST" action="{{ route('karyawan.store') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="nip_pengawas" class="form-label">NIP</label>
            <input type="text" name="nip_pengawas" id="nip_pengawas" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="nama_pengawas" class="form-label">Nama</label>
            <input type="text" name="nama_pengawas" id="nama_pengawas" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" name="jabatan" id="jabatan" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="tugas" class="form-label">Tugas</label>
            <input type="text" name="tugas" id="tugas" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- =================== TABEL STATUS DARURAT =================== --}}
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Status</th>
            <th width="80" class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($status as $i => $s)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $s->status }}</td>
            <td class="text-center">
                <form action="{{ route('status.destroy', ['id_status_darurat' => $s->id_status_darurat]) }}"
      method="POST"
      onsubmit="return confirm('Yakin hapus status ini?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm">
        <i class="bi bi-trash"></i>
    </button>
</form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center text-muted py-3">Belum ada data status</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- =================== TABEL JENIS BENCANA =================== --}}
<div class="d-flex justify-content-between align-items-center my-3">
    <h5 class="mb-0">Jenis Bencana</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalJenis">
        <i class="bi bi-plus-circle"></i> Tambah Jenis Bencana
    </button>
</div>

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th width="50">#</th>
            <th>Klasifikasi Bencana</th>
            <th>Jenis Bencana</th>
            <th width="80" class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jenis as $i => $j)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $j->klasifikasi->klasifikasi_bencana ?? '-' }}</td>
            <td>{{ $j->jenis_bencana }}</td>
            <td class="text-center">
                <form action="{{ route('jenis.destroy', ['id_jenis_bencana' => $j->id_jenis_bencana]) }}"
      method="POST"
      onsubmit="return confirm('Yakin hapus jenis bencana ini?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm">
        <i class="bi bi-trash"></i>
    </button>
</form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center text-muted py-3">
                Belum ada data jenis bencana
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- =================== TEMPLATE PENANDATANGAN LAPORAN =================== --}}
<div class="table-card mt-4">
    <h5 class="mb-3">Template Penandatangan Laporan</h5>

    <form action="{{ route('template.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Pejabat</label>
                <select name="nip_template" id="selectPejabat" class="form-control" required>
    <option value="">-- Pilih Pejabat --</option>

    @foreach($karyawan as $p)
        <option value="{{ $p->nip_pengawas }}"
                data-nama="{{ $p->nama_pengawas }}"
                data-jabatan="{{ $p->jabatan }}"
                {{ isset($template) && $template->nip_pengawas == $p->nip_pengawas ? 'selected' : '' }}>
            {{ $p->nama_pengawas }} ({{ $p->jabatan }})
        </option>
    @endforeach
</select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Jabatan</label>
                <input type="text" id="jabatanPejabat" class="form-control"
                       value="{{ $template->jabatan ?? '' }}" readonly>
            </div>
        </div>

        {{-- HIDDEN FIELD untuk disimpan ke database --}}
<input type="hidden" id="nipHidden" name="nip_pengawas">
<input type="hidden" id="namaHidden" name="nama_pengawas">
<input type="hidden" id="jabatanHidden" name="jabatan">
        <button class="btn btn-primary mt-2">Simpan Template</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('selectPejabat');
    const nipHidden = document.getElementById('nipHidden');
    const namaHidden = document.getElementById('namaHidden');
    const jabatanHidden = document.getElementById('jabatanHidden');
    const jabatanPejabat = document.getElementById('jabatanPejabat');

    if (sel && sel.value) {
        const opt = sel.options[sel.selectedIndex];
        nipHidden.value = opt.value;
        namaHidden.value = opt.getAttribute('data-nama') || '';
        jabatanHidden.value = opt.getAttribute('data-jabatan') || '';
        jabatanPejabat.value = opt.getAttribute('data-jabatan') || '';
    }

    sel.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];

        nipHidden.value = selected.value;
        namaHidden.value = selected.getAttribute('data-nama') || '';
        jabatanHidden.value = selected.getAttribute('data-jabatan') || '';
        jabatanPejabat.value = selected.getAttribute('data-jabatan') || '';
    });
});
</script>

{{-- ===== MODAL TAMBAH JENIS BENCANA ===== --}}
<div class="modal fade" id="modalJenis" tabindex="-1" aria-labelledby="modalJenisLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalJenisLabel">Tambah Jenis Bencana</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('jenis.store') }}" method="POST">
        @csrf

        <div class="modal-body">

          {{-- Dropdown klasifikasi --}}
          <div class="mb-3">
            <label class="form-label">Klasifikasi Bencana</label>
            <select name="id_klasifikasi_bencana" class="form-control" required>
              <option value="">-- Pilih Klasifikasi --</option>
              @foreach($klasifikasi as $k)
                <option value="{{ $k->id_klasifikasi_bencana }}">
                  {{ $k->klasifikasi_bencana }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Input nama jenis --}}
          <div class="mb-3">
            <label class="form-label">Jenis Bencana</label>
            <input 
              type="text" 
              class="form-control" 
              name="jenis_bencana" 
              required 
              placeholder="Contoh: Banjir, Gempa, Longsor">
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ===== MODAL TAMBAH STATUS DARURAT ===== --}}
<div class="modal fade" id="modalStatus" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalStatusLabel">Tambah Status Darurat</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('status.store') }}" method="POST">
        @csrf
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">Nama Status</label>
            <input type="text" class="form-control" name="status" required placeholder="Contoh: Siaga, Waspada, Darurat">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
