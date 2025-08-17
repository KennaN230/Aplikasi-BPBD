<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Kejadian Kabupaten Malang</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('/gambar/bpbd 1.png') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            color: white;
            max-width: 1000px;
            width: 100%;
            height: 500px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .logo-container img {
            height: 130px;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 10px 0;
        }

        p {
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 250px;
        }

        .btn {
            background-color: #ff6600;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        
        .btn:hover {
            background-color: #e55a00;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .social-icons1 {
            margin-top: 90px; /* atur jarak sesuai kebutuhan */
            gap: 50px;
        }

        .social-icons2 {
            margin-top: 10px; /* atur jarak sesuai kebutuhan */
            gap: 10px;
        }

        .penjelasan h1 {
            font-size: 40px;
            gap: 50px;
            margin: 10px 0;
        }

        .penjelasan p {
            font-size: 22px;
            gap: 50px;
        }


    </style>
</head>
<body>
    <div class="overlay">
        <div class="logo-container">
            <img src="/gambar/Logo 1 1.png" alt="BPBD Kota Malang">
            <img src="/gambar/Logo_Kabupaten_Malang 1.png" alt="Kabupaten Malang">
        </div>
        <div class="penjelasan">
            <h1>INFORMASI KEJADIAN KABUPATEN MALANG</h1>
        <p>Pusat Pengendalian Operasi Penanggulangan Bencana<br>
        Badan Penanggulangan Bencana Daerah Kabupaten Malang</p>
        </div>

        <div class="button-container">
            <a href="/Admin" class="btn">ADMIN</a>
            <a href="/user" class="btn">USER</a>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
