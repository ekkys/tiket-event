<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ config('app.name') }}</title>
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
        
        .login-card { 
            background: white; 
            width: 100%; 
            max-width: 460px; 
            border-radius: 40px; 
            padding: 56px 48px; 
            box-shadow: 0 40px 120px -20px rgba(0,0,0,0.8); 
            border: 1px solid rgba(255,255,255,0.1); 
            animation: slideUp 0.6s cubic-bezier(0.2, 0, 0, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo { font-weight: 800; font-size: 2rem; color: var(--primary); margin-bottom: 40px; text-align: center; letter-spacing: -1px; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 12px; color: var(--text); letter-spacing: -0.5px; }
        .header p { color: var(--muted); font-size: 15px; font-weight: 500; }

        .form-group { margin-bottom: 24px; }
        label { display: block; font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 10px; }
        input[type="email"], input[type="password"], input[type="text"] { 
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
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--primary), var(--primary-light)); 
            color: white; 
            margin-top: 16px; 
            box-shadow: 0 10px 25px rgba(92, 59, 254, 0.3);
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(92, 59, 254, 0.4); }

        .alert { background: #FEE2E2; color: #B91C1C; padding: 16px; border-radius: 16px; font-size: 14px; font-weight: 600; margin-bottom: 32px; text-align: center; border: 1px solid #FECACA; }
        
        .footer-note { text-align: center; margin-top: 40px; font-size: 15px; color: var(--muted); font-weight: 500; }
        .footer-note a { color: var(--primary); text-decoration: none; font-weight: 800; }
        .footer-note a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .login-card { padding: 48px 24px; }
            .header h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">🎟️ {{ config('app.name') }}</div>
    <div class="header">
        <h1>Masuk Akun</h1>
        <p>Kelola event dan pantau tiket Anda</p>
    </div>

    @if($errors->any())
        <div class="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: admin@gmail.com" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <div style="position: relative;">
                <input type="password" name="password" id="password" placeholder="••••••••" style="padding-right: 50px;" required>
                <button type="button" onclick="togglePassword('password', 'toggleIcon')" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted); padding: 0; display: flex; align-items: center;">
                    <svg id="toggleIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px;">
            <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 0; cursor: pointer;">
                <input type="checkbox" name="remember" style="width: 18px; height: 18px; accent-color: var(--primary);"> 
                <span style="font-size: 14px; font-weight: 600; color: var(--muted);">Ingat Saya</span>
            </label>
            <a href="{{ route('password.request') }}" style="font-size: 14px; font-weight: 700; color: var(--primary); text-decoration: none;">Lupa Password?</a>
        </div>

        <button type="submit" class="btn btn-primary">Masuk ke Dashboard</button>
    </form>

    <div class="footer-note">
        Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}
</script>
</body>
</html>
