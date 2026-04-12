<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuota Penuh - {{ $event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F4F2FF;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(92,59,254,0.1);
        }
        .icon { font-size: 4rem; margin-bottom: 24px; }
        h1 { font-size: 1.5rem; color: #1A1033; margin-bottom: 12px; }
        p { color: #7B7A8E; line-height: 1.6; margin-bottom: 32px; }
        .btn {
            display: inline-block;
            background: #F4F2FF;
            color: #5C3BFE;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">🚫</div>
    <h1>Kuota Sudah Penuh</h1>
    <p>
        Mohon maaf, kuota pendaftaran untuk <strong>{{ $event->name }}</strong> sudah terpenuhi.
        <br><br>
        Terima kasih atas antusiasme Anda. Sampai jumpa di event berikutnya!
    </p>
    <a href="/" class="btn">Kembali ke Beranda</a>
</div>
</body>
</html>
