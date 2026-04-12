{{-- FILE: resources/views/registration/form.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - {{ $event->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --primary-dark: #3E1FE0;
            --success: #00C48C;
            --error: #FF4757;
            --bg: #F4F2FF;
            --card: #FFFFFF;
            --text: #1A1033;
            --muted: #7B7A8E;
            --border: #E2DEF9;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, #8B5CF6 100%);
            color: white;
            padding: 48px 24px 80px;
            text-align: center;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .hero h1 { font-size: clamp(1.8rem, 5vw, 2.8rem); font-weight: 800; line-height: 1.2; }
        .hero p  { margin-top: 12px; opacity: 0.85; font-size: 1rem; }

        .quota-bar {
            max-width: 480px;
            margin: 20px auto 0;
        }

        .quota-track {
            background: rgba(255,255,255,0.2);
            border-radius: 100px;
            height: 8px;
            overflow: hidden;
        }

        .quota-fill {
            height: 100%;
            background: #FFD60A;
            border-radius: 100px;
            width: {{ ($event->registered_count / $event->quota) * 100 }}%;
            transition: width 1s ease;
        }

        .quota-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            opacity: 0.8;
            margin-top: 6px;
        }

        .container {
            max-width: 600px;
            margin: -40px auto 48px;
            padding: 0 16px;
        }

        .card {
            background: var(--card);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(92,59,254,0.12);
            padding: 36px 32px;
        }

        @media (max-width: 480px) {
            .card { padding: 24px 20px; }
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        label .req { color: var(--error); margin-left: 2px; }

        input, select, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            color: var(--text);
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(92,59,254,0.1);
        }

        input.error, select.error { border-color: var(--error); }

        .error-msg {
            font-size: 12px;
            color: var(--error);
            margin-top: 4px;
            font-weight: 500;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }

        .price-box {
            background: linear-gradient(135deg, #F4F2FF, #EDE9FF);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .price-label { font-size: 13px; color: var(--muted); font-weight: 600; }
        .price-amount { font-size: 1.6rem; font-weight: 800; color: var(--primary); }
        .price-free { font-size: 1.4rem; font-weight: 800; color: var(--success); }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            color: white;
            border: none;
            border-radius: 14px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 8px 24px rgba(92,59,254,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(92,59,254,0.45); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .info-note {
            font-size: 12px;
            color: var(--muted);
            text-align: center;
            margin-top: 16px;
            line-height: 1.6;
        }

        .alert-error {
            background: #FFF0F0;
            border: 1px solid #FFD4D4;
            border-radius: 12px;
            padding: 12px 16px;
            color: var(--error);
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="hero">
    <div class="hero-badge">🎉 Pendaftaran Terbuka</div>
    <h1>{{ $event->name }}</h1>
    <p>
        📅 {{ $event->event_date->isoFormat('dddd, D MMMM Y') }}
        @if($event->location) &nbsp;·&nbsp; 📍 {{ $event->location }} @endif
    </p>
    <div class="quota-bar">
        <div class="quota-track"><div class="quota-fill"></div></div>
        <div class="quota-label">
            <span>{{ number_format($event->registered_count) }} terdaftar</span>
            <span>Sisa {{ number_format($event->getRemainingQuota()) }} slot</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="card">
        <div style="margin-bottom: 24px;">
            <a href="{{ route('home') }}" style="color: var(--primary); font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                ⬅️ Kembali ke Daftar Event
            </a>
        </div>

        <div class="card-title">📝 Form Pendaftaran</div>

        @if($errors->has('rate'))
            <div class="alert-error">⚠️ {{ $errors->first('rate') }}</div>
        @endif

        <form method="POST" action="{{ route('registration.store') }}" id="regForm">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="price-box">
                <div>
                    <div class="price-label">Harga Tiket</div>
                    @if($event->is_free)
                        <div class="price-free">GRATIS 🎁</div>
                    @else
                        <div class="price-amount">Rp {{ number_format($event->price, 0, ',', '.') }}</div>
                    @endif
                </div>
                <div style="font-size: 2rem;">🎟️</div>
            </div>

            <div class="form-group">
                <label>Nama Lengkap <span class="req">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}"
                    placeholder="Masukkan nama lengkap Anda"
                    class="{{ $errors->has('full_name') ? 'error' : '' }}" required>
                @error('full_name')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="req">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="email@contoh.com"
                        class="{{ $errors->has('email') ? 'error' : '' }}" required>
                    @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>No. WhatsApp <span class="req">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                        placeholder="08xxxxxxxxxx"
                        class="{{ $errors->has('phone') ? 'error' : '' }}" required>
                    @error('phone')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>NIK / No. KTP</label>
                    <input type="text" name="id_number" id="nikInput" value="{{ old('id_number') }}"
                        placeholder="16 digit NIK (opsional)" maxlength="16">
                    <div id="nikFeedback" style="font-size: 11px; margin-top: 4px; font-weight: 600;"></div>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="gender">
                        <option value="">-- Pilih --</option>
                        <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Asal Instansi / Sekolah / Kampus</label>
                <input type="text" name="institution" value="{{ old('institution') }}"
                    placeholder="Contoh: Universitas Airlangga, SMAN 1 Surabaya, dll">
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" rows="2"
                    placeholder="Alamat lengkap (opsional)">{{ old('address') }}</textarea>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span id="btnText">
                    @if($event->is_free) 🎁 Daftar Gratis @else 💳 Lanjut ke Pembayaran @endif
                </span>
            </button>

            <p class="info-note">
                🔒 Data Anda aman dan tidak akan disebarkan.<br>
                Tiket akan dikirim ke email setelah pendaftaran berhasil.
            </p>
        </form>
    </div>
</div>

<script>
document.getElementById('regForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const txt = document.getElementById('btnText');
    btn.disabled = true;
    txt.textContent = '⏳ Memproses...';
});

// Real-time NIK validation
const nikInput = document.getElementById('nikInput');
const nikFeedback = document.getElementById('nikFeedback');

nikInput.addEventListener('input', function() {
    const val = this.value;
    if (val.length === 0) {
        nikFeedback.textContent = '';
    } else if (val.length < 16) {
        nikFeedback.textContent = '❌ kurang dari 16 digit';
        nikFeedback.style.color = '#EF4444';
    } else {
        nikFeedback.textContent = '✅ sesuai 16 digit';
        nikFeedback.style.color = '#10B981';
    }
    
    // Hanya angka
    this.value = val.replace(/[^0-9]/g, '');
});
</script>
</body>
</html>
