{{-- resources/views/laporan-harian-eoc.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $judul }}</title>
  <style>
    @page { margin: 24mm 16mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color:#111; }
    .h1 { font-size: 18px; font-weight: 700; text-align:center; margin:0 0 6px }
    .muted { color:#666; font-size: 11px; text-align:center; margin-bottom: 14px }
    .meta { margin: 10px 0 14px; }
    .meta td { padding:4px 6px; font-size:12px }
    .table { width:100%; border-collapse: collapse; }
    .table th, .table td { border:1px solid #444; padding:6px 8px; vertical-align: top }
    .table th { background:#f3f3f3; text-align: left }
    .right { text-align:right }
    .center { text-align:center }
    .ttd { margin-top: 26px; width:100% }
    .ttd td { vertical-align: bottom; height: 80px }
  </style>
</head>
<body>
  <div class="h1">{{ $judul }}</div>
  <div class="muted">Periode: {{ $tanggal_label }}</div>

  <table class="meta">
    <tr>
      <td>Penyusun</td><td>: {{ $penyusun }}</td>
    </tr>
    <tr>
      <td>Tanggal Cetak</td><td>: {{ now()->format('d/m/Y H:i') }} WIB</td>
    </tr>
  </table>

  <table class="table">
    <thead>
      <tr>
        <th class="center" style="width:80px">Waktu</th>
        <th>Kejadian/Informasi</th>
        <th style="width:160px">Lokasi</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
    @forelse($items as $row)
      <tr>
        <td class="center">{{ $row['waktu'] }}</td>
        <td>{{ $row['kejadian'] }}</td>
        <td>{{ $row['lokasi'] }}</td>
        <td>{{ $row['catatan'] }}</td>
      </tr>
    @empty
      <tr><td colspan="4" class="center" style="color:#777">Tidak ada data pada periode ini.</td></tr>
    @endforelse
    </tbody>
  </table>

  <table class="ttd">
    <tr>
      <td></td>
      <td class="right">Mengetahui,<br>Kepala Pelaksana</td>
    </tr>
    <tr>
      <td></td>
      <td class="right" style="font-weight:700; padding-top:10px">_____________________</td>
    </tr>
  </table>
</body>
</html>