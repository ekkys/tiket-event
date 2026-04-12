{{-- FILE: resources/views/payment/show.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - {{ $registration->event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    {{-- Production: https://app.midtrans.com/snap/snap.js --}}
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0F0A2A 0%, #1a0f40 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 36px 32px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
        }

        .logo { font-size: 2rem; text-align: center; margin-bottom: 8px; }
        h1 { text-align: center; font-size: 1.25rem; font-weight: 800; color: #1A1033; }
        .sub { text-align: center; color: #7B7A8E; font-size: 14px; margin-top: 4px; }

        .divider { height: 1px; background: #F0EEFF; margin: 24px 0; }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .info-key { font-size: 13px; color: #7B7A8E; font-weight: 500; }
        .info-val { font-size: 14px; font-weight: 700; color: #1A1033; text-align: right; max-width: 60%; }

        .price-big {
            background: linear-gradient(135deg, #F4F2FF, #EDE9FF);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .price-label { font-size: 12px; color: #7B7A8E; font-weight: 600; margin-bottom: 4px; }
        .price-amount { font-size: 2rem; font-weight: 800; color: #5C3BFE; }

        .qris-hint {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #166534;
            font-weight: 600;
        }

        .btn-pay {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #5C3BFE, #8B5CF6);
            color: white;
            border: none;
            border-radius: 14px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(92,59,254,0.35);
        }

        .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; }

        .status-msg {
            text-align: center;
            font-size: 13px;
            color: #7B7A8E;
            margin-top: 12px;
        }

        .spinner {
            display: none;
            width: 20px; height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .timer-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            color: #7B7A8E;
            margin-top: 8px;
        }

        .timer { font-weight: 800; color: #FF4757; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">💳</div>
    <h1>Selesaikan Pembayaran</h1>
    <p class="sub">{{ $registration->event->name }}</p>

    <div class="divider"></div>

    <div class="info-row">
        <span class="info-key">Nama</span>
        <span class="info-val">{{ $registration->full_name }}</span>
    </div>
    <div class="info-row">
        <span class="info-key">Email</span>
        <span class="info-val">{{ $registration->email }}</span>
    </div>
    <div class="info-row">
        <span class="info-key">No. Daftar</span>
        <span class="info-val">{{ $registration->registration_code }}</span>
    </div>

    <div class="price-big">
        <div class="price-label">Total Pembayaran</div>
        <div class="price-amount">Rp {{ number_format($registration->event->price, 0, ',', '.') }}</div>
    </div>

    <div class="qris-hint">
        <span style="font-size:1.4rem">📱</span>
        <span>Tersedia pembayaran via QRIS, GoPay, Dana, dan Transfer Bank</span>
    </div>

    <button class="btn-pay" id="payBtn" onclick="doPay()">
        🔒 Bayar Sekarang
    </button>

    <div class="spinner" id="spinner"></div>

    <div class="status-msg" id="statusMsg">
        Pembayaran aman diproses oleh Midtrans
    </div>

    <div class="timer-box">
        ⏱ Batas waktu pembayaran: <span class="timer" id="timer">02:00:00</span>
    </div>
</div>

<script>
const snapToken   = '{{ $snapToken }}';
const statusUrl   = '{{ route("payment.status", $registration->registration_code) }}';

function doPay() {
    snap.pay(snapToken, {
        onSuccess: function(result) {
            document.getElementById('statusMsg').textContent = '✅ Pembayaran berhasil! Mengarahkan...';
            pollStatus();
        },
        onPending: function(result) {
            document.getElementById('statusMsg').textContent = '⏳ Menunggu konfirmasi pembayaran...';
            pollStatus();
        },
        onError: function(result) {
            document.getElementById('statusMsg').textContent = '❌ Pembayaran gagal. Silakan coba lagi.';
        },
        onClose: function() {
            document.getElementById('statusMsg').textContent = 'Klik tombol di atas untuk melanjutkan pembayaran.';
        }
    });
}

async function pollStatus() {
    const interval = setInterval(async () => {
        try {
            const res  = await fetch(statusUrl);
            const data = await res.json();
            if (data.status === 'paid' && data.ticket_url) {
                clearInterval(interval);
                window.location.href = data.ticket_url;
            }
        } catch {}
    }, 3000);

    // Stop polling after 10 minutes
    setTimeout(() => clearInterval(interval), 600000);
}

// Countdown timer (2 jam = 7200 detik)
let seconds = 7200;
const timer = document.getElementById('timer');
const tick  = setInterval(() => {
    if (seconds <= 0) { clearInterval(tick); return; }
    seconds--;
    const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
    const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    timer.textContent = `${h}:${m}:${s}`;
}, 1000);
</script>
</body>
</html>
