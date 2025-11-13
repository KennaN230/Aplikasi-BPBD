@extends('layouts.app')
@section('title', 'Cek Cuaca (Open-Meteo)')

@section('content')
<div class="container mt-4">
    <form method="GET" action="{{ route('cuaca') }}" class="mb-3">
        <div class="row">
            <div class="col">
                <input type="text" name="lat" class="form-control" placeholder="Latitude" value="{{ request('lat', '-7.9771') }}">
            </div>
            <div class="col">
                <input type="text" name="lon" class="form-control" placeholder="Longitude" value="{{ request('lon', '112.6341') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Cek Cuaca</button>
            </div>
        </div>
    </form>

    @if(isset($hasil))
    <div class="card p-3 shadow-sm">
        <h5>📍 Lokasi: {{ $hasil['lokasi'] }}</h5>
        <p>🌡️ Suhu: {{ $hasil['suhu'] }} °C</p>
        <p>🌧️ Curah Hujan: {{ $hasil['hujan'] }} mm</p>
        <p>💨 Kecepatan Angin: {{ $hasil['angin'] }} km/jam</p>
        <small>⏰ Terakhir diperbarui: {{ $hasil['waktu'] }}</small>
    </div>
    @endif
</div>
@endsection
