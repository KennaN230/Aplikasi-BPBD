<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{background:#224597;display:flex;justify-content:center;align-items:center;min-height:100vh}
    .card{background:#0B1C3F;color:#fff;width:360px;padding:28px 32px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    h2{font-weight:600;text-align:center;margin-bottom:18px}
    .alert{padding:10px;border-radius:6px;text-align:center;margin-bottom:12px;font-size:14px}
    .alert-success{background:#22c55e}
    .alert-error{background:#ef4444}
    label{display:block;margin:8px 0 6px;font-weight:600;font-size:14px}
    input{width:100%;padding:10px 12px;border:0;border-radius:8px;background:#f8f9fa;color:#111;font-size:13px}
    .mt{margin-top:12px}
    .btn{width:100%;padding:10px;border:0;border-radius:8px;background:#F46A1F;color:#fff;font-weight:700;cursor:pointer}
    .btn:hover{background:#d85a18}
    .back{text-align:center;margin-top:14px}
    .back a{color:#B0C4DE;text-decoration:none;font-size:12px}
    .back a:hover{text-decoration:underline}
  </style>
</head>
<body>
  <div class="card">
    <h2>Reset Password</h2>

    {{-- pesan sukses --}}
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- pesan error validasi --}}
    @if ($errors->any())
      <div class="alert alert-error">
        <ul style="list-style:none;padding:0;margin:0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      {{-- token + email HARUS dikirim ke broker --}}
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ old('email', request('email')) }}">

      <label for="password">Password Baru</label>
      <input id="password" name="password" type="password" required autofocus>

      <label for="password_confirmation" class="mt">Konfirmasi Password</label>
      <input id="password_confirmation" name="password_confirmation" type="password" required>

      <button type="submit" class="btn mt">SIMPAN PASSWORD</button>
    </form>

    <div class="back">
      <a href="{{ route('login') }}">Kembali ke Login</a>
    </div>
  </div>
</body>
</html>