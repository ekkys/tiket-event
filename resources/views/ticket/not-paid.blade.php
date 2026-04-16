<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Belum Dibayar - {{ $ticket->registration->event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            border: 1px solid #F1F5F9;
        }
        .icon { font-size: 4rem; margin-bottom: 24px; }
        h1 { font-size: 1.5rem; color: #0F172A; margin-bottom: 12px; font-weight: 800; }
        p { color: #64748B; line-height: 1.6; margin-bottom: 32px; font-weight: 500; }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #1E3A8A, #3B82F6);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 16px;
            font-weight: 800;
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.2);
            transition: 0.3s;
        }
        .btn:hover { 
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(30, 58, 138, 0.3);
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
