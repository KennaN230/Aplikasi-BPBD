<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPBD Kabupaten Malang</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #243F86; /* Biru tua */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #0B1C3F; /* Biru gelap */
            padding: 80px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .logo-container img {
            height: 80px;
        }
        h1 {
            font-size: 18px;
            color: white;
            margin-bottom: 30px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #FF6600; /* Oranye */
            color: white;
            border: none;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #e55a00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="/gambar/Logo 1 1.png" alt="BPBD Logo">
            <img src="/gambar/Logo_Kabupaten_Malang 1.png" alt="Kabupaten Malang Logo">
        </div>
        <h1>Selamat Datang Di Pusat Informasi<br>Kejadian Kabupaten Malang</h1>
        <a href="/login" class="btn">LOGIN</a>
        <a href="/register" class="btn">REGISTER</a>
    </div>
</body>
</html>
