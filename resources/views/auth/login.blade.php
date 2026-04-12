<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --bg: #F8F9FD;
            --text: #1A1033;
            --muted: #7B7A8E;
            --border: #E2E8F0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        
        .login-card { background: white; width: 100%; max-width: 440px; border-radius: 32px; padding: 48px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: 1px solid #E2E8F0; }
        .logo { font-weight: 800; font-size: 1.75rem; color: var(--primary); margin-bottom: 32px; text-align: center; }
        .header { text-align: center; margin-bottom: 32px; }
        .header h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 8px; }
        .header p { color: var(--muted); font-size: 14px; font-weight: 500; }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
        input { width: 100%; padding: 14px 18px; border-radius: 14px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; outline: none; transition: 0.2s; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(92, 59, 254, 0.1); }

        .btn { width: 100%; padding: 14px; border-radius: 14px; border: none; font-family: inherit; font-weight: 700; cursor: pointer; font-size: 15px; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; margin-top: 12px; }
        .btn-primary:hover { background: #4B2EE0; transform: translateY(-1px); }

        .error { color: #DC2626; font-size: 12px; font-weight: 600; margin-top: 4px; }
        .alert { background: #FEE2E2; color: #B91C1C; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 24px; text-align: center; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">🎟️ TiketEvent</div>
    <div class="header">
        <h1>Selamat Datang</h1>
        <p>Silakan login untuk mengelola event Anda</p>
    </div>

    @if($errors->any())
        <div class="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@gmail.com" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 0; cursor: pointer;">
                <input type="checkbox" name="remember" style="width: auto;"> <span style="font-size: 13px; font-weight: 600; color: var(--muted);">Ingat Saya</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Masuk ke Dashboard</button>
    </form>
    <div style="text-align: center; margin-top: 32px; font-size: 14px; color: #7B7A8E;">
        Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; font-weight: 700;">Daftar Sekarang</a>
    </div>
</div>

</body>
</html>
