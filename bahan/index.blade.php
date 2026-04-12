{{-- FILE: resources/views/scanner/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0F0A2A">
    <title>Scanner Tiket - Petugas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #5C3BFE;
            --success: #00C48C;
            --error: #FF4757;
            --warning: #FFA502;
            --bg: #0F0A2A;
            --card: #1C1540;
            --border: rgba(255,255,255,0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: white;
            min-height: 100vh;
            min-height: 100dvh;
        }

        /* HEADER */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .header-title { font-size: 18px; font-weight: 800; }
        .header-sub   { font-size: 12px; opacity: 0.5; margin-top: 2px; }

        .stats-pill {
            background: rgba(255,255,255,0.08);
            border-radius: 100px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
        }

        /* SCANNER SETUP */
        .setup-screen {
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .input-group label {
            font-size: 13px;
            font-weight: 600;
            opacity: 0.6;
            display: block;
            margin-bottom: 8px;
        }

        .input-group input {
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            color: white;
            font-family: inherit;
            font-size: 15px;
            outline: none;
        }

        .input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(92,59,254,0.2);
        }

        .btn-start {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            border: none;
            border-radius: 14px;
            color: white;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(92,59,254,0.4);
        }

        /* SCANNER VIEW */
        .scanner-screen { display: none; flex-direction: column; height: calc(100vh - 65px); height: calc(100dvh - 65px); }

        .camera-wrap {
            flex: 1;
            position: relative;
            background: black;
            overflow: hidden;
        }

        #scanner-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scan-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .scan-frame {
            width: 240px;
            height: 240px;
            position: relative;
        }

        .scan-frame::before, .scan-frame::after,
        .scan-frame span::before, .scan-frame span::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border-color: white;
            border-style: solid;
        }

        .scan-frame::before  { top: 0;    left: 0;  border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
        .scan-frame::after   { top: 0;    right: 0; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
        .scan-frame span::before { bottom: 0; left: 0;  border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
        .scan-frame span::after  { bottom: 0; right: 0; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }

        .scan-line {
            position: absolute;
            left: 10px; right: 10px; height: 2px;
            background: linear-gradient(90deg, transparent, #5C3BFE, transparent);
            animation: scanLine 2s ease-in-out infinite;
        }

        @keyframes scanLine {
            0%   { top: 20px; opacity: 1; }
            50%  { opacity: 0.5; }
            100% { top: 210px; opacity: 1; }
        }

        .scan-hint {
            position: absolute;
            bottom: 24px;
            left: 0; right: 0;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            opacity: 0.8;
        }

        /* RESULT */
        .result-panel {
            padding: 20px;
            background: var(--card);
            min-height: 180px;
            border-top: 1px solid var(--border);
        }

        .result-idle {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 120px;
            opacity: 0.3;
            font-size: 14px;
            font-weight: 600;
        }

        .result-card {
            border-radius: 16px;
            padding: 20px;
            display: none;
        }

        .result-card.valid   { background: rgba(0,196,140,0.15); border: 2px solid var(--success); }
        .result-card.invalid { background: rgba(255,71,87,0.12);  border: 2px solid var(--error); }
        .result-card.used    { background: rgba(255,165,2,0.12);  border: 2px solid var(--warning); }

        .result-status {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .result-status.valid   { color: var(--success); }
        .result-status.invalid { color: var(--error); }
        .result-status.used    { color: var(--warning); }

        .result-name {
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 8px;
        }

        .result-detail {
            font-size: 13px;
            opacity: 0.7;
            margin-top: 4px;
        }

        .result-time {
            font-size: 12px;
            opacity: 0.5;
            margin-top: 8px;
        }

        /* Manual input fallback */
        .manual-btn {
            text-align: center;
            padding: 8px;
        }

        .manual-link {
            font-size: 13px;
            color: rgba(255,255,255,0.4);
            text-decoration: underline;
            cursor: pointer;
            background: none;
            border: none;
            color: rgba(255,255,255,0.4);
            font-family: inherit;
        }

        .manual-section { display: none; padding: 0 20px 20px; }
        .manual-row { display: flex; gap: 8px; }
        .manual-row input {
            flex: 1;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            color: white;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }

        .manual-row button {
            background: var(--primary);
            border: none;
            border-radius: 10px;
            color: white;
            padding: 12px 20px;
            font-family: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        .back-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.5);
            font-family: inherit;
            font-size: 13px;
            cursor: pointer;
            padding: 4px 8px;
        }

        /* PIN Gate */
        .pin-screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 65px);
            padding: 24px;
        }

        .pin-title { font-size: 1.3rem; font-weight: 800; margin-bottom: 8px; }
        .pin-sub   { font-size: 14px; opacity: 0.5; margin-bottom: 32px; }

        .pin-input {
            background: var(--card);
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 16px 24px;
            color: white;
            font-family: 'Plus Jakarta Sans', monospace;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 4px;
            text-align: center;
            width: 200px;
            outline: none;
            margin-bottom: 16px;
        }

        .pin-input:focus { border-color: var(--primary); }

        .pin-btn {
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            border: none;
            border-radius: 12px;
            color: white;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 40px;
            cursor: pointer;
        }

        .pin-error {
            color: var(--error);
            font-size: 13px;
            font-weight: 600;
            margin-top: 12px;
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <div class="header-title">📲 Scanner Petugas</div>
        <div class="header-sub" id="scannerName">{{ config('app.name') }}</div>
    </div>
    <div class="stats-pill" id="statsCount">- masuk</div>
</div>

<!-- PIN Screen -->
<div class="pin-screen" id="pinScreen">
    <div class="pin-title">🔐 Masukkan PIN Petugas</div>
    <div class="pin-sub">Akses khusus petugas scan tiket</div>
    <input type="password" class="pin-input" id="pinInput" maxlength="6" placeholder="••••••">
    <button class="pin-btn" onclick="checkPin()">Masuk</button>
    <div class="pin-error" id="pinError"></div>
</div>

<!-- Setup Screen -->
<div class="setup-screen" id="setupScreen" style="display:none">
    <div class="input-group">
        <label>Nama Anda (Petugas)</label>
        <input type="text" id="scannerNameInput" placeholder="Contoh: Budi - Pintu Utara">
    </div>
    <button class="btn-start" onclick="startScanner()">📷 Mulai Scan</button>
</div>

<!-- Scanner Screen -->
<div class="scanner-screen" id="scannerScreen">
    <div class="camera-wrap">
        <video id="scanner-video" playsinline autoplay muted></video>
        <div class="scan-overlay">
            <div class="scan-frame">
                <span></span>
                <div class="scan-line"></div>
            </div>
        </div>
        <div class="scan-hint">Arahkan kamera ke QR Code tiket</div>
    </div>

    <div class="result-panel">
        <div class="result-idle" id="resultIdle">👆 Scan QR Code untuk verifikasi</div>
        <div class="result-card" id="resultCard">
            <div class="result-status" id="resultStatus"></div>
            <div class="result-name" id="resultName"></div>
            <div class="result-detail" id="resultDetail"></div>
            <div class="result-time" id="resultTime"></div>
        </div>
    </div>

    <div class="manual-btn">
        <button class="manual-link" onclick="toggleManual()">⌨️ Input token manual</button>
        <button class="back-btn" onclick="goBack()">↩ Kembali</button>
    </div>

    <div class="manual-section" id="manualSection">
        <div class="manual-row">
            <input type="text" id="manualToken" placeholder="Paste token QR di sini...">
            <button onclick="verifyManual()">Cek</button>
        </div>
    </div>
</div>

<!-- QR Scanner Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>

<script>
const SCANNER_PIN  = '{{ config("scanner.pin", "123456") }}';
const VERIFY_URL   = '{{ route("scanner.verify") }}';
const STATS_URL    = '{{ route("scanner.stats") }}';
const CSRF_TOKEN   = '{{ csrf_token() }}';

let scannerName    = 'Petugas';
let isScanning     = false;
let lastToken      = '';
let cooldown       = false;
let videoStream    = null;

// ---- PIN ----
function checkPin() {
    const pin = document.getElementById('pinInput').value;
    if (pin === SCANNER_PIN) {
        document.getElementById('pinScreen').style.display   = 'none';
        document.getElementById('setupScreen').style.display = 'flex';
    } else {
        document.getElementById('pinError').textContent = '❌ PIN salah. Coba lagi.';
        document.getElementById('pinInput').value = '';
    }
}

document.getElementById('pinInput').addEventListener('keyup', e => {
    if (e.key === 'Enter') checkPin();
});

// ---- START ----
async function startScanner() {
    const name = document.getElementById('scannerNameInput').value.trim();
    scannerName = name || 'Petugas';
    document.getElementById('scannerName').textContent = scannerName;

    document.getElementById('setupScreen').style.display  = 'none';
    document.getElementById('scannerScreen').style.display = 'flex';

    try {
        videoStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        const video = document.getElementById('scanner-video');
        video.srcObject = videoStream;
        video.play();
        isScanning = true;
        requestAnimationFrame(tick);
    } catch (err) {
        alert('Tidak dapat mengakses kamera: ' + err.message);
    }

    updateStats();
    setInterval(updateStats, 15000);
}

// ---- SCAN LOOP ----
function tick() {
    if (!isScanning) return;
    const video  = document.getElementById('scanner-video');
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        const canvas  = document.createElement('canvas');
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const img  = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(img.data, img.width, img.height, { inversionAttempts: 'dontInvert' });

        if (code && !cooldown) {
            const url   = code.data;
            // Extract token from URL
            const token = extractToken(url);
            if (token && token !== lastToken) {
                lastToken = token;
                verify(token);
            }
        }
    }
    requestAnimationFrame(tick);
}

function extractToken(raw) {
    // Coba parse sebagai URL dan ambil path terakhir
    try {
        const url = new URL(raw);
        const parts = url.pathname.split('/');
        return parts[parts.length - 1] || raw;
    } catch {
        return raw; // Langsung token
    }
}

// ---- VERIFY ----
async function verify(token) {
    cooldown = true;
    showLoading();

    try {
        const res = await fetch(VERIFY_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({ token, scanner_name: scannerName }),
        });

        const data = await res.json();
        showResult(data);

        // Vibrate feedback
        if (navigator.vibrate) {
            data.success ? navigator.vibrate([100, 50, 100]) : navigator.vibrate(500);
        }
    } catch (err) {
        showResult({ success: false, message: '⚠️ Koneksi bermasalah, coba lagi' });
    }

    // Cooldown 3 detik sebelum scan lagi
    setTimeout(() => {
        cooldown   = false;
        lastToken  = '';
    }, 3000);
}

function showLoading() {
    document.getElementById('resultIdle').style.display = 'none';
    const card = document.getElementById('resultCard');
    card.className = 'result-card valid';
    card.style.display = 'block';
    document.getElementById('resultStatus').textContent = '⏳ Memverifikasi...';
    document.getElementById('resultStatus').className   = 'result-status';
    document.getElementById('resultName').textContent   = '';
    document.getElementById('resultDetail').textContent = '';
    document.getElementById('resultTime').textContent   = '';
}

function showResult(data) {
    const card    = document.getElementById('resultCard');
    const status  = document.getElementById('resultStatus');
    const name    = document.getElementById('resultName');
    const detail  = document.getElementById('resultDetail');
    const time    = document.getElementById('resultTime');

    card.style.display = 'block';
    document.getElementById('resultIdle').style.display = 'none';

    if (data.success) {
        card.className = 'result-card valid';
        status.className = 'result-status valid';
        status.textContent = data.message;
        name.textContent   = data.attendee?.name || '';
        detail.textContent = [data.attendee?.institution, data.attendee?.phone].filter(Boolean).join(' · ');
        time.textContent   = '🕐 ' + (data.checked_in || '');
    } else {
        const cls = data.status === 'used' ? 'used' : 'invalid';
        card.className = 'result-card ' + cls;
        status.className = 'result-status ' + cls;
        status.textContent = data.message;
        name.textContent   = data.attendee || '';
        detail.textContent = data.used_at ? 'Digunakan: ' + data.used_at + (data.used_by ? ' oleh ' + data.used_by : '') : '';
        time.textContent   = '';
    }

    updateStats();
}

async function updateStats() {
    try {
        const res  = await fetch(STATS_URL);
        const data = await res.json();
        document.getElementById('statsCount').textContent =
            (data.total_checked_in || 0) + ' masuk';
    } catch {}
}

function toggleManual() {
    const s = document.getElementById('manualSection');
    s.style.display = s.style.display === 'block' ? 'none' : 'block';
}

function verifyManual() {
    const token = document.getElementById('manualToken').value.trim();
    if (token) verify(token);
}

document.getElementById('manualToken').addEventListener('keyup', e => {
    if (e.key === 'Enter') verifyManual();
});

function goBack() {
    isScanning = false;
    if (videoStream) videoStream.getTracks().forEach(t => t.stop());
    document.getElementById('scannerScreen').style.display = 'none';
    document.getElementById('setupScreen').style.display   = 'flex';
}
</script>
</body>
</html>
