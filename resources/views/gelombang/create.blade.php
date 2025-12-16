@extends('layouts.app')

@section('title', 'Tambah Data Gelombang')

@section('content')
@php
    $user = Auth::user();
@endphp

<!-- === User Login Section (Kanan Atas) === -->
<div class="d-flex justify-content-end mb-3">
    <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <img src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('gambar/profile.png') }}"
                 class="rounded-circle me-2" width="40" height="40">
            <span class="fw-bold">{{ $user->nama ?? $user->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li class="px-3 py-2">
                <div class="fw-bold">{{ $user->nama ?? $user->name }}</div>
                <div class="text-muted small">{{ ucfirst(strtolower($user->role)) }}</div>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item text-danger">
                        <i class="fa fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

<!-- === Halaman Utama === -->
<div class="container-fluid px-4">

    <h1 class="mt-4 mb-4">Tambah Data Tinggi Gelombang</h1>

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white">
                    <strong>Form Input Tinggi Gelombang</strong>
                </div>

                <div class="card-body">

                    <form action="{{ route('gelombang.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control shadow-sm" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tinggi Gelombang Maksimum (m)</label>
                            <input type="number" step="0.01" name="tinggi_gelombang_max" class="form-control shadow-sm" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tinggi Gelombang Minimum (m)</label>
                            <input type="number" step="0.01" name="tinggi_gelombang_min" class="form-control shadow-sm" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('gelombang.index') }}" class="btn btn-secondary px-4">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                Simpan
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection
