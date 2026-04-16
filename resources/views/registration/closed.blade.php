<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ditutup - {{ $event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .card {
            background: white;
            border-radius: 32px;
            padding: 48px 40px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            border: 1px solid #F1F5F9;
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 24px;
        }

        h1 {
            font-size: 1.5rem;
            color: #0F172A;
            margin-bottom: 12px;
            font-weight: 800;
        }

        p {
            color: #64748B;
            line-height: 1.6;
            margin-bottom: 32px;
            font-weight: 500;
        }

        .btn {
            display: inline-block;
            background: #F1F5F9;
            color: #1E3A8A;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 16px;
            font-weight: 800;
            transition: 0.2s;
        }

        .btn:hover {
            background: #E2E8F0;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">🔒</div>
        <h1>Pendaftaran Ditutup</h1>
        <p>
            Mohon maaf, pendaftaran untuk <strong>{{ $event->name }}</strong> saat ini sudah ditutup.
            <br><br>
            Silakan pantau kembali informasi terbaru dari kami.
        </p>
        <a href="/" class="btn">Kembali ke Beranda</a>
    </div>
</body>

</html>