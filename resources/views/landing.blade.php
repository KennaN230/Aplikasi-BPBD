@extends('layouts.public')
@section('title','Infografis Sebaran Kejadian')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  :root{
    --blue:#EA620D; --blue-dark:#A54204; --ink:#0b1c3f;
    --tile:#eaf2ff; --paper:#ffffff; --muted:#6b7280;
    --ok:#1aa65d; --warn:#f2b705; --danger:#e03030;
    --card-shadow:0 12px 30px rgba(0,0,0,.08); --radius:16px;
  }
  .hero{background:linear-gradient(90deg,var(--blue),var(--blue-dark));color:#fff;
        border-radius:16px;box-shadow:var(--card-shadow);padding:14px 16px;
        display:flex;align-items:center;gap:14px;margin-bottom:14px;}
  .hero .title{font-size:22px;font-weight:800}
  .hero .legend{display:flex;gap:10px;align-items:center;margin-left:auto}
  .dot{width:14px;height:14px;border-radius:50%}
  .g{background:var(--ok)} .y{background:var(--warn)} .r{background:var(--danger)}
  .period-form{background:rgba(255,255,255,.18);border-radius:12px;padding:6px 8px;display:flex;gap:6px;align-items:center}
  .period-form input{border:0;border-radius:8px;padding:6px 8px}
  .period-form button{border:0;border-radius:10px;padding:6px 12px;background:#fff;color:#A54204;font-weight:800}
  .stat-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
  .stat{background:#fff;border-radius:16px;box-shadow:var(--card-shadow);padding:14px}
  .stat .h{color:var(--ink);font-weight:700}
  .stat .num{font-size:32px;font-weight:800;color:#0b1c3f;line-height:1}
  .grid{display:grid;grid-template-columns:2fr 1fr;gap:14px}
  .panel{background:#fff;border-radius:16px;box-shadow:var(--card-shadow)}
  .panel .ph{padding:12px 14px;border-bottom:1px solid #edf0f5;font-weight:800;color:var(--ink)}
  .panel .pb{padding:12px 14px}
  #map{height:520px;border-radius:0 0 16px 16px}
  .impact{display:grid;grid-template-columns:1fr;gap:10px}
  .impact .box{border:1px solid #e7ecf4;border-radius:12px;padding:10px;background:#fafcff}
  .box .bh{font-weight:800;color:var(--ink);margin-bottom:6px}
  .mini-table{width:100%;border-collapse:collapse}
  .mini-table th,.mini-table td{padding:6px 8px;border-bottom:1px solid #eef1f6;font-size:13px}
  .mini-table th{color:var(--ink);font-weight:800}
  .chart-wrap{padding:8px 14px 18px}
  @media (max-width:992px){.stat-cards{grid-template-columns:1fr 1fr}.grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@php
  $points = ($kecamatanPoints ?? collect())->map(function($it){
      $c = (int)($it['count'] ?? 0);
      $color = $c >= 10 ? 'red' : ($c >= 5 ? 'yellow' : 'green');
      return ['name'=>(string)($it['nama'] ?? '-'),'count'=>$c,'lat'=>$it['lat'],'lng'=>$it['lng'],'color'=>$color];
  })->values();
@endphp

<div class="hero">
  <div><div class="title">Infografis Sebaran Kejadian</div></div>

  <form class="period-form" method="get" action="{{ route('landing') }}">
    <i class="bi bi-calendar2-week me-1"></i>
    <input type="date" name="from" value="{{ $from->toDateString() }}">
    <span class="px-1">s.d.</span>
    <input type="date" name="to" value="{{ $to->toDateString() }}">
    <button type="submit"><i class="bi bi-arrow-repeat me-1"></i> Terapkan</button>
  </form>

  <div class="legend ms-2">
    <div class="dot g"></div><span>1–4</span>
    <div class="dot y"></div><span>5–9</span>
    <div class="dot r"></div><span>10+</span>
  </div>
</div>

<div class="stat-cards">
  <div class="stat">
    <div class="h">Jumlah Kejadian</div>
    <div class="num">{{ number_format($kejadianTotal) }}</div>
    <div class="small text-muted">{{ number_format($kecDisplayCount ?? 0) }} kecamatan terlibat</div>
  </div>
  <div class="stat">
    <div class="h">Hari Hujan (akumulasi)</div>
    <div class="num">{{ number_format($rainTotalHujan) }}</div>
    <div class="small text-muted">dari tabel <code>rain</code></div>
  </div>
  <div class="stat">
    <div class="h">Hari Tidak Hujan</div>
    <div class="num">{{ number_format($rainTotalTidak) }}</div>
    <div class="small text-muted">&nbsp;</div>
  </div>
  <div class="stat">
    <div class="h">Gempa (periode)</div>
    <div class="num">{{ number_format($gempaCount ?? 0) }}</div>
    <div class="small text-muted">10 terbaru di panel kanan</div>
  </div>
</div>

<div class="grid">
  <div class="panel">
    <div class="ph">Peta Sebaran Kejadian Per Kecamatan</div>
    <div class="pb">
      @if(($points->count() ?? 0) === 0)
        <div class="text-muted">Belum ada titik koordinat kejadian pada periode ini.</div>
      @endif
      <div class="small text-muted mt-1">*Data gempa tidak dipetakan karena tabel tidak memiliki koordinat.</div>
    </div>
    <div id="map"></div>
  </div>

  <div class="panel">
    <div class="ph">Ringkasan Dampak & Hujan</div>
    <div class="pb">
      <div class="impact">
        {{-- KORBAN --}}
        <div class="box">
          <div class="bh">Korban</div>
          <div class="d-flex justify-content-between"><span>Jiwa Meninggal</span><strong>{{ number_format($korban['meninggal'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Jiwa Luka-luka</span><strong>{{ number_format($korban['luka'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Jiwa Hilang</span><strong>{{ number_format($korban['hilang'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Jiwa Mengungsi</span><strong>{{ number_format($korban['mengungsi'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Total</span><strong>{{ number_format($korban['total'] ?? 0) }}</strong></div>
        </div>

        {{-- RUMAH --}}
        <div class="box">
          <div class="bh">Bangunan (Rumah) Terdampak</div>
          <div class="d-flex justify-content-between"><span>Rusak Berat</span><strong>{{ number_format($rumah['rb'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Rusak Sedang</span><strong>{{ number_format($rumah['rs'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Rusak Ringan</span><strong>{{ number_format($rumah['rr'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Total Unit</span><strong>{{ number_format($rumah['total'] ?? 0) }}</strong></div>
        </div>

        {{-- SARPRAS --}}
        <div class="box">
          <div class="bh">Fasilitas Umum/Sarpras</div>
          <div class="d-flex justify-content-between"><span>Rusak Berat</span><strong>{{ number_format($sarpras['rb'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Rusak Sedang</span><strong>{{ number_format($sarpras['rs'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Rusak Ringan</span><strong>{{ number_format($sarpras['rr'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Total Unit</span><strong>{{ number_format($sarpras['total'] ?? 0) }}</strong></div>
        </div>

        {{-- KERUGIAN --}}
        <div class="box">
          <div class="bh">Kerugian (Taksiran)</div>
          <div class="d-flex justify-content-between">
            <span>Total</span>
            <strong>Rp {{ number_format(($kerugian ?? 0), 0, ',', '.') }}</strong>
          </div>
        </div>

        {{-- HUJAN TOP 10 --}}
        <div class="box">
          <div class="bh">Hujan – 10 Kecamatan Tertinggi</div>
          <table class="mini-table">
            <thead><tr><th>Kecamatan</th><th class="text-end">Hari Hujan</th><th class="text-end">Tidak Hujan</th></tr></thead>
            <tbody>
              @forelse(($rainTop ?? collect()) as $r)
                <tr>
                  <td>{{ $r['kecamatan'] }}</td>
                  <td class="text-end">{{ $r['hujan'] }}</td>
                  <td class="text-end">{{ $r['tidak'] }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted">Belum ada data hujan pada periode ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- GEMPA TERBARU --}}
        <div class="box">
          <div class="bh">Gempa Terakhir (maks. 10)</div>
          <table class="mini-table">
            <thead><tr><th>Waktu</th><th class="text-end">SR</th><th>Lokasi</th><th>Keterangan</th></tr></thead>
            <tbody>
            @forelse(($gempaList ?? collect()) as $g)
              <tr>
                <td>{{ \Carbon\Carbon::parse($g['waktu'])->locale('id')->translatedFormat('d M Y H:i') }}</td>
                <td class="text-end">{{ is_null($g['mag']) ? '–' : number_format($g['mag'],1) }}</td>
                <td>{{ $g['lokasi'] }}</td>
                <td>{{ $g['keterangan'] }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-muted">Belum ada data gempa pada periode ini.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="panel mt-3">
  <div class="ph">Kejadian Per Bulan</div>
  <div class="chart-wrap"><canvas id="chartMonthly" height="88"></canvas></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
<script>
  // ===== MAP (kejadian) =====
  const points = @json($points);
  const map = L.map('map', { scrollWheelZoom:false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19}).addTo(map);
  const group = L.featureGroup().addTo(map);

  function makeIcon(num, color){
    const bg = color==='red' ? '#e03030' : (color==='yellow' ? '#f2b705' : '#1aa65d');
    const html = `<div style="display:flex;align-items:center;justify-content:center;
      width:30px;height:30px;border-radius:50%;background:${bg};color:#fff;
      font-weight:800;border:2px solid #fff;box-shadow:0 4px 10px rgba(0,0,0,.25)">${num}</div>`;
    return L.divIcon({ html, className:'', iconSize:[30,30] });
  }

  points.forEach(p=>{
    if (typeof p.lat==='number' && typeof p.lng==='number') {
      L.marker([p.lat,p.lng],{ icon:makeIcon(p.count,p.color) })
       .bindPopup(`<strong>${p.name}</strong><br>${p.count} kejadian`).addTo(group);
    }
  });

  if (group.getLayers().length){ map.fitBounds(group.getBounds().pad(0.2)); }
  else { map.setView([-8.001,112.63], 9); } // fallback Malang

  // ===== CHART =====
  const labels = @json($chartLabels ?? []);
  const data = @json($chartCounts ?? []);
  new Chart(document.getElementById('chartMonthly').getContext('2d'), {
    type:'line',
    data:{ labels, datasets:[{ label:'Kejadian', data, tension:.35, fill:false }]},
    options:{ plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true, ticks:{precision:0}}}}
  });
</script>
@endpush
