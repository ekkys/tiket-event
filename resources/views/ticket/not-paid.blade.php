<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Belum Dibayar - {{ $ticket->registration->event->name }}</title>
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
            background: linear-gradient(135deg, #5C3BFE, #8B5CF6);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">💳</div>
    <h1>Pembayaran Belum Diterima</h1>
    <p>
        Tiket ini baru dapat diakses setelah pembayaran untuk pendaftaran <strong>{{ $ticket->registration->registration_code }}</strong> berhasil diverifikasi.
        <br><br>
        Silakan selesaikan pembayaran Anda atau cek status pendaftaran.
    </p>
    <a href="{{ route('payment.show', $ticket->registration->registration_code) }}" class="btn">Bayar Sekarang</a>
</div>
</body>
</html>
