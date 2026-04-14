<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --primary-light: #8B5CF6;
            --bg: #F8F9FD;
            --text: #0F172A;
            --muted: #64748B;
            --border: #E2E8F0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #0F0A2A; 
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(92, 59, 254, 0.15) 0%, transparent 30%),
                radial-gradient(circle at 100% 100%, rgba(139, 92, 246, 0.15) 0%, transparent 30%);
            color: var(--text); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            padding: 24px; 
            margin: 0;
        }
        
        .verify-card { 
            background: white; 
            width: 100%; 
            max-width: 480px; 
            border-radius: 40px; 
            padding: 56px 48px; 
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.5); 
            text-align: center;
            animation: slideUp 0.6s cubic-bezier(0.2, 0, 0, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #F4F2FF;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 32px;
        }

        h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 16px; color: var(--text); letter-spacing: -0.5px; }
        p { color: var(--muted); font-size: 15px; font-weight: 500; line-height: 1.6; margin-bottom: 32px; }

        .btn { 
            width: 100%; 
            padding: 18px; 
            border-radius: 18px; 
            border: none; 
            font-family: inherit; 
            font-weight: 800; 
            cursor: pointer; 
            font-size: 16px; 
            transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--primary), var(--primary-light)); 
            color: white; 
            box-shadow: 0 10px 25px rgba(92,59,254,0.3);
            margin-bottom: 16px;
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(92,59,254,0.4); }

        .btn-logout { background: #F8FAFC; color: var(--muted); border: 1px solid var(--border); }
        .btn-logout:hover { background: #F1F5F9; color: var(--text); }

        .alert-success { 
            background: #D1FAE5; 
            color: #065F46; 
            padding: 16px; 
            border-radius: 16px; 
            font-size: 14px; 
            font-weight: 600; 
            margin-bottom: 32px; 
            border: 1px solid #A7F3D0; 
        }

        @media (max-width: 480px) {
            .verify-card { padding: 48px 24px; }
            h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="verify-card">
    <div class="icon-circle">✉️</div>
    <h1>Verifikasi Email Anda</h1>
    
    @if (session('message'))
        <div class="alert-success">
            ✅ {{ session('message') }}
        </div>
    @else
        <p>
            Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan.
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary">
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-logout">
            Keluar (Logout)
        </button>
    </form>
</div>

</body>
</html>
