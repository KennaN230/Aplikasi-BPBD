@extends('layouts.public')
@section('title','Infografis Sebaran Kejadian')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  :root{--cream:#fff7f2;--paper:#fff;--ink:#0f172a;--muted:#667085;--brand:#ff7a45;--brand-2:#ff5b6b;--ok:#22c55e;--warn:#f59e0b;--danger:#ef4444;--shadow:0 18px 46px rgba(15,23,42,.10)}
  html,body{background:linear-gradient(180deg,var(--cream),#fff 70%);font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;color:var(--ink)}
  .hero{background:#fff;border-radius:22px;box-shadow:var(--shadow);padding:20px 22px;margin-bottom:16px}
  .hero-grid{display:grid;grid-template-columns:1.2fr .8fr;gap:24px;align-items:center}
  .title{font-size:28px;font-weight:800}
  .accent{background:linear-gradient(90deg,var(--brand),var(--brand-2));-webkit-background-clip:text;background-clip:text;color:transparent}
  .sub{color:var(--muted)}
  .period-form{display:flex;gap:8px;align-items:center;background:#fff;border:1px solid #ffe2d7;border-radius:999px;padding:10px 12px;box-shadow:0 12px 28px rgba(255,122,69,.18)}
  .period-form input{border:0;outline:0;height:40px;padding:0 12px;border-radius:999px}
  .btn-apply{height:40px;padding:0 16px;border:0;border-radius:999px;color:#fff;font-weight:800;background:linear-gradient(90deg,var(--brand),var(--brand-2))}
  .legend{display:flex;gap:10px;color:#6b7280;font-weight:600}
  .dot{width:14px;height:14px;border-radius:50%}.g{background:var(--ok)}.y{background:var(--warn)}.r{background:var(--danger)}
  .stat-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px}
  .stat{background:#fff;border:1px solid #ffe2d7;border-radius:22px;padding:16px;box-shadow:var(--shadow)}
  .stat .num{font-size:34px;font-weight:800;line-height:1}
  .grid{display:grid;grid-template-columns:2fr 1fr;gap:16px}
  .grid-left{display:flex;flex-direction:column;gap:16px}
  .panel{background:#fff;border:1px solid #ffe2d7;border-radius:22px;box-shadow:var(--shadow)}
  .ph{padding:14px 16px;border-bottom:1px solid #f3f4f6;font-weight:800}
  .pb{padding:12px 16px}
  #map{height:520px;border-radius:0 0 22px 22px;position:relative}
  .map-badge{position:absolute;right:14px;bottom:14px;background:#fff;border:1px solid #ffe2d7;border-radius:999px;padding:6px 10px;font-size:12px;box-shadow:var(--shadow)}
  .impact{display:grid;gap:12px}
  .impact .box{background:#fff;border:1px solid #ffe2d7;border-radius:16px;padding:12px;box-shadow:var(--shadow)}
  .box .bh{font-weight:800;margin-bottom:6px}
  .mini-table,.mini-table th,.mini-table td{background:#fff}
  .mini-table{width:100%;border-collapse:collapse}
  .mini-table th,.mini-table td{padding:8px 10px;border-bottom:1px solid #eef2f7;font-size:13px}
  .mini-table th{font-weight:800}
  .mini-table .col-idx{width:48px;text-align:center;color:#6b7280;font-weight:700}
  .chart-wrap{padding:8px 16px 18px}
  @media (max-width:992px){.hero-grid{grid-template-columns:1fr}.stat-cards{grid-template-columns:1fr 1fr}.grid{grid-template-columns:1fr}}

  /* ====== ICON GEMPA (lebih kecil) ====== */
  .quake-icon{position:relative;width:48px;height:48px;pointer-events:none}     /* dari 70 -> 48 */
  .quake-icon .ring{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);border:2px solid rgba(239,68,68,.95);border-radius:50%}
  .quake-icon .r1{width:30px;height:30px}                                       /* dari 48 -> 30 */
  .quake-icon .r2{width:44px;height:44px;opacity:.85}                           /* dari 68 -> 44 */
  .quake-icon .center{
    position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);
    width:22px;height:22px;border-radius:50%;                                   /* dari 34 -> 22 */
    background:radial-gradient(circle at 35% 35%,#fff59d,#fdd835);
    display:flex;align-items:center;justify-content:center;
    font-weight:800;color:#111;font-size:11px;
    box-shadow:0 0 0 2px #fff, 0 10px 20px rgba(0,0,0,.25);
    border:1px solid rgba(0,0,0,.12)
  }
</style>
@endpush

@section('content')
@php
  $pointsKec = collect($pointsKec ?? [])->map(function($it){
    $c = (int)($it->count ?? 0);
    $color = $c >= 10 ? 'red' : ($c >= 5 ? 'yellow' : 'green');
    return ['kecamatan'=>(string)($it->kecamatan ?? '-'),'count'=>$c,'lat'=>$it->lat ?? null,'lng'=>$it->lng ?? null,'color'=>$color];
  })->values();

  $pointsDesa = collect($pointsDesa ?? [])->map(function($it){
    $c = (int)($it->count ?? 0);
    $color = $c >= 10 ? 'red' : ($c >= 5 ? 'yellow' : 'green');
    return ['desa'=>(string)($it->desa ?? '-'),'kecamatan'=>(string)($it->kecamatan ?? '-'),'count'=>$c,'lat'=>$it->lat ?? null,'lng'=>$it->lng ?? null,'color'=>$color];
  })->values();

  $gempaPoints = collect($gempaPoints ?? []);
@endphp

<div class="hero">
  <div class="hero-grid">
    <div>
      <div class="title">Infografis <span class="accent">Sebaran Kejadian</span></div>
      <div class="sub">Pantau kejadian, dampak, hujan, dan gempa—dalam satu halaman.</div>
    </div>
    <div class="d-flex flex-column align-items-end gap-2">
      <form class="period-form" method="get" action="{{ route('landing') }}" aria-label="Filter Periode">
        <input id="inputFrom" type="date" name="from" value="{{ ($from ?? now())->toDateString() }}" aria-label="Dari tanggal">
        <span class="mx-2">s.d.</span>
        <input id="inputTo" type="date" name="to" value="{{ ($to ?? now())->toDateString() }}" aria-label="Sampai tanggal">
        <button type="submit" class="btn-apply">Terapkan</button>
      </form>
      <div class="legend">
        <div class="dot g"></div><span>1–4</span>
        <div class="dot y"></div><span>5–9</span>
        <div class="dot r"></div><span>10+</span>
      </div>
    </div>
  </div>
</div>

<div class="stat-cards">
  <div class="stat"><div class="fw-bold">Jumlah Kejadian</div><div class="num">{{ number_format($kejadianTotal) }}</div><div class="small text-muted">{{ number_format($kecDisplayCount ?? 0) }} kecamatan terlibat</div></div>
  <div class="stat"><div class="fw-bold">Hari Hujan (akumulasi)</div><div class="num">{{ number_format($rainTotalHujan) }}</div></div>
  <div class="stat"><div class="fw-bold">Hari Tidak Hujan</div><div class="num">{{ number_format($rainTotalTidak) }}</div></div>
  <div class="stat"><div class="fw-bold">Gempa (periode)</div><div class="num">{{ number_format($gempaCount ?? 0) }}</div><div class="small text-muted">10 terbaru di panel kanan</div></div>
</div>

<div class="grid">
  <div class="grid-left">
    <div class="panel position-relative">
      <div class="ph">Peta Sebaran Kejadian (kecamatan ⇄ desa otomatis)</div>
      <div class="pb">
        @if(($pointsKec->count() ?? 0) === 0)
          <div class="text-muted">Belum ada titik koordinat kejadian pada periode ini.</div>
        @endif
        <div class="small text-muted mt-1">*Titik dihitung dari semua kejadian yang memiliki koordinat pada periode terpilih.</div>
      </div>
      <div id="map"></div>
      <div class="map-badge">
        <span id="badgeText">Layer: -</span> ·
        Kec: {{ $countKec }} · Desa: {{ $countDesa }}
      </div>
    </div>

    <div class="panel">
      <div class="ph">Grafik Jumlah Kejadian Per Bulan</div>
      <div class="chart-wrap"><canvas id="chartMonthly" height="220"></canvas></div>
    </div>
  </div>

  <div class="panel">
    <div class="ph">Ringkasan Dampak & Hujan</div>
    <div class="pb">
      <div class="impact">
        <div class="box">
          <div class="bh">Korban</div>
          <div class="d-flex justify-content-between"><span>Jiwa Meninggal</span><strong>{{ number_format($korban['meninggal'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Jiwa Luka-luka</span><strong>{{ number_format($korban['luka'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Jiwa Hilang</span><strong>{{ number_format($korban['hilang'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Jiwa Mengungsi</span><strong>{{ number_format($korban['mengungsi'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Total Jiwa</span><strong>{{ number_format($korban['total'] ?? 0) }}</strong></div>
        </div>

        <div class="box">
          <div class="bh">Bangunan (Rumah) Terdampak</div>
          <div class="d-flex justify-content-between"><span>Rusak Berat</span><strong>{{ number_format($rumah['rb'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Rusak Sedang</span><strong>{{ number_format($rumah['rs'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Rusak Ringan</span><strong>{{ number_format($rumah['rr'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Total Unit</span><strong>{{ number_format($rumah['total'] ?? 0) }}</strong></div>
        </div>

        <div class="box">
          <div class="bh">Fasilitas Umum/Sarpras</div>
          <div class="d-flex justify-content-between"><span>Rusak Berat</span><strong>{{ number_format($sarpras['rb'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Rusak Sedang</span><strong>{{ number_format($sarpras['rs'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Rusak Ringan</span><strong>{{ number_format($sarpras['rr'] ?? 0) }}</strong></div>
          <div class="d-flex justify-content-between"><span>Total Unit</span><strong>{{ number_format($sarpras['total'] ?? 0) }}</strong></div>
        </div>

        <div class="box">
          <div class="bh">Kerugian (Taksiran)</div>
          <div class="d-flex justify-content-between"><span>Total</span><strong>Rp {{ number_format(($kerugian ?? 0), 0, ',', '.') }}</strong></div>
        </div>

        <div class="box">
          <div class="bh">Hujan – 10 Kecamatan Tertinggi</div>
          <table class="mini-table">
            <thead><tr><th class="col-idx">No</th><th>Kecamatan</th><th class="text-end">Hari Hujan</th><th class="text-end">Tidak Hujan</th></tr></thead>
            <tbody>
              @forelse(($rainTop ?? collect()) as $r)
                <tr>
                  <td class="col-idx">{{ $loop->iteration }}</td>
                  <td>{{ $r->kecamatan }}</td>
                  <td class="text-end">{{ $r->hujan }}</td>
                  <td class="text-end">{{ $r->tidak }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-muted text-center">Belum ada data hujan pada periode ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="box">
          <div class="bh">Gempa Terakhir (maks. 10)</div>
          <table class="mini-table">
            <thead>
              <tr><th class="col-idx">No</th><th>Tanggal</th><th>Jam</th><th class="text-end">SR</th><th>Lokasi</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
              @forelse(($gempaList ?? collect()) as $g)
                <tr>
                  <td class="col-idx">{{ $loop->iteration }}</td>
                  <td>{{ $g['tanggal'] }}</td>
                  <td>{{ $g['jam'] }}</td>
                  <td class="text-end">{{ is_null($g['mag']) ? '–' : number_format($g['mag'],1) }}</td>
                  <td>{{ $g['lokasi'] ?? '-' }}</td>
                  <td>{{ $g['keterangan'] ?? '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-muted text-center">Belum ada data gempa pada periode ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="kejadianModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="kejadianModalTitle">Detail Kejadian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div id="modalLoading" class="text-center py-4">
          <div class="spinner-border" role="status"></div>
          <div class="small text-muted mt-2">Memuat data…</div>
        </div>

        <div id="modalListJenis" class="d-none">
          <div class="mb-2 fw-bold">Daftar Kejadian di <span id="modalLokasiLabel"></span></div>
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="table-light"><tr><th>#</th><th>Jenis Kejadian</th><th class="text-end">Jumlah</th><th></th></tr></thead>
              <tbody id="modalJenisBody"><tr><td colspan="4" class="text-center text-muted">Tidak ada data.</td></tr></tbody>
            </table>
          </div>
        </div>

        <div id="modalDetailJenis" class="d-none">
          <ul class="nav nav-tabs" id="kejadianTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-lokasi" type="button" role="tab">Lokasi</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-korban" type="button" role="tab">Korban Jiwa</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kerusakan" type="button" role="tab">Kerusakan</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-upaya" type="button" role="tab">Upaya</button></li>
          </ul>
          <div class="tab-content border-start border-end border-bottom p-3">
            <div class="tab-pane fade show active" id="tab-lokasi" role="tabpanel"><table class="mini-table"><tbody id="kv-lokasi"></tbody></table></div>
            <div class="tab-pane fade" id="tab-korban" role="tabpanel"><table class="mini-table"><tbody id="kv-korban"></tbody></table></div>
            <div class="tab-pane fade" id="tab-kerusakan" role="tabpanel"><table class="mini-table"><tbody id="kv-kerusakan"></tbody></table></div>
            <div class="tab-pane fade" id="tab-upaya" role="tabpanel"><ul id="list-upaya" class="mb-0"></ul></div>
          </div>
        </div>

      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const ptsKec   = @json($pointsKec);
  const ptsDesa  = @json($pointsDesa);
  const quakePts = @json($gempaPoints);
  const ZOOM_SPLIT = 10;

  const toNum = v => { if (typeof v==='string') v=v.replace(',', '.'); const n=parseFloat(v); return Number.isFinite(n)?n:NaN; };
  const valid = (lat,lng) => Number.isFinite(lat)&&Number.isFinite(lng)&&lat>=-90&&lat<=90&&lng>=-180&&lng<=180&&!(lat===0&&lng===0);

  const map = L.map('map', {zoomControl:true, scrollWheelZoom:true, wheelDebounceTime:25, zoomDelta:0.1, zoomSnap:0.25, minZoom:7, maxZoom:19, preferCanvas:true});

  const baseOSM   = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19, attribution:'© OpenStreetMap' }).addTo(map);
  const baseCarto = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom:19, attribution:'© Carto' });
  const baseEsri  = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxNativeZoom:17, maxZoom:19, attribution:'Tiles © Esri' });

  const groupKec   = L.featureGroup().addTo(map);
  const groupDesa  = L.featureGroup();
  const groupGempa = L.featureGroup().addTo(map);

  L.control.layers(
    { "Streets (OSM)": baseOSM, "Streets (Carto)": baseCarto, "Satellite (Esri)": baseEsri },
    { "Gempa": groupGempa },
    { collapsed:true, position:'topright' }
  ).addTo(map);

  function makeIcon(num, color){
    const bg = color==='red' ? '#ef4444' : (color==='yellow' ? '#f59e0b' : '#22c55e');
    const html = `<div style="display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:${bg};color:#fff;font-weight:800;border:2px solid #fff;box-shadow:0 10px 24px rgba(0,0,0,.22)">${num}</div>`;
    return L.divIcon({ html, className:'', iconSize:[34,34] });
  }

  // ikon "bullseye" gempa (lebih kecil)
  function makeQuakeIcon(mag){
    const label = (mag==null || isNaN(mag)) ? '•' : Number(mag).toFixed(1);
    const html = `
      <div class="quake-icon">
        <div class="ring r2"></div>
        <div class="ring r1"></div>
        <div class="center">${label}</div>
      </div>`;
    return L.divIcon({ html, className:'', iconSize:[48,48], iconAnchor:[24,24], popupAnchor:[0,-22] }); // dulu 70,35
  }

  // titik kejadian
  ptsKec.forEach(p=>{
    const lat = toNum(p.lat), lng = toNum(p.lng); if(!valid(lat,lng)) return;
    const html = `<div class="fw-bold">${p.kecamatan}</div><div class="mb-1">${p.count} kejadian</div><button type="button" class="btn btn-success btn-sm js-next" data-kec="${encodeURIComponent(p.kecamatan)}">Next</button>`;
    L.marker([lat,lng],{ icon:makeIcon(p.count,p.color) }).bindPopup(html).addTo(groupKec);
  });
  ptsDesa.forEach(p=>{
    const lat = toNum(p.lat), lng = toNum(p.lng); if(!valid(lat,lng)) return;
    const html = `<div class="fw-bold">${p.desa}</div><div class="small text-muted">${p.kecamatan}</div><div class="mb-1">${p.count} kejadian</div><button type="button" class="btn btn-primary btn-sm js-next" data-kec="${encodeURIComponent(p.kecamatan)}" data-desa="${encodeURIComponent(p.desa)}">Next</button>`;
    L.marker([lat,lng],{ icon:makeIcon(p.count,p.color) }).bindPopup(html).addTo(groupDesa);
  });

  // titik gempa
  quakePts.forEach(g=>{
    const lat = toNum(g.latitude), lng = toNum(g.longitude); if(!valid(lat,lng)) return;
    const mag = g.sr==null ? null : Number(toNum(g.sr));
    const tgl = g.tanggal ? ` <span class="small text-muted">${g.tanggal}${g.waktu ? ' '+g.waktu : ''}</span>` : '';
    const html = `<div class="fw-bold">${g.label || 'Gempa'}${tgl}</div><div>Magnitudo: ${mag!=null ? mag.toFixed(1)+' SR' : '-'}</div><div class="small text-muted">${lat.toFixed(3)}, ${lng.toFixed(3)}</div>`;
    L.marker([lat,lng], { icon: makeQuakeIcon(mag) }).bindPopup(html).addTo(groupGempa);
  });

  // wiring tombol Next
  function attachNextHandler(layerGroup){
    layerGroup.on('popupopen', (e) => {
      const el = e.popup.getElement();
      if (!el) return;
      const btn = el.querySelector('.js-next');
      if (!btn) return;
      btn.onclick = (ev) => {
        ev.preventDefault(); ev.stopPropagation();
        map.closePopup();
        const kec  = decodeURIComponent(btn.dataset.kec || '');
        const desa = btn.dataset.desa ? decodeURIComponent(btn.dataset.desa) : null;
        openLokasiModal(kec, desa);
      };
    });
  }
  attachNextHandler(groupKec);
  attachNextHandler(groupDesa);

  // initial view
  if (groupKec.getLayers().length) map.fitBounds(groupKec.getBounds().pad(0.15));
  else if (groupDesa.getLayers().length) map.fitBounds(groupDesa.getBounds().pad(0.15));
  else if (groupGempa.getLayers().length) map.fitBounds(groupGempa.getBounds().pad(0.15));
  else map.setView([-8.001, 112.63], 10);

  const badge = document.getElementById('badgeText');
  function syncLayers(){
    const wantDesa = map.getZoom() >= ZOOM_SPLIT;
    const desaAvailable = groupDesa.getLayers().length > 0;
    if (wantDesa && desaAvailable) {
      if (!map.hasLayer(groupDesa)) map.addLayer(groupDesa);
      if (map.hasLayer(groupKec))   map.removeLayer(groupKec);
      badge.textContent = 'Layer: Desa';
    } else {
      if (!map.hasLayer(groupKec))  map.addLayer(groupKec);
      if (map.hasLayer(groupDesa))  map.removeLayer(groupDesa);
      badge.textContent = 'Layer: Kecamatan';
    }
  }
  map.on('zoomend', syncLayers);
  syncLayers();

  setTimeout(() => map.invalidateSize(), 0);
  window.addEventListener('resize', () => map.invalidateSize());

  // ===== Modal (tetap) =====
  const modalEl = document.getElementById('kejadianModal');
  const bsModal = new bootstrap.Modal(modalEl);
  const $ = (sel) => modalEl.querySelector(sel);

  function setView(state){
    $('#modalLoading').classList.toggle('d-none', state!=='loading');
    $('#modalListJenis').classList.toggle('d-none', state!=='list');
    $('#modalDetailJenis').classList.toggle('d-none', state!=='detail');
  }

  function renderListJenis(labelLokasi, list, ctx){
    document.getElementById('kejadianModalTitle').textContent = 'Informasi Kejadian';
    document.getElementById('modalLokasiLabel').textContent = labelLokasi;
    const body = $('#modalJenisBody');
    if (!Array.isArray(list) || list.length===0){
      body.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Tidak ada data.</td></tr>`;
      return;
    }
    body.innerHTML = list.map((row,i)=>`
      <tr>
        <td>${i+1}</td>
        <td>${row.jenis ?? '-'}</td>
        <td class="text-end">${row.jumlah ?? 0}</td>
        <td class="text-end">
          <button class="btn btn-primary btn-sm js-open-detail"
            data-jenis="${encodeURIComponent(row.id ?? '')}"
            data-jenis-label="${encodeURIComponent(row.jenis ?? '')}"
            data-kec="${encodeURIComponent(ctx.kecamatan)}"
            ${ctx.desa ? `data-desa="${encodeURIComponent(ctx.desa)}"` : ''}>Lihat</button>
        </td>
      </tr>
    `).join('');

    body.querySelectorAll('.js-open-detail').forEach(btn=>{
      btn.onclick = () => {
        const jenisSlug  = decodeURIComponent(btn.dataset.jenis||'');
        const jenisLabel = decodeURIComponent(btn.dataset.jenisLabel||'');
        const kec        = decodeURIComponent(btn.dataset.kec||'');
        const desaAttr   = btn.dataset.desa ? decodeURIComponent(btn.dataset.desa) : null;
        fetchDetailJenis({kecamatan:kec, desa:desaAttr}, jenisSlug, jenisLabel);
      };
    });
  }

  function renderDetailJenis(jenisLabel, data){
    document.getElementById('kejadianModalTitle').textContent = jenisLabel || 'Detail Kejadian';
    const LOK = data?.lokasi || {};
    $('#kv-lokasi').innerHTML = `
      <tr><td>Desa</td><td>${LOK.desa || '-'}</td></tr>
      <tr><td>Kecamatan</td><td>${LOK.kecamatan || '-'}</td></tr>
      <tr><td>Kabupaten</td><td>${LOK.kabupaten || 'Malang'}</td></tr>
      <tr><td>Provinsi</td><td>${LOK.provinsi || 'Jawa Timur'}</td></tr>
      <tr><td>Foto</td><td>${LOK.foto_url ? `<a href="${LOK.foto_url}" target="_blank" rel="noopener">Lihat Foto</a>` : '-'}</td></tr>
      <tr><td>Tanggal Kejadian</td><td>${LOK.tanggal || '-'}</td></tr>
    `;
    const K = data?.korban || {};
    $('#kv-korban').innerHTML = `
      <tr><td>Jiwa Meninggal</td><td>${K.meninggal ?? 0}</td></tr>
      <tr><td>Jiwa Luka-luka</td><td>${K.luka ?? 0}</td></tr>
      <tr><td>Jiwa Hilang</td><td>${K.hilang ?? 0}</td></tr>
      <tr><td>Jiwa Mengungsi</td><td>${K.mengungsi ?? 0}</td></tr>
      <tr><td>Total Jiwa</td><td>${K.total ?? 0}</td></tr>
    `;
    const R = data?.kerusakan || {}, RH = R.rumah || {}, SP = R.sarpras || {};
    $('#kv-kerusakan').innerHTML = `
      <tr><td>Rumah Rusak Berat</td><td>${RH.rb ?? 0}</td></tr>
      <tr><td>Rumah Rusak Sedang</td><td>${RH.rs ?? 0}</td></tr>
      <tr><td>Rumah Rusak Ringan</td><td>${RH.rr ?? 0}</td></tr>
      <tr><td>Total Rumah</td><td>${RH.total ?? ((RH.rb||0)+(RH.rs||0)+(RH.rr||0))}</td></tr>
      <tr><td>Sarpras Rusak Berat</td><td>${SP.rb ?? 0}</td></tr>
      <tr><td>Sarpras Rusak Sedang</td><td>${SP.rs ?? 0}</td></tr>
      <tr><td>Sarpras Rusak Ringan</td><td>${SP.rr ?? 0}</td></tr>
      <tr><td>Total Sarpras</td><td>${SP.total ?? ((SP.rb||0)+(SP.rs||0)+(SP.rr||0))}</td></tr>
    `;
    const U = Array.isArray(data?.upaya) ? data.upaya : [];
    document.getElementById('list-upaya').innerHTML = U.length ? U.map(i=>`<li>${i}</li>`).join('') : '<li class="text-muted">Belum ada data upaya.</li>';
  }

  async function openLokasiModal(kecamatan, desa){
    bsModal.show(); setView('loading');
    const f = document.getElementById('inputFrom')?.value || '', t = document.getElementById('inputTo')?.value || '';
    const qp = new URLSearchParams(); if (f) qp.set('from', f); if (t) qp.set('to', t);
    const qs = qp.toString() ? `?${qp.toString()}` : '';
    try{
      let url, label;
      if (desa) { url = `/kejadian/daerah/${encodeURIComponent(kecamatan)}/desa/${encodeURIComponent(desa)}${qs}`; label = `Desa ${desa}, Kec. ${kecamatan}`; }
      else { url = `/kejadian/daerah/${encodeURIComponent(kecamatan)}${qs}`; label = `Kec. ${kecamatan}`; }
      const res = await fetch(url);
      const json = await res.json();
      renderListJenis(label, json.rangkuman || [], {kecamatan, desa: desa || null});
      setView('list');
    }catch(err){
      setView('list');
      document.getElementById('modalJenisBody').innerHTML = `<tr><td colspan="4" class="text-danger text-center">Gagal memuat data.</td></tr>`;
      console.error(err);
    }
  }

  async function fetchDetailJenis(ctx, jenisSlug, jenisLabel){
    setView('loading');
    const f = document.getElementById('inputFrom')?.value || '', t = document.getElementById('inputTo')?.value || '';
    const qp = new URLSearchParams(); if (f) qp.set('from', f); if (t) qp.set('to', t);
    const qs = qp.toString() ? `?${qp.toString()}` : '';
    try{
      const url = ctx.desa
        ? `/kejadian/daerah/${encodeURIComponent(ctx.kecamatan)}/desa/${encodeURIComponent(ctx.desa)}/${encodeURIComponent(jenisSlug)}${qs}`
        : `/kejadian/daerah/${encodeURIComponent(ctx.kecamatan)}/${encodeURIComponent(jenisSlug)}${qs}`;
      const res = await fetch(url);
      const json = await res.json();
      renderDetailJenis(jenisLabel, json);
      setView('detail');
    }catch(err){
      renderDetailJenis(jenisLabel, {}); setView('detail');
    }
  }

  // CHART
  const rawLabels = @json($chartLabels ?? []);
  const counts    = (@json($chartCounts ?? [])).map(Number);
  const MONTHS = ['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGS','SEP','OKT','NOV','DES'];
  const labels = rawLabels.map(ym => { const y = ym.slice(0,4), m = parseInt(ym.slice(5,7),10); return [MONTHS[m-1], y]; });
  const ctx = document.getElementById('chartMonthly')?.getContext('2d');
  if (ctx && typeof Chart!=='undefined'){
    const valueLabel = { id:'valueLabel', afterDatasetsDraw(chart){ const {ctx}=chart, ds=chart.data.datasets[0], m=chart.getDatasetMeta(0); ctx.save(); m.data.forEach((b,i)=>{ const v=ds.data[i]; if(v==null) return; const p=b.tooltipPosition(); ctx.font='bold 12px Poppins,system-ui'; ctx.fillStyle='#111827'; ctx.textAlign='center'; ctx.textBaseline='bottom'; ctx.fillText(v, p.x, b.y-6); }); ctx.restore(); } };
    new Chart(ctx, { type:'bar', data:{ labels, datasets:[{ label:'Kejadian', data:counts, backgroundColor:'#ff8a63', borderRadius:8, hoverBackgroundColor:'#ff6a7a' }]},
      options:{ responsive:true, maintainAspectRatio:false, layout:{ padding:{top:18,right:12,left:12,bottom:6}},
        plugins:{ legend:{display:false}, tooltip:{enabled:true}, valueLabel:{} },
        scales:{ x:{ grid:{display:false}, ticks:{ color:'#475569', font:{weight:'700'} } }, y:{ beginAtZero:true, grid:{ color:'#eef2f7' }, ticks:{ color:'#64748b', precision:0 } } }}, plugins:[valueLabel] });
  }
});
</script>
@endpush