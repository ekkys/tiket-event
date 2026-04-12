{{-- FILE: resources/views/scanner/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner Tiket - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        :root {
            --primary: #5C3BFE;
            --success: #00C48C;
            --error: #FF4757;
            --warning: #FFB800;
            --bg: #0F0A2A;
            --card: #1A123D;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            padding: 20px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo { font-weight: 800; font-size: 1.2rem; }
        .stats { font-size: 13px; font-weight: 600; opacity: 0.8; }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }

        #reader {
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            border: 2px solid rgba(92,59,254,0.3);
            background: #000;
        }

        .controls {
            margin-top: 20px;
            display: flex;
            gap: 12px;
        }

        .btn {
            flex: 1;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-family: inherit;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: transparent; border: 2px solid rgba(255,255,255,0.1); color: white; }

        #result {
            margin-top: 24px;
            border-radius: 20px;
            padding: 24px;
            background: var(--card);
            display: none;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .res-icon { font-size: 3rem; margin-bottom: 16px; text-align: center; }
        .res-title { font-size: 1.25rem; font-weight: 800; text-align: center; margin-bottom: 8px; }
        .res-msg { text-align: center; font-size: 14px; opacity: 0.8; margin-bottom: 20px; line-height: 1.5; }

        .res-info {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .info-row:last-child { margin-bottom: 0; }
        .info-key { opacity: 0.6; }
        .info-val { font-weight: 700; }

        .valid { border: 3px solid var(--success); }
        .invalid { border: 3px solid var(--error); }
        .used { border: 3px solid var(--warning); }

        #manualInput {
            margin-top: 20px;
            display: none;
        }

        input {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: white;
            font-family: inherit;
            margin-bottom: 12px;
        }

        .history {
            margin-top: 32px;
        }
        .history-title { font-size: 14px; font-weight: 700; opacity: 0.5; margin-bottom: 12px; text-transform: uppercase; }
        .history-item {
            background: rgba(255,255,255,0.03);
            padding: 12px 16px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">🎟️ TIKET SCAN</div>
    <div class="stats">Checked In: <span id="checkCount">0</span></div>
</header>

<main>
    <div id="reader"></div>

    <div class="controls">
        <button class="btn btn-outline" id="torchBtn">🔦 Senter</button>
        <button class="btn btn-outline" id="inputBtn">⌨️ Manual</button>
    </div>

    <div id="manualInput">
        <input type="text" id="tokenInput" placeholder="Masukkan token tiket...">
        <button class="btn btn-primary" onclick="verifyToken(document.getElementById('tokenInput').value)">Verifikasi</button>
    </div>

    <div id="result">
        <div class="res-icon" id="resIcon">✅</div>
        <div class="res-title" id="resTitle">Tiket Valid</div>
        <div class="res-msg" id="resMsg">Silakan masuk!</div>
        
        <div class="res-info" id="resInfo">
            <div class="info-row">
                <span class="info-key">Nama</span>
                <span class="info-val" id="resName">-</span>
            </div>
            <div class="info-row">
                <span class="info-key">Instansi</span>
                <span class="info-val" id="resInst">-</span>
            </div>
        </div>
        
        <button class="btn btn-primary" style="margin-top: 20px;" onclick="resetScanner()">Scan Berikutnya</button>
    </div>

    <div class="history">
        <div class="history-title">Riwayat Terakhir</div>
        <div id="historyList"></div>
    </div>
</main>

<script>
let html5QrCode;
const config = { fps: 10, qrbox: { width: 250, height: 250 } };

function onScanSuccess(decodedText, decodedResult) {
    // QR code is usually a URL: https://.../v/token
    // We need just the token
    const parts = decodedText.split('/');
    const token = parts[parts.length - 1];
    
    verifyToken(token);
}

async function verifyToken(token) {
    if(!token) return;
    
    if(html5QrCode) {
        html5QrCode.stop().catch(err => console.error(err));
    }
    
    try {
        const response = await fetch('{{ route("scanner.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token })
        });
        
        const data = await response.json();
        showResult(data);
        updateStats();
        addToHistory(data);
        
    } catch (error) {
        alert('Terjadi kesalahan koneksi');
        resetScanner();
    }
}

function showResult(data) {
    const resDiv = document.getElementById('result');
    const resIcon = document.getElementById('resIcon');
    const resTitle = document.getElementById('resTitle');
    const resMsg = document.getElementById('resMsg');
    
    resDiv.style.display = 'block';
    resDiv.className = 'used'; // default
    
    if (data.status === 'valid') {
        resDiv.className = 'valid';
        resIcon.textContent = '✅';
        resTitle.textContent = 'TIKET VALID';
        resMsg.textContent = 'Silakan masuk!';
        document.getElementById('resName').textContent = data.attendee.name;
        document.getElementById('resInst').textContent = data.attendee.institution || '-';
    } else if (data.status === 'used') {
        resDiv.className = 'used';
        resIcon.textContent = '⚠️';
        resTitle.textContent = 'SUDAH DIGUNAKAN';
        resMsg.textContent = `Scanned at: ${data.used_at}`;
        document.getElementById('resName').textContent = data.attendee || '-';
    } else {
        resDiv.className = 'invalid';
        resIcon.textContent = '❌';
        resTitle.textContent = 'TIDAK VALID';
        resMsg.textContent = data.message;
    }
}

function resetScanner() {
    document.getElementById('result').style.display = 'none';
    document.getElementById('manualInput').style.display = 'none';
    startScanner();
}

function startScanner() {
    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
}

function updateStats() {
    fetch('{{ route("scanner.stats") }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('checkCount').textContent = data.total_checked_in;
        });
}

function addToHistory(data) {
    const list = document.getElementById('historyList');
    const item = document.createElement('div');
    item.className = 'history-item';
    
    const name = data.attendee ? (data.attendee.name || data.attendee) : 'Tiket Tidak Valid';
    const status = data.success ? '✅' : '❌';
    
    item.innerHTML = `
        <span>${name}</span>
        <span>${status}</span>
    `;
    
    list.prepend(item);
    if(list.children.length > 5) list.lastChild.remove();
}

document.getElementById('inputBtn').addEventListener('click', () => {
    document.getElementById('manualInput').style.display = 'block';
    if(html5QrCode) html5QrCode.stop();
});

// Start initially
startScanner();
updateStats();
</script>
</body>
</html>
