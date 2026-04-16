<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --primary-light: #8B5CF6;
            --bg: #F8FAFC;
            --text: #0F172A;
            --muted: #64748B;
            --border: #E2E8F0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #0F0A2A; 
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(92, 59, 254, 0.15) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 20%);
            color: var(--text); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            padding: 24px; 
            margin: 0;
        }
        
        .auth-card { 
            background: white; 
            width: 100%; 
            max-width: 480px; 
            border-radius: 40px; 
            padding: 56px 48px; 
            box-shadow: 0 40px 120px -20px rgba(0,0,0,0.8); 
            animation: slideUp 0.6s cubic-bezier(0.2, 0, 0, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo { font-weight: 800; font-size: 2rem; color: var(--primary); margin-bottom: 40px; text-align: center; letter-spacing: -1px; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 12px; color: var(--text); letter-spacing: -0.5px; }
        .header p { color: var(--muted); font-size: 15px; font-weight: 500; line-height: 1.6; }

        .form-group { margin-bottom: 24px; }
        label { display: block; font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 10px; }
        input { 
            width: 100%; 
            padding: 16px 20px; 
            border-radius: 16px; 
            border: 1px solid var(--border); 
            font-family: inherit; 
            font-size: 15px; 
            outline: none; 
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            background: #F8FAFC;
        }
        input:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(92, 59, 254, 0.1); }

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
            margin-top: 16px; 
            box-shadow: 0 10px 25px rgba(92,59,254,0.3);
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(92,59,254,0.4); }

        .alert-success { background: #D1FAE5; color: #065F46; padding: 16px; border-radius: 16px; font-size: 14px; font-weight: 600; margin-bottom: 32px; text-align: center; border: 1px solid #A7F3D0; }
        .alert-error { background: #FEE2E2; color: #B91C1C; padding: 16px; border-radius: 16px; font-size: 14px; font-weight: 600; margin-bottom: 32px; text-align: center; border: 1px solid #FECACA; }
        
        .footer-note { text-align: center; margin-top: 40px; font-size: 15px; color: var(--muted); font-weight: 500; }
        .footer-note a { color: var(--primary); text-decoration: none; font-weight: 800; }

        @media (max-width: 480px) {
            .auth-card { padding: 48px 24px; }
            .header h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="logo">🎟️ {{ config('app.name') }}</div>
    <div class="header">
        <h1>Lupa Password?</h1>
        <p>Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan tautan pemulihan.</p>
    </div>

    @if (session('status'))
        <div class="alert-success">
            ✅ {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@gmail.com" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary">Kirim Link Pemulihan</button>
    </form>

    <div class="footer-note">
        Ingat password Anda? <a href="{{ route('login') }}">Masuk Kembali</a>
    </div>
</div>

</body>
</html>
