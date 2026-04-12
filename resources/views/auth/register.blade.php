{{-- FILE: resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penyelenggara - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --bg: #F8F9FD;
            --text: #1A1033;
            --muted: #7B7A8E;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0F0A2A;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            background: white;
            padding: 48px;
            border-radius: 32px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .logo { font-size: 2.5rem; text-align: center; margin-bottom: 24px; }
        h1 { font-size: 1.75rem; font-weight: 800; text-align: center; margin-bottom: 8px; color: var(--text); }
        .sub { text-align: center; color: var(--muted); margin-bottom: 40px; font-size: 14px; }
        
        .form-group { margin-bottom: 24px; }
        label { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            font-family: inherit;
            font-size: 14px;
            transition: 0.2s;
            outline: none;
        }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(92, 59, 254, 0.1); }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 14px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 12px;
        }
        .btn-login:hover { background: #4B2EE0; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(92, 59, 254, 0.3); }

        .error-msg { color: #DC2626; font-size: 12px; margin-top: 4px; font-weight: 600; }
        
        .footer-links { text-align: center; margin-top: 32px; font-size: 14px; color: var(--muted); }
        .footer-links a { color: var(--primary); text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">🎟️</div>
    <h1>Buat Akun</h1>
    <p class="sub">Daftar sebagai penyelenggara event baru</p>

    <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus>
            @error('name')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
            @error('email')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Min. 8 karakter" required>
            @error('password')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="btn-login">Daftar Sekarang</button>
    </form>

    <div class="footer-links">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
    </div>
</div>

</body>
</html>
