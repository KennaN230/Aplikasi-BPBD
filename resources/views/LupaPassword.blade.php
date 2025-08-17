<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
      font-family: 'Poppins', sans-serif; 
    }
    body {
      background-color: #224597;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .forgot-password-container {
      background-color: #0B1C3F;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      width: 350px;
      color: white;
    }
    .forgot-password-container h2 { 
      text-align: center; 
      margin-bottom: 20px; 
      font-weight: 600; 
    }
    .alert { 
      padding: 10px; 
      border-radius: 5px; 
      text-align: center; 
      margin-bottom: 15px; 
      font-size: 14px; 
    }
    .alert-success { 
      background-color: #22c55e; 
      color: white; 
    }
    .alert-error { 
      background-color: #ef4444; 
      color: white; 
    }
    label { 
      font-weight: 600; 
      font-size: 14px; 
      margin-bottom: 5px; 
      display: block; 
    }
    input { 
      width: 100%; 
      padding: 10px 12px; 
      border: none; 
      border-radius: 8px; 
      outline: none; 
      font-size: 12px; 
      background-color: #f8f9fa;
      color: #212529;
    }
    .input-group { 
      position: relative; 
      margin-bottom: 15px; 
    }
    .submit-btn { 
      background-color: #F46A1F; 
      color: white; 
      border: none; 
      width: 100%; 
      padding: 10px; 
      border-radius: 6px; 
      font-weight: bold; 
      cursor: pointer; 
      font-size: 14px; 
      transition: background 0.3s ease; 
    }
    .submit-btn:hover { 
      background-color: #d85a18; 
    }
    .back-to-login { 
      text-align: center; 
      margin-top: 20px; 
    }
    .back-to-login a { 
      font-size: 12px; 
      color: #B0C4DE; 
      text-decoration: none; 
    }
    .back-to-login a:hover { 
      text-decoration: underline; 
    }
  </style>
</head>
<body>

  <div class="forgot-password-container">
    <h2>Lupa Password</h2>

    {{-- Pesan status --}}
    @if(session('status'))
      <div class="alert alert-success">
        {{ session('status') }}
      </div>
    @endif

    {{-- Pesan error --}}
    @if($errors->any())
      <div class="alert alert-error">
        <ul style="list-style: none; padding: 0; margin: 0;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
      @csrf
      <label for="email">Email</label>
      <div class="input-group">
        <input type="email" name="email" id="email" placeholder="Masukkan email Anda" required>
      </div>

      <button type="submit" class="submit-btn">KIRIM LINK RESET</button>
    </form>

    <div class="back-to-login">
      <a href="{{ route('login') }}">Kembali ke Login</a>
    </div>
  </div>

</body>
</html>