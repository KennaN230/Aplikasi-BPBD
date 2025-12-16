@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="bi bi-info-circle-fill text-primary fs-2"></i>
                                </div>
                                <div>
                                    <h1 class="h2 mb-1 text-dark">📋 Detail Kejadian Bencana</h1>
                                    <p class="text-muted mb-0">Informasi lengkap mengenai kejadian bencana</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="bg-light px-3 py-2 rounded d-inline-block">
                                <small class="text-primary fw-bold">ID: {{ $kejadian->id_kejadian ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Utama Kejadian --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary bg-opacity-10 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        <h5 class="card-title mb-0 text-dark">Informasi Utama</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Nama Kejadian</span>
                            <span class="fw-semibold text-dark">{{ $kejadian->nama_kejadian ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Jenis Bencana</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary fs-6">
                                {{ $kejadian->jenisBencana->jenis_bencana ?? 'Tidak tersedia' }}
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Tanggal Kejadian</span>
                            <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($kejadian->tanggal)->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Waktu</span>
                            <span class="fw-semibold text-dark">{{ $kejadian->waktu ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Status Darurat</span>
                            <span class="badge bg-{{ $kejadian->statusDarurat->id_status_darurat == 1 ? 'success' : ($kejadian->statusDarurat->id_status_darurat == 2 ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $kejadian->statusDarurat->id_status_darurat == 1 ? 'success' : ($kejadian->statusDarurat->id_status_darurat == 2 ? 'warning' : 'danger') }} fs-6">
                                {{ $kejadian->statusDarurat->status ?? 'Normal' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-success bg-opacity-10 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt text-success me-2"></i>
                        <h5 class="card-title mb-0 text-dark">Lokasi Kejadian</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Alamat</span>
                            <span class="fw-semibold text-dark text-end">{{ $kejadian->alamat ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Kecamatan</span>
                            <span class="fw-semibold text-dark">{{ $kejadian->kecamatan->kecamatan ?? '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Desa</span>
                            <span class="fw-semibold text-dark">{{ $kejadian->desa->desa ?? '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Koordinat</span>
                            <span class="fw-semibold text-dark">{{ $kejadian->latitude ?? '-' }}, {{ $kejadian->longitude ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Kerusakan --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger bg-opacity-10 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-bar-chart text-danger me-2"></i>
                        <h4 class="card-title mb-0 text-dark">📊 Data Kerusakan</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Data Rumah --}}
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-warning h-100">
                                <div class="card-header bg-warning bg-opacity-10 border-warning">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0 text-dark">🏠 Rumah</h6>
                                        <span class="badge bg-warning text-dark">
                                            {{ ($kejadian->rumah->rmh_rr ?? 0) + ($kejadian->rumah->rmh_rs ?? 0) + ($kejadian->rumah->rmh_rb ?? 0) + ($kejadian->rumah->terendam ?? 0) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Ringan</small>
                                        <span class="fw-bold text-warning">{{ $kejadian->rumah->rmh_rr ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Sedang</small>
                                        <span class="fw-bold text-warning">{{ $kejadian->rumah->rmh_rs ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Berat</small>
                                        <span class="fw-bold text-warning">{{ $kejadian->rumah->rmh_rb ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Terendam</small>
                                        <span class="fw-bold text-warning">{{ $kejadian->rumah->terendam ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sosial Ekonomi --}}
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info bg-opacity-10 border-info">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0 text-dark">💰 Sosial Ekonomi</h6>
                                        @if($kejadian->sosek)
                                        <span class="badge bg-info">
                                            {{ ($kejadian->sosek->sosek_rr ?? 0) + ($kejadian->sosek->sosek_rs ?? 0) + ($kejadian->sosek->sosek_rb ?? 0) + ($kejadian->sosek->sosek_terendam ?? 0) }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Tidak ada</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($kejadian->sosek)
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Luas (Ha)</small>
                                        <span class="fw-bold text-info">{{ $kejadian->sosek->luas ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Ringan</small>
                                        <span class="fw-bold text-info">{{ $kejadian->sosek->sosek_rr ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Sedang</small>
                                        <span class="fw-bold text-info">{{ $kejadian->sosek->sosek_rs ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Berat</small>
                                        <span class="fw-bold text-info">{{ $kejadian->sosek->sosek_rb ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Terendam</small>
                                        <span class="fw-bold text-info">{{ $kejadian->sosek->sosek_terendam ?? 0 }}</span>
                                    </div>
                                    @else
                                    <div class="text-center py-3">
                                        <i class="bi bi-dash-circle text-muted fs-1"></i>
                                        <p class="text-muted mt-2 mb-0">Tidak ada data</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Sarana Prasarana --}}
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary bg-opacity-10 border-primary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0 text-dark">🏢 Sarana Prasarana</h6>
                                        @if($kejadian->sarpras)
                                        <span class="badge bg-primary">
                                            {{ ($kejadian->sarpras->sarpras_rr ?? 0) + ($kejadian->sarpras->sarpras_rs ?? 0) + ($kejadian->sarpras->sarpras_rb ?? 0) + ($kejadian->sarpras->sarpras_terendam ?? 0) }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Tidak ada</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($kejadian->sarpras)
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Ringan</small>
                                        <span class="fw-bold text-primary">{{ $kejadian->sarpras->sarpras_rr ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Sedang</small>
                                        <span class="fw-bold text-primary">{{ $kejadian->sarpras->sarpras_rs ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Berat</small>
                                        <span class="fw-bold text-primary">{{ $kejadian->sarpras->sarpras_rb ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Terendam</small>
                                        <span class="fw-bold text-primary">{{ $kejadian->sarpras->sarpras_terendam ?? 0 }}</span>
                                    </div>
                                    @else
                                    <div class="text-center py-3">
                                        <i class="bi bi-dash-circle text-muted fs-1"></i>
                                        <p class="text-muted mt-2 mb-0">Tidak ada data</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Pelayanan Dasar --}}
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success bg-opacity-10 border-success">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0 text-dark">⚕️ Pelayanan Dasar</h6>
                                        @if($kejadian->pelayanan)
                                        <span class="badge bg-success">
                                            {{ ($kejadian->pelayanan->pelayanan_rr ?? 0) + ($kejadian->pelayanan->pelayanan_rs ?? 0) + ($kejadian->pelayanan->pelayanan_rb ?? 0) + ($kejadian->pelayanan->pelayanan_terendam ?? 0) }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Tidak ada</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($kejadian->pelayanan)
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Ringan</small>
                                        <span class="fw-bold text-success">{{ $kejadian->pelayanan->pelayanan_rr ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Sedang</small>
                                        <span class="fw-bold text-success">{{ $kejadian->pelayanan->pelayanan_rs ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Rusak Berat</small>
                                        <span class="fw-bold text-success">{{ $kejadian->pelayanan->pelayanan_rb ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Terendam</small>
                                        <span class="fw-bold text-success">{{ $kejadian->pelayanan->pelayanan_terendam ?? 0 }}</span>
                                    </div>
                                    @if($kejadian->pelayanan->taksiran > 0)
                                    <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                        <small class="text-muted">Taksiran Kerugian</small>
                                        <span class="fw-bold text-danger">Rp {{ number_format($kejadian->pelayanan->taksiran, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    @else
                                    <div class="text-center py-3">
                                        <i class="bi bi-dash-circle text-muted fs-1"></i>
                                        <p class="text-muted mt-2 mb-0">Tidak ada data</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Korban --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger bg-opacity-10 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people text-danger me-2"></i>
                        <h4 class="card-title mb-0 text-dark">👥 Data Korban</h4>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($kejadian->korban && $kejadian->korban->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4">Kategori Korban</th>
                                    <th class="text-center">Kategori Umur</th>
                                    <th class="text-center">👨 Laki-laki</th>
                                    <th class="text-center">👩 Perempuan</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalL = 0;
                                    $totalP = 0;
                                    $grandTotal = 0;
                                @endphp
                                @foreach($kejadian->korban as $korban)
                                    @php
                                        $totalL += $korban->L ?? 0;
                                        $totalP += $korban->P ?? 0;
                                        $rowTotal = ($korban->L ?? 0) + ($korban->P ?? 0);
                                        $grandTotal += $rowTotal;
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-semibold text-dark">{{ $korban->kategoriKorban->nama_kategori ?? 'Tanpa Kategori' }}</span>
                                        </td>
                                        <td class="text-center text-muted">
                                            {{ $korban->kategoriUmur->nama_kategori_umur ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary fs-6">
                                                {{ $korban->L ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger bg-opacity-10 text-danger fs-6">
                                                {{ $korban->P ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-dark bg-opacity-10 text-dark fw-bold fs-6">
                                                {{ $rowTotal }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="ps-4 fw-bold text-end">TOTAL:</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6">{{ $totalL }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger fs-6">{{ $totalP }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-dark fs-6">{{ $grandTotal }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-exclamation-triangle text-warning fs-1"></i>
                        </div>
                        <h5 class="text-warning mb-2">Tidak Ada Data Korban</h5>
                        <p class="text-muted">Belum terdapat data korban yang tercatat untuk kejadian ini.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Tambahan --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info bg-opacity-10 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-badge text-info me-2"></i>
                        <h5 class="card-title mb-0 text-dark">Petugas Penanggung Jawab</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($semuaPengawas && $semuaPengawas->count() > 0)
                        @foreach($semuaPengawas as $pengawas)
                        <div class="d-flex align-items-center p-3 bg-info bg-opacity-5 rounded {{ !$loop->last ? 'mb-3' : '' }}">
                            <div class="bg-info text-white rounded-circle p-3 me-3">
                                <i class="bi bi-person-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-dark">{{ $pengawas->nama_pengawas ?? 'Tidak tersedia' }}</h6>
                                <p class="text-muted mb-1 small">{{ $pengawas->jabatan ?? '-' }}</p>
                                <p class="text-muted mb-0 small">NIP: {{ $pengawas->nip_pengawas ?? '-' }}</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada data petugas</p>
                            @if($kejadian->nip_pengawas)
                            <p class="small text-muted mb-0">NIP dari database: {{ $kejadian->nip_pengawas }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-secondary bg-opacity-10 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-text text-secondary me-2"></i>
                        <h5 class="card-title mb-0 text-dark">Informasi Lainnya</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Penyebab</span>
                            <span class="fw-semibold text-dark text-end">{{ $kejadian->penyebab ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Kronologi</span>
                            <span class="fw-semibold text-dark text-end">{{ Str::limit($kejadian->kronologi, 50) ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Upaya Penanganan</span>
                            <span class="fw-semibold text-dark text-end">{{ Str::limit($kejadian->upaya, 50) ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Kode Indeks Bencana (KIB)</span>
                            <code class="bg-light px-2 py-1 rounded">{{ $kejadian->kib ?? 'N/A' }}</code>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Sumber Informasi</span>
                            <span class="fw-semibold text-dark">{{ $kejadian->sumber ?? 'Tidak tersedia' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Deskripsi dan Kronologi Lengkap --}}
    @if($kejadian->deskripsi || $kejadian->kronologi)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark bg-opacity-10 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-journal-text text-dark me-2"></i>
                        <h5 class="card-title mb-0 text-dark">Deskripsi & Kronologi Lengkap</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($kejadian->deskripsi)
                    <div class="mb-4">
                        <h6 class="text-dark mb-2">📝 Deskripsi Kejadian:</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $kejadian->deskripsi }}
                        </div>
                    </div>
                    @endif
                    
                    @if($kejadian->kronologi)
                    <div>
                        <h6 class="text-dark mb-2">🕰️ Kronologi Kejadian:</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $kejadian->kronologi }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Tombol Aksi --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Data dibuat: {{ \Carbon\Carbon::parse($kejadian->created_at ?? now())->translatedFormat('d F Y H:i') }}
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('kejadian') }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Kembali ke Daftar
                                </a>
                                <a href="{{ route('kejadian.edit', $kejadian->id_kejadian) }}" 
                                   class="btn btn-primary">
                                    <i class="bi bi-pencil me-1"></i>
                                    Edit Data
                                </a>
                                <button type="button" class="btn btn-success" onclick="window.print()">
                                    <i class="bi bi-printer me-1"></i>
                                    Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}

.card-header {
    padding: 1rem 1.25rem;
}

/* Print Styles */
@media print {
    .btn, .bi-arrow-left, .bi-pencil, .bi-printer {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
    
    .container-fluid {
        max-width: 100% !important;
        padding: 0 !important;
    }
}
</style>

<script>
// Menambahkan efek hover pada kartu
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.style.cursor = 'pointer';
    });
    
    // Auto-expand jika hanya sedikit data
    if (document.querySelectorAll('.table tbody tr').length <= 3) {
        document.querySelectorAll('.card').forEach(card => {
            card.classList.remove('collapsed');
        });
    }
});
</script>
@endsection