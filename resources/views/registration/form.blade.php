{{-- FILE: resources/views/registration/form.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar - {{ $event->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A;
            --primary-dark: #0F172A;
            --success: #10B981;
            --error: #EF4444;
            --bg: #F8FAFC;
            --card: #FFFFFF;
            --text: #0F172A;
            --muted: #64748B;
            --border: #E2E8F0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, #0F172A 100%);
            color: white;
            padding: 60px 24px 100px;
            text-align: center;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(2rem, 6vw, 3rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1px;
        }

        .hero p {
            margin-top: 16px;
            opacity: 0.9;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .quota-bar {
            max-width: 480px;
            margin: 24px auto 0;
        }

        .quota-track {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 100px;
            height: 10px;
            overflow: hidden;
        }

        .quota-fill {
            height: 100%;
            background: #0cafccff;
            border-radius: 100px;
            width:
                {{ ($event->registered_count / $event->quota) * 100 }}
                %;
            transition: width 1s cubic-bezier(0.1, 0.7, 0.1, 1);
        }

        .quota-label {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            opacity: 0.9;
            margin-top: 8px;
        }

        .container {
            max-width: 640px;
            margin: -60px auto 48px;
            padding: 0 16px;
        }

        .card {
            background: var(--card);
            border-radius: 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            padding: 48px;
            border: 1px solid #F1F5F9;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 32px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }

        label .req {
            color: var(--error);
            margin-left: 2px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--border);
            border-radius: 16px;
            font-family: inherit;
            font-size: 16px;
            color: var(--text);
            background: #F9F8FF;
            transition: all 0.2s;
            outline: none;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 5px rgba(92, 59, 254, 0.1);
        }

        input.error,
        select.error {
            border-color: var(--error);
            background: #FFF9F9;
        }

        .error-msg {
            font-size: 13px;
            color: var(--error);
            margin-top: 6px;
            font-weight: 600;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .price-box {
            background: #F8FAFC;
            border: 2px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .price-label {
            font-size: 14px;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .price-amount {
            font-size: 2rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -1px;
        }

        .price-free {
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--success);
            letter-spacing: -1px;
        }

        .btn-submit {
            width: 100%;
            padding: 20px;
            background: linear-gradient(45deg, var(--primary), #3B82F6);
            color: white;
            border: none;
            border-radius: 20px;
            font-family: inherit;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(30, 58, 138, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .btn-submit:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(30, 58, 138, 0.4);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 640px) {
            .hero {
                padding: 48px 20px 80px;
            }

            .container {
                margin-top: -50px;
            }

            .card {
                padding: 32px 24px;
                border-radius: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .price-amount {
                font-size: 1.75rem;
            }

            .btn-submit {
                font-size: 16px;
                padding: 18px;
            }
        }

        .back-link {
            text-decoration: none;
            color: var(--primary);
            font-size: 14px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 24px;
            transition: opacity 0.2s;
        }

        .back-link:hover {
            opacity: 0.8;
        }

        .info-note {
            font-size: 13px;
            color: var(--muted);
            text-align: center;
            margin-top: 24px;
            line-height: 1.6;
            font-weight: 500;
        }

        .alert-error {
            background: #FFF0F0;
            border: 2px solid #FFDCDC;
            border-radius: 16px;
            padding: 16px;
            color: var(--error);
            font-size: 14px;
            margin-bottom: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>

<body>

    <div class="hero">
        <div class="hero-badge">🎉 Pendaftaran Terbuka</div>
        <h1>{{ $event->name }}</h1>
        <p>
            📅 {{ $event->event_date->isoFormat('dddd, D MMMM Y') }}
            @if($event->location) &nbsp;·&nbsp; 📍 {{ $event->location }}
            @endif
        </p>
        <div class="quota-bar">
            <div class="quota-track">
                <div class="quota-fill"></div>
            </div>
            <div class="quota-label">
                <span>{{ number_format($event->registered_count) }} terdaftar</span>
                <span>Sisa {{ number_format($event->getRemainingQuota()) }} slot</span>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <a href="{{ route('home') }}" class="back-link">
                ← Kembali ke Daftar Event
            </a>

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
                    <div style="font-size: 2.5rem;">🎟️</div>
                </div>

                <div class="form-group">
                    <label>Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}"
                        placeholder="Masukkan nama lengkap Anda" class="{{ $errors->has('full_name') ? 'error' : '' }}"
                        required>
                    @error('full_name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span class="req">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com"
                            class="{{ $errors->has('email') ? 'error' : '' }}" required>
                        @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>No. WhatsApp <span class="req">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                            class="{{ $errors->has('phone') ? 'error' : '' }}" required>
                        @error('phone')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>NIK / No. KTP</label>
                        <input type="text" name="id_number" id="nikInput" value="{{ old('id_number') }}"
                            placeholder="16 digit NIK (opsional)" maxlength="16">
                        <div id="nikFeedback" style="font-size: 11px; margin-top: 6px; font-weight: 700;"></div>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="gender">
                            <option value="">-- Pilih --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
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

                @if($event->terms_and_conditions)
                    <div class="form-group"
                        style="background: #F8FAFC; padding: 20px; border-radius: 16px; border: 1px solid var(--border); margin-bottom: 24px;">
                        <label
                            style="color: var(--primary); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">📜
                            Syarat & Ketentuan</label>
                        <div
                            style="font-size: 12px; max-height: 120px; overflow-y: auto; color: var(--muted); line-height: 1.6; white-space: pre-line; padding-right: 8px;">
                            {{ $event->terms_and_conditions }}
                        </div>
                    </div>
                @endif

                <div class="form-group" style="margin-bottom: 32px;">
                    <label
                        style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; font-weight: 600; line-height: 1.5; color: var(--muted);">
                        <input type="checkbox" name="terms_accepted" value="1" required
                            style="width: 22px; height: 22px; margin-top: 2px; accent-color: var(--primary);">
                        <span>Saya setuju dengan <strong style="color: var(--text);">Syarat & Ketentuan</strong> yang
                            berlaku untuk event ini. <span class="req">*</span></span>
                    </label>
                    @error('terms_accepted')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span id="btnText">
                        @if($event->is_free) 🎁 Daftar Sekarang @else 💳 Lanjut Pembayaran @endif
                    </span>
                </button>

                <p class="info-note">
                    🔒 Keamanan Data Terjamin<br>
                    Tiket resmi akan dikirim ke email segera setelah pendaftaran.
                </p>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('regForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            const txt = document.getElementById('btnText');
            btn.disabled = true;
            txt.textContent = '⏳ Memproses...';
        });

        // Real-time NIK validation
        const nikInput = document.getElementById('nikInput');
        const nikFeedback = document.getElementById('nikFeedback');

        nikInput.addEventListener('input', function () {
            const val = this.value;
            if (val.length === 0) {
                nikFeedback.textContent = '';
            } else if (val.length < 16) {
                nikFeedback.textContent = '❌ Kurang dari 16 digit';
                nikFeedback.style.color = '#EF4444';
            } else {
                nikFeedback.textContent = '✅ Format Sesuai (16 digit)';
                nikFeedback.style.color = '#10B981';
            }

            // Hanya angka
            this.value = val.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>