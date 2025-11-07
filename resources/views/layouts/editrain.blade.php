<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Data Hujan</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f9f6f2; padding: 40px; }
    .form-card { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin:auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1);}
    h2 { margin-bottom: 20px; color: #122453; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display:block; margin-bottom: 6px; font-weight: 600; }
    .form-group input { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; }
    .actions { display:flex; justify-content:center; gap:10px; margin-top:20px; }
    .btn { padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:600; color:white; text-decoration:none; }
    .btn-save { background:#28D82B; }
    .btn-cancel { background:#E30707; }
  </style>
</head>
<body>

  <div class="form-card">
    <h2>Edit Data Hujan dan Tidak Hujan</h2>

    <form action="{{ route('rain.update', $rain->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="kecamatan">Kecamatan</label>
        <input type="text" id="kecamatan" name="kecamatan" value="{{ $rain->kecamatan }}" required>
      </div>

      <div class="form-group">
        <label for="hariHujan">Hari Hujan</label>
        <input type="number" id="hariHujan" name="hari_hujan" value="{{ $rain->hari_hujan }}" required>
      </div>

      <div class="form-group">
        <label for="hariTidakHujan">Hari Tidak Hujan</label>
        <input type="number" id="hariTidakHujan" name="hari_tidak_hujan" value="{{ $rain->hari_tidak_hujan }}" required>
      </div>

      <div class="actions">
        <button type="submit" class="btn btn-save">Simpan</button>
        <a href="{{ route('rain.index') }}" class="btn btn-cancel">Batal</a>
      </div>
    </form>
  </div>

</body>
</html>