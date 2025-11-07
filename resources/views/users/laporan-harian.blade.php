{{-- resources/views/laporan-harian.blade.php --}}
@php
  use Illuminate\Support\Collection;
  use Carbon\Carbon;

  $fmt = fn($d,$f='d F Y') => $d ? Carbon::parse($d)->locale('id')->translatedFormat($f) : '-';

  $fmtRange = $start->locale('id')->translatedFormat('l, d F Y').' – '.$end->locale('id')->translatedFormat('l, d F Y');

  // Deteksi bentuk data kejadian & gunung
  $kejIsCollection = isset($kejadian) && $kejadian instanceof Collection;
  $gunIsCollection = isset($gunung) && $gunung instanceof Collection;

  // Helper ambil field aman (array / object / relasi)
  $get = function ($item, $key, $default='-') {
      if (is_array($item) || is_object($item)) return data_get($item, $key, $default);
      return $default;
  };

  // Helper format waktu gabung tanggal+jam
  $labelWaktu = function ($item) use ($get) {
      $wktJoin = $get($item,'waktu_kejadian'); // kalau ada string datetime langsung
      $tgl = $get($item,'tanggal');
      $jam = $get($item,'waktu');

      try {
          if ($wktJoin) {
              return Carbon::parse($wktJoin)->format('d/m/Y H:i');
          }
          if ($tgl && $jam) {
              return Carbon::parse($tgl.' '.$jam)->format('d/m/Y H:i');
          }
          if ($tgl) {
              return Carbon::parse($tgl)->format('d/m/Y');
          }
          if ($jam) {
              // jam saja
              return Carbon::parse($jam)->format('H:i');
          }
      } catch (\Throwable $e) {
          // fallback aman
      }
      return '-';
  };

  // Helper jenis bencana
  $labelJenis = fn($item) =>
      $get($item,'namaKejadian.nama')   // relasi namaKejadian
      ?? $get($item,'jenisBencana.nama')
      ?? $get($item,'jenis')
      ?? '-';

  // Helper kecamatan (ambil dari relasi atau kolom nama)
  $labelKec = function ($item) use ($get) {
      return $get($item,'kecamatan.nama')
          ?? $get($item,'kecamatan_nama')
          ?? $get($item,'kecamatan')
          ?? $get($item,'kec')
          ?? '-';
  };

  // Helper lokasi (komposisi aman)
  $labelLokasi = function ($item) use ($get, $labelKec) {
      $parts = [];
      $alamat = $get($item,'lokasi') ?? $get($item,'alamat') ?? $get($item,'deskripsi');
      if ($alamat) $parts[] = $alamat;

      $desa = $get($item,'desa.nama') ?? $get($item,'desa_nama');
      if ($desa) $parts[] = 'Desa '.$desa;

      $kec = $labelKec($item);
      if ($kec && $kec !== '-') $parts[] = 'Kec. '.$kec;

      return $parts ? implode(', ', $parts) : '-';
  };

  // Helper sumber
  $labelSumber = fn($item) =>
      $get($item,'sumber')
      ?? $get($item,'sumber_info')
      ?? '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Harian Pusdalops</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  @page { size: A4; margin: 18mm 16mm 20mm 16mm; }
  *{ box-sizing: border-box; }
  body{ font-family: "DejaVu Sans", Arial, Helvetica, sans-serif; color:#111; }
  .toolbar{ position: fixed; top: 8px; right: 12px; display:flex; gap:8px }
  .btn{ padding:8px 12px; border:1px solid #ddd; border-radius:8px; text-decoration:none; color:#0f2a4a; font-weight:700; background:#efe8e0 }
  .btn:hover{ background:#e6ddd3 }
  .page{ page-break-after: always; }
  .no-break{ page-break-inside: avoid; }

  .kop{ text-align:center; border-bottom:3px solid #000; padding-bottom:6px; margin-bottom:10px }
  .kop .l1{ font-weight:800; font-size:16px; letter-spacing:.3px }
  .kop .l2{ font-weight:800; font-size:16px; letter-spacing:.3px }
  .kop .alamat{ font-size:12px; margin-top:2px }
  .kop .kontak{ font-size:12px }
  .kop .email{ font-size:12px; margin-top:2px }

  .section-title{ font-weight:800; font-size:16px; text-align:center; margin:12px 0 10px; text-transform:uppercase }
  .grid-2{ display:grid; grid-template-columns: 1fr 1fr; gap:10px; }
  .mt-6{ margin-top:6px } .mt-10{ margin-top:10px } .mt-14{ margin-top:14px }
  .mb-0{ margin-bottom:0 } .mb-6{ margin-bottom:6px } .mb-10{ margin-bottom:10px }

  .table{ width:100%; border-collapse:collapse; }
  .table th, .table td{ border:1px solid #333; padding:6px 8px; font-size:12.5px; vertical-align:top }
  .table th{ text-align:center; font-weight:800; background:#f2f2f2 }

  .small{ font-size:12px } .tiny{ font-size:11px } .nowrap{ white-space: nowrap; }
  .hr{ height:2px; background:#000; margin:6px 0 10px }

  .ttd-wrap{ display:flex; gap:24px; justify-content:space-between; margin-top:20px }
  .ttd-box{ width:48%; text-align:center; }
  .ttd-space{ height:60px } /* ruang tanda tangan */

  .list{ margin:0; padding-left:16px; }
  .list li{ margin:2px 0 }

  .doc-grid{ display:grid; grid-template-columns:1fr 1fr; gap:10px }
  .doc-ph{ height:220px; border:1px dashed #bbb; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#999 }

  img.doc{ width:100%; height:220px; object-fit:cover; border-radius:8px; border:1px solid #ddd }

  @media print{
    .toolbar{ display:none }
    a{ color:inherit; text-decoration:none }
  }
</style>
</head>
<body>

<div class="toolbar">
  <a href="{{ url()->previous() ?? url('/') }}" class="btn">Kembali</a>
  <a href="#" class="btn" onclick="window.print()">Cetak PDF</a>
</div>

{{-- ==================== SAMPUL / SURAT PENGANTAR ==================== --}}
<section class="page">
  <div class="kop">
    <div class="l1">{{ data_get($kop,'instansi1','') }}</div>
    <div class="l2">{{ data_get($kop,'instansi2','') }}</div>
    <div class="alamat">{{ data_get($kop,'alamat','') }}</div>
    <div class="kontak">{{ data_get($kop,'kontak','') }}</div>
    <div class="email">{{ data_get($kop,'email','') }}</div>
  </div>

  <div class="grid-2">
    <div></div>
    <div class="small" style="text-align:right">
      {{ data_get($surat,'kota_tanggal','') }}
    </div>
  </div>

  <table class="table mt-6" style="border:0">
    <tr style="border:0">
      <td style="border:0;width:70px" class="small">Nomor</td>
      <td style="border:0" class="small">: {{ data_get($surat,'nomor','') }}</td>
      <td style="border:0" rowspan="4" class="small" >
        Kepada Yth.<br>
        {{ data_get($surat,'tujuan','') }}<br>
        di-<br>
        <b>{{ data_get($surat,'tujuan_kota','') }}</b>
      </td>
    </tr>
    <tr style="border:0">
      <td style="border:0" class="small">Sifat</td>
      <td style="border:0" class="small">: {{ data_get($surat,'sifat','') }}</td>
    </tr>
    <tr style="border:0">
      <td style="border:0" class="small">Lampiran</td>
      <td style="border:0" class="small">: {{ data_get($surat,'lampiran','') }}</td>
    </tr>
    <tr style="border:0">
      <td style="border:0" class="small">Perihal</td>
      <td style="border:0" class="small">: <b>{{ data_get($surat,'perihal','') }}</b></td>
    </tr>
  </table>

  <p class="small mt-10">{{ data_get($surat,'paragraf','') }}</p>
  <p class="small">{{ data_get($surat,'penutup','') }}</p>

  <div class="ttd-wrap">
    <div></div>
    <div class="ttd-box">
      <div class="small">{{ data_get($surat,'pejabat.jabatan','') }}</div>
      <div class="ttd-space"></div>
      <div><b>{{ data_get($surat,'pejabat.nama','') }}</b></div>
      <div class="tiny">{{ data_get($surat,'pejabat.pangkat','') }}</div>
      <div class="tiny">{{ data_get($surat,'pejabat.nip','') }}</div>
    </div>
  </div>

  <div class="mt-14">
    <div class="small"><b>TEMBUSAN :</b></div>
    <ol class="small" style="margin-top:4px; padding-left:18px">
      @foreach((array) data_get($surat,'tembusan',[]) as $i=>$t)
        <li>{{ $t }}</li>
      @endforeach
    </ol>
  </div>
</section>

{{-- ==================== INTI LAPORAN ==================== --}}
<section class="page">
  <div class="kop">
    <div class="l1">{{ data_get($kop,'instansi1','') }}</div>
    <div class="l2">{{ data_get($kop,'instansi2','') }}</div>
    <div class="alamat">{{ data_get($kop,'alamat','') }}</div>
    <div class="kontak">{{ data_get($kop,'kontak','') }}</div>
    <div class="email">{{ data_get($kop,'email','') }}</div>
  </div>

  <div class="section-title">LAPORAN HARIAN PUSDALOPS</div>
  <p class="small mb-10">Dilaporkan kejadian alam dan bencana di wilayah Kabupaten Malang pada {{ $fmtRange }} (07.00 – 07.00 WIB). Informasi Pusdalops sebagai berikut:</p>

  {{-- ==== MODE 1: KEJADIAN DARI DB (Collection daftar kejadian) ==== --}}
  @if($kejIsCollection)
    <h4 class="mb-6">Daftar Kejadian Bencana</h4>
    <table class="table">
      <thead>
        <tr>
          <th style="width:36px">No</th>
          <th style="width:130px">Waktu</th>
          <th>Jenis</th>
          <th>Lokasi</th>
          <th style="width:120px">Kecamatan</th>
          <th>Sumber</th>
        </tr>
      </thead>
      <tbody>
        @forelse($kejadian as $i => $k)
          @php
            $waktuLabel = $labelWaktu($k);
            $jenis      = $labelJenis($k);
            $lok        = $labelLokasi($k);
            $kec        = $labelKec($k);
            $src        = $labelSumber($k);
          @endphp
          <tr>
            <td style="text-align:center">{{ $i+1 }}</td>
            <td class="nowrap">{{ $waktuLabel }}</td>
            <td>{{ $jenis }}</td>
            <td>{{ $lok }}</td>
            <td>{{ $kec }}</td>
            <td>{{ $src }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="small" style="text-align:center">Tidak ada kejadian pada rentang ini.</td></tr>
        @endforelse
      </tbody>
    </table>

    {{-- Ringkasan opsional: ambil kejadian pertama sebagai "contoh" uraian --}}
    @php $k0 = $kejadian->first(); @endphp
    @if($k0)
      <h4 class="mt-10 mb-6">Kronologi (Contoh Kejadian)</h4>
      <p class="small">{{ $get($k0,'kronologi','-') }}</p>
    @endif

  {{-- ==== MODE 2: KEJADIAN RINGKAS (array seperti template lama) ==== --}}
  @else
    <h4 class="mb-6">Kejadian Bencana</h4>
    <table class="table">
      <tr><th style="width:180px">Jenis Kejadian</th><td>{{ data_get($kejadian,'jenis','-') }}</td></tr>
      <tr><th>Tanggal</th><td>{{ data_get($kejadian,'tanggal','-') }}</td></tr>
      <tr><th>Pukul</th><td>{{ data_get($kejadian,'pukul','-') }}</td></tr>
      <tr><th>Lokasi</th><td>{{ data_get($kejadian,'lokasi','-') }}</td></tr>
      <tr><th>Kecamatan</th><td>{{ data_get($kejadian,'kecamatan','-') }}</td></tr>
      <tr><th>Kabupaten</th><td>{{ data_get($kejadian,'kabupaten','-') }}</td></tr>
      <tr><th>Sumber Info</th><td>{{ data_get($kejadian,'sumber_info','-') }}</td></tr>
      <tr><th>Info Masuk</th><td>{{ data_get($kejadian,'info_masuk','-') }}</td></tr>
    </table>

    <h4 class="mt-10 mb-6">Kronologi</h4>
    <p class="small">{{ data_get($kejadian,'kronologi','-') }}</p>

    <h4 class="mt-10 mb-6">Dampak</h4>
    <ul class="list small">
      @foreach((array) data_get($kejadian,'dampak',[]) as $d)
        <li>{{ $d }}</li>
      @endforeach
    </ul>

    <div class="grid-2 mt-10">
      <div>
        <h4 class="mb-6">Korban Jiwa</h4>
        <p class="small">{{ data_get($kejadian,'korban_jiwa','-') }}</p>
      </div>
      <div>
        <h4 class="mb-6">Kebutuhan Mendesak</h4>
        <ul class="list small">
          @foreach((array) data_get($kejadian,'kebutuhan',[]) as $k)
            <li>{{ $k }}</li>
          @endforeach
        </ul>
      </div>
    </div>

    <div class="grid-2 mt-10">
      <div>
        <h4 class="mb-6">Upaya yang Dilakukan</h4>
        <ul class="list small">
          @foreach((array) data_get($kejadian,'upaya',[]) as $u)
            <li>{{ $u }}</li>
          @endforeach
        </ul>
      </div>
      <div>
        <h4 class="mb-6">Kondisi Saat Ini</h4>
        <p class="small">{{ data_get($kejadian,'kondisi','-') }}</p>
      </div>
    </div>

    <h4 class="mt-10 mb-6">Unsur yang Terlibat</h4>
    <ul class="list small">
      @foreach((array) data_get($kejadian,'unsur',[]) as $u)
        <li>{{ $u }}</li>
      @endforeach
    </ul>

    <p class="small mt-10">Demikian update laporan dibuat pukul {{ data_get($kejadian,'update_waktu','-') }}. Apabila terdapat perkembangan informasi akan disampaikan pada update berikutnya.</p>
  @endif

  {{-- (Opsional) Tampilkan Curah Hujan bila dikirim dari controller --}}
  @if(isset($curah_hujan) && $curah_hujan instanceof \Illuminate\Support\Collection && $curah_hujan->count())
    <h4 class="mt-14 mb-6">Curah Hujan</h4>
    <table class="table">
      <thead><tr><th style="width:60px">No</th><th>Tanggal</th><th>Kecamatan</th><th>Hari Hujan</th><th>Hari Tidak Hujan</th></tr></thead>
      <tbody>
        @foreach($curah_hujan as $i => $r)
          <tr>
            <td style="text-align:center">{{ $i+1 }}</td>
            <td class="nowrap">{{ optional($r->hari_tanggal)->format('d/m/Y') }}</td>
            <td>{{ $r->kecamatan }}</td>
            <td style="text-align:center">{{ $r->hari_hujan }}</td>
            <td style="text-align:center">{{ $r->hari_tidak_hujan }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <div class="mt-10 small">
    <b>Alamat Pos Komando Penanggulangan Bencana</b><br>
    Badan Penanggulangan Bencana Daerah Kabupaten Malang:<br>
    Jl. Raya Trunojoyo Kav.08, Kec. Kepanjen, Kabupaten Malang.
  </div>

  <div class="grid-2 small mt-6">
    <div>
      <b>Frekuensi Radio VHF</b><br>
      Output: 148.270 MHz<br>
      Input: 153.600 MHz<br>
      Tune: 88,5 MHz
    </div>
    <div>
      <b>Frekuensi Radio UHF</b><br>
      Output: 444.995 MHz<br>
      Input: 435.500 MHz<br>
      Tune: CTCSS 127.3 MHz
    </div>
  </div>

  <div class="small mt-6">
    <b>Media Center BPBD</b><br>
    https://linktr.ee/pusdalopspbmalangkab
  </div>

  <div class="small mt-10">
    <b>Kegiatan</b><br>
    Kegiatan penanganan banjir di Desa Sitiarjo, Kec. Sumbermanjing Wetan, Kabupaten Malang.
  </div>
</section>

{{-- ==================== AKTIVITAS GUNUNG API ==================== --}}
<section class="page">
  <h3 class="section-title">Aktivitas Gunung Api di Kabupaten Malang</h3>

  {{-- MODE DB (Collection) --}}
  @if($gunIsCollection)
    @forelse($gunung as $g)
      @php
        $tglG = $get($g,'tanggal');
        // Jika tanggal sudah diformat string (mis. "12 Oktober 2025"), print apa adanya.
        // Jika berupa Y-m-d/Carbon, coba format ID.
        try {
            $tglLabel = $tglG instanceof \Carbon\Carbon
              ? $tglG->locale('id')->translatedFormat('d F Y')
              : (\Illuminate\Support\Str::isMatch('/^\d{4}-\d{2}-\d{2}/',$tglG ?? '')
                    ? \Carbon\Carbon::parse($tglG)->locale('id')->translatedFormat('d F Y')
                    : ($tglG ?: '-'));
        } catch (\Throwable $e) {
            $tglLabel = $tglG ?: '-';
        }
      @endphp
      <div class="no-break" style="margin-bottom:14px">
        <h4 style="margin:0 0 4px"><b>Gunung {{ $get($g,'nama','-') }}</b></h4>
        <div class="small mb-6">Tanggal {{ $tglLabel }}</div>

        <div class="small"><b>Meteorologi</b></div>
        <p class="small">{{ $get($g,'meteorologi','-') }}</p>

        <div class="small"><b>Visual</b></div>
        <p class="small">{{ $get($g,'visual','-') }}</p>

        <div class="small"><b>Aktivitas Vulkanik</b></div>
        <p class="small">{{ $get($g,'aktivitas','-') }}</p>

        <div class="small"><b>Rekomendasi</b></div>
        <p class="small">{{ $get($g,'rekomendasi','-') }}</p>

        @php $docs = (array) $get($g,'dokumentasi',[]); @endphp
        <div class="small"><b>Dokumentasi Gunung {{ $get($g,'nama','-') }}</b></div>
        @if(!empty($docs))
          <div class="doc-grid">
            @foreach($docs as $img)
              <img class="doc" src="{{ $img }}">
            @endforeach
          </div>
        @else
          <div class="doc-grid"><div class="doc-ph">— foto —</div><div class="doc-ph">— foto —</div></div>
        @endif
      </div>
    @empty
      <p class="small">Tidak ada data aktivitas gunung.</p>
    @endforelse

  {{-- MODE array lama --}}
  @else
    @foreach((array) $gunung as $g)
      <div class="no-break" style="margin-bottom:14px">
        <h4 style="margin:0 0 4px"><b>Gunung {{ data_get($g,'nama','-') }}</b></h4>
        <div class="small mb-6">Tanggal {{ data_get($g,'tanggal','-') }}</div>

        <div class="small"><b>Meteorologi</b></div>
        <p class="small">{{ data_get($g,'meteorologi','-') }}</p>

        <div class="small"><b>Visual</b></div>
        <p class="small">{{ data_get($g,'visual','-') }}</p>

        <div class="small"><b>Aktivitas Vulkanik</b></div>
        <p class="small">{{ data_get($g,'aktivitas','-') }}</p>

        <div class="small"><b>Rekomendasi</b></div>
        <p class="small">{{ data_get($g,'rekomendasi','-') }}</p>

        <div class="small"><b>Dokumentasi Gunung {{ data_get($g,'nama','-') }}</b></div>
        @if(!empty(data_get($g,'dokumentasi',[])))
          <div class="doc-grid">
            @foreach(data_get($g,'dokumentasi',[]) as $img)
              <img class="doc" src="{{ $img }}">
            @endforeach
          </div>
        @else
          <div class="doc-grid"><div class="doc-ph">— foto —</div><div class="doc-ph">— foto —</div></div>
        @endif
      </div>
    @endforeach
  @endif
</section>

{{-- ==================== HOTSPOT, PERINGATAN DINI, PRAKIRAAN ==================== --}}
<section class="page">
  <h4 class="mb-6">Titik Panas di Kabupaten Malang</h4>
  <div class="small mb-6">Pantauan tanggal {{ $start->locale('id')->translatedFormat('d F Y') }} sampai pukul {{ data_get($hotspot,'pukul1','-') }}:</div>
  <table class="table">
    <thead><tr><th style="width:60px">No</th><th>Kecamatan</th><th class="nowrap">Jumlah Hotspot</th></tr></thead>
    <tbody>
      @foreach((array) data_get($hotspot,'data1',[]) as $i=>$row)
        <tr><td style="text-align:center">{{ $i+1 }}</td><td>{{ data_get($row,'kecamatan','-') }}</td><td style="text-align:center">{{ data_get($row,'jumlah','-') }}</td></tr>
      @endforeach
    </tbody>
  </table>

  <div class="small mt-10 mb-6">Pantauan tanggal {{ $end->locale('id')->translatedFormat('d F Y') }} sampai pukul {{ data_get($hotspot,'pukul2','-') }}:</div>
  <table class="table">
    <thead><tr><th style="width:60px">No</th><th>Kecamatan</th><th class="nowrap">Jumlah Hotspot</th></tr></thead>
    <tbody>
      @foreach((array) data_get($hotspot,'data2',[]) as $i=>$row)
        <tr><td style="text-align:center">{{ $i+1 }}</td><td>{{ data_get($row,'kecamatan','-') }}</td><td style="text-align:center">{{ data_get($row,'jumlah','-') }}</td></tr>
      @endforeach
    </tbody>
  </table>

  <h4 class="mt-14 mb-6">Penyebaran Informasi Peringatan Dini</h4>
  <ul class="list small">
    <li>Menyebarkan & menggali informasi via frekuensi radio: {{ data_get($peringatan_dini,'radio','-') }}</li>
    <li>Menyebarkan & menggali informasi ke kecamatan via jejaring sosial & ponsel monitoring: {{ data_get($peringatan_dini,'jejaring','-') }}</li>
  </ul>

  <h4 class="mt-14 mb-6">Prakiraan Cuaca di Kabupaten Malang</h4>
  <table class="table">
    <tr><th style="width:220px">Periode</th><td>{{ data_get($prakiraan_cuaca,'rentang','-') }}</td></tr>
    <tr><th>Cuaca</th><td>{{ data_get($prakiraan_cuaca,'cuaca','-') }}</td></tr>
    <tr><th>Suhu</th><td>{{ data_get($prakiraan_cuaca,'suhu','-') }}</td></tr>
    <tr><th>Kelembaban</th><td>{{ data_get($prakiraan_cuaca,'kelembaban','-') }}</td></tr>
    <tr><th>Kecepatan Angin</th><td>{{ data_get($prakiraan_cuaca,'kecepatan','-') }}</td></tr>
    <tr><th>Arah Angin</th><td>{{ data_get($prakiraan_cuaca,'arah','-') }}</td></tr>
  </table>

  <h4 class="mt-14 mb-6">Prakiraan Tinggi Gelombang di Kabupaten Malang</h4>
  <table class="table">
    <thead>
      <tr>
        <th>Arah Angin</th>
        <th style="width:80px">Kec. Angin (kts)</th>
        <th>Cuaca</th>
        <th style="width:120px">Sig. (m)</th>
        <th style="width:120px">Max (m)</th>
      </tr>
    </thead>
    <tbody>
      @foreach((array) $gelombang as $g)
        <tr>
          <td>{{ data_get($g,'arah','-') }}</td>
          <td style="text-align:center">{{ data_get($g,'kts','-') }}</td>
          <td>{{ data_get($g,'cuaca','-') }}</td>
          <td style="text-align:center">{{ data_get($g,'sig','-') }}</td>
          <td style="text-align:center">{{ data_get($g,'max','-') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <h4 class="mt-14 mb-6">Komunikasi Radio (Pantauan Cuaca)</h4>
  <table class="table">
    <thead>
      <tr><th style="width:60px">No</th><th>Kecamatan</th><th>Pantauan Cuaca</th></tr>
    </thead>
    <tbody>
      @foreach((array) $radio as $i=>$row)
        <tr>
          <td style="text-align:center">{{ $i+1 }}</td>
          <td>{{ data_get($row,'kecamatan','-') }}</td>
          <td>{{ data_get($row,'pantauan','-') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="ttd-wrap mt-14">
    <div class="ttd-box">
      <div class="small">{!! nl2br(e(data_get($ttd,'manager.jabatan',''))) !!}</div>
      <div class="ttd-space"></div>
      <div><b>{{ data_get($ttd,'manager.nama','') }}</b></div>
      <div class="tiny">{{ data_get($ttd,'manager.pangkat','') }}</div>
      <div class="tiny">{{ data_get($ttd,'manager.nip','') }}</div>
    </div>
    <div class="ttd-box">
      <div class="small">{{ data_get($ttd,'tanggal_bawah','') }}</div>
      @foreach((array) data_get($ttd,'piket',[]) as $p)
        <div class="small mt-10">{{ data_get($p,'label','') }}</div>
        <div class="ttd-space"></div>
        <div><b>{{ data_get($p,'nama','') }}</b></div>
      @endforeach
    </div>
  </div>
</section>

{{-- ==================== DOKUMENTASI ==================== --}}
<section class="page">
  <h4>Dokumentasi Kejadian</h4>
  <div class="doc-grid mt-6">
    <div class="doc-ph">— foto —</div>
    <div class="doc-ph">— foto —</div>
    <div class="doc-ph">— foto —</div>
    <div class="doc-ph">— foto —</div>
  </div>

  <h4 class="mt-14">Dokumentasi Kegiatan</h4>
  <div class="doc-grid mt-6">
    <div class="doc-ph">— foto —</div>
    <div class="doc-ph">— foto —</div>
    <div class="doc-ph">— foto —</div>
    <div class="doc-ph">— foto —</div>
  </div>
</section>

</body>
</html>