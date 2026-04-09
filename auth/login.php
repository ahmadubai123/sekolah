<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>HadirKu | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(120deg, #2ecc71, #27ae60, #1abc9c);
            background-size: 300% 300%;
            animation: bgMove 10s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        @keyframes bgMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ===== LOGO ATAS ===== */
        .logo-top {
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            width: 90px;
            z-index: 2;
            animation: fadeDown 1s ease;
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translate(-50%, -20px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }

        /* ===== TEXT SEKOLAH ===== */
        .school-name {
            position: absolute;
            bottom: 30px;
            width: 100%;
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.9);
            text-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 1;
        }

        /* ===== GAMBAR SISWA 3D KIRI ===== */
        .student-left,
        .student-right {
            position: absolute;
            bottom: 0;
            width: 420px;
            animation: floatStudent 6s ease-in-out infinite;
            z-index: 1;
            pointer-events: none;
        }

        .student-left {
            left: 6%;
        }

        .student-right {
            right: 6%;
            animation-delay: 1.5s;
        }

        @keyframes floatStudent {
            0% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
            100% { transform: translateY(0); }
        }

        /* ===== LOGIN CARD ===== */
        .container {
            position: relative;
            z-index: 2;
            background: #ffffff;
            width: 380px;
            padding: 40px 35px;
            border-radius: 22px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.25);
            animation: fadeUp 0.9s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container h2 {
            text-align: center;
            margin-bottom: 8px;
            font-size: 34px;
            color: #27ae60;
            font-weight: 600;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-bottom: 30px;
        }

        form input {
            width: 100%;
            padding: 14px 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        form button {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }

        .footer {
            margin-top: 22px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
        }

        /* ===== RESPONSIVE ===== */
        @media(max-width: 1000px){
            .student-left,
            .student-right {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- LOGO -->
<img src="../assets/img/logo.png" class="logo-top" alt="Logo Sekolah">

<!-- SISWA KIRI -->
<img src="../assets/img/student-3d.png" class="student-left" alt="Siswa 3D">

<!-- SISWA KANAN -->
<img src="../assets/img/student-pramuka-3d.png" class="student-right" alt="Siswa Pramuka 3D">

<!-- LOGIN -->
<div class="container">
    <h2>HadirKu</h2>
    <div class="subtitle">Aplikasi Absensi Digital</div>

    <form method="POST" action="login_process.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="footer">
        © <?= date('Y') ?> HadirKu
    </div>
</div>

<!-- NAMA SEKOLAH -->
<div class="school-name">MI PRIUK – CILEGON</div>

</body>
</html>
