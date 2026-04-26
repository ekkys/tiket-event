{{-- FILE: resources/views/scanner/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Scanner Tiket - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        header {
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-weight: 800;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stats {
            font-size: 13px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 12px;
            border-radius: 100px;
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }

        .scanner-container {
            position: relative;
            width: 100%;
            border-radius: 24px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(92, 59, 254, 0.3);
        }

        #reader {
            width: 100% !important;
            border: none !important;
        }

        #reader__scan_region {
            background: #000 !important;
        }

        .controls {
            margin-top: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .btn {
            padding: 16px;
            border-radius: 16px;
            border: none;
            font-family: inherit;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 14px;
        }

        .btn:active {
            transform: scale(0.95);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 8px 16px rgba(92, 59, 254, 0.3);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        #result {
            margin-top: 24px;
            border-radius: 24px;
            padding: 32px 24px;
            background: var(--card);
            display: none;
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 10;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .res-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .res-title {
            font-size: 1.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .res-msg {
            text-align: center;
            font-size: 15px;
            opacity: 0.7;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .res-info {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-key {
            opacity: 0.5;
            font-weight: 500;
        }

        .info-val {
            font-weight: 700;
            color: #fff;
        }

        .valid {
            border: 2px solid var(--success);
            box-shadow: 0 0 30px rgba(0, 196, 140, 0.2);
        }

        .invalid {
            border: 2px solid var(--error);
            box-shadow: 0 0 30px rgba(255, 71, 87, 0.2);
        }

        .used {
            border: 2px solid var(--warning);
            box-shadow: 0 0 30px rgba(255, 184, 0, 0.2);
        }

        #manualInput {
            margin-top: 24px;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        input {
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-family: inherit;
            font-size: 16px;
            margin-bottom: 16px;
            outline: none;
            transition: 0.2s;
        }

        input:focus {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
        }

        .history {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .history-title {
            font-size: 12px;
            font-weight: 800;
            opacity: 0.4;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .history-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 16px;
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-10px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .nav-back {
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            opacity: 0.6;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .nav-back:hover {
            opacity: 1;
        }

        @media (max-width: 480px) {
            main {
                padding: 16px;
            }

            .header {
                padding: 16px;
            }

            .res-icon {
                font-size: 3.5rem;
            }

            .btn {
                padding: 14px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <header>
        <a href="{{ route('admin.dashboard') }}" class="nav-back">← Dashboard</a>
        <div class="stats">Checked In: <span id="checkCount">0</span></div>
    </header>

    <main>
        <div class="scanner-container">
            <div id="reader"></div>
        </div>

        <div class="controls">
            <button class="btn btn-outline" id="inputBtn">⌨️ Input Manual</button>
            <button class="btn btn-outline" id="switchCamBtn">🔄 Switch Camera</button>
        </div>

        <div id="manualInput">
            <input type="text" id="tokenInput" placeholder="Masukkan token tiket..." autocomplete="off">
            <button class="btn btn-primary" style="width: 100%;"
                onclick="verifyToken(document.getElementById('tokenInput').value)">Verifikasi Tiket</button>
        </div>

        <div id="result">
            <div class="res-icon" id="resIcon">✅</div>
            <div class="res-title" id="resTitle">Tiket Valid</div>
            <div class="res-msg" id="resMsg">Silakan masuk!</div>

            <div class="res-info" id="resInfo">
                <div class="info-row">
                    <span class="info-key">Event</span>
                    <span class="info-val" id="resEvent">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Nama Lengkap</span>
                    <span class="info-val" id="resName">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">No. Registrasi</span>
                    <span class="info-val" id="resRegCode">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Email</span>
                    <span class="info-val" id="resEmail">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">No. HP</span>
                    <span class="info-val" id="resPhone">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">NIK/ID</span>
                    <span class="info-val" id="resIdNumber">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Jenis Kelamin</span>
                    <span class="info-val" id="resGender">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Instansi</span>
                    <span class="info-val" id="resInst">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Alamat</span>
                    <span class="info-val" id="resAddress">-</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Waktu Check-in</span>
                    <span class="info-val" id="resCheckIn">-</span>
                </div>
            </div>

            <div id="verifyAction" style="display: none;">
                <button class="btn btn-primary" style="margin-top: 24px; width: 100%; background: var(--success); box-shadow: 0 8px 16px rgba(0, 196, 140, 0.2);" id="btnConfirm" onclick="confirmVerification()">✅ Verifikasi & Konfirmasi</button>
            </div>

            <button class="btn btn-primary" style="margin-top: 24px; width: 100%;" id="btnReset" onclick="resetScanner()">Scan Tiket Lain</button>
        </div>

        <div class="history">
            <div class="history-title">Riwayat Terakhir</div>
            <div id="historyList"></div>
        </div>
    </main>

    <script>
        let html5QrCode;
        let currentCameraId;
        let cameras = [];

        const config = {
            fps: 15,
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                let size = Math.min(viewfinderWidth, viewfinderHeight) * 0.7;
                return { width: size, height: size };
            },
            aspectRatio: 1.0
        };

        function onScanSuccess(decodedText) {
            // Extract token from URL or use text directly
            let token = decodedText;
            if (decodedText.includes('/')) {
                const parts = decodedText.split('/');
                token = parts[parts.length - 1];
            }

            // Vibrate device if supported
            if (navigator.vibrate) navigator.vibrate(100);

            verifyToken(token);
        }

        async function verifyToken(token) {
            if (!token) return;

            if (html5QrCode && html5QrCode.isScanning) {
                await html5QrCode.stop().catch(err => console.error(err));
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
                if (data.status !== 'valid') {
                    updateStats();
                    addToHistory(data);
                }

            } catch (error) {
                alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                resetScanner();
            }
        }

        function showResult(data) {
            const resDiv = document.getElementById('result');
            const resIcon = document.getElementById('resIcon');
            const resTitle = document.getElementById('resTitle');
            const resMsg = document.getElementById('resMsg');

            resDiv.style.display = 'block';
            document.getElementById('manualInput').style.display = 'none';

            if (data.status === 'valid') {
                resDiv.className = 'valid';
                resIcon.textContent = '✅';
                resTitle.textContent = 'TIKET DITEMUKAN';
                resTitle.style.color = 'var(--success)';
                resMsg.textContent = 'Silakan cek identitas peserta di bawah.';

                document.getElementById('resEvent').textContent = data.event;
                document.getElementById('resName').textContent = data.attendee.name;
                document.getElementById('resRegCode').textContent = data.attendee.registration_code;
                document.getElementById('resEmail').textContent = data.attendee.email;
                document.getElementById('resPhone').textContent = data.attendee.phone;
                document.getElementById('resIdNumber').textContent = data.attendee.id_number || '-';
                document.getElementById('resGender').textContent = data.attendee.gender || '-';
                document.getElementById('resInst').textContent = data.attendee.institution || '-';
                document.getElementById('resAddress').textContent = data.attendee.address || '-';
                document.getElementById('resCheckIn').textContent = 'Menunggu Verifikasi...';

                // Show verify button, hide reset button
                document.getElementById('verifyAction').style.display = 'block';
                document.getElementById('btnReset').style.display = 'none';
                
                // Store token for confirmation
                window.currentToken = data.token;
            } else if (data.status === 'used') {
                resDiv.className = 'used';
                resIcon.textContent = '⚠️';
                resTitle.textContent = 'SUDAH DIGUNAKAN';
                resTitle.style.color = 'var(--warning)';
                resMsg.textContent = `Pernah di-scan pada: ${data.used_at}`;

                document.getElementById('resEvent').textContent = data.event;
                document.getElementById('resName').textContent = data.attendee.name;
                document.getElementById('resRegCode').textContent = data.attendee.registration_code;
                document.getElementById('resEmail').textContent = data.attendee.email;
                document.getElementById('resPhone').textContent = data.attendee.phone;
                document.getElementById('resIdNumber').textContent = data.attendee.id_number || '-';
                document.getElementById('resGender').textContent = data.attendee.gender || '-';
                document.getElementById('resInst').textContent = data.attendee.institution || '-';
                document.getElementById('resAddress').textContent = data.attendee.address || '-';
                document.getElementById('resCheckIn').textContent = data.used_at;

                document.getElementById('verifyAction').style.display = 'none';
                document.getElementById('btnReset').style.display = 'block';
            } else {
                resDiv.className = 'invalid';
                resIcon.textContent = '❌';
                resTitle.textContent = 'TIDAK VALID';
                resTitle.style.color = 'var(--error)';
                resMsg.textContent = data.message || 'Token tidak terdaftar dalam sistem.';

                const fields = ['resEvent', 'resName', 'resRegCode', 'resEmail', 'resPhone', 'resIdNumber', 'resGender', 'resInst', 'resAddress', 'resCheckIn'];
                fields.forEach(id => document.getElementById(id).textContent = '-');

                document.getElementById('verifyAction').style.display = 'none';
                document.getElementById('btnReset').style.display = 'block';
            }
        }

        async function confirmVerification() {
            const btn = document.getElementById('btnConfirm');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '⌛ Memproses...';

            try {
                const response = await fetch('{{ route("scanner.confirm") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ token: window.currentToken })
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('resTitle').textContent = 'VERIFIKASI BERHASIL';
                    document.getElementById('resMsg').textContent = 'Data log telah tersimpan. Silakan masuk.';
                    document.getElementById('resCheckIn').textContent = data.checked_in;
                    
                    document.getElementById('verifyAction').style.display = 'none';
                    document.getElementById('btnReset').style.display = 'block';
                    
                    updateStats();
                    // Add to history with success status
                    addToHistory({
                        status: 'valid',
                        attendee: document.getElementById('resName').textContent
                    });
                } else {
                    alert(data.message || 'Gagal melakukan verifikasi.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function resetScanner() {
            document.getElementById('result').style.display = 'none';
            document.getElementById('tokenInput').value = '';
            document.getElementById('btnConfirm').disabled = false;
            document.getElementById('btnConfirm').innerHTML = '✅ Verifikasi & Konfirmasi';
            window.currentToken = null;
            startScanner();
        }

        async function startScanner() {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            const camerasFound = await Html5Qrcode.getCameras();
            if (camerasFound && camerasFound.length > 0) {
                cameras = camerasFound;
                // Default to back camera
                const backCam = cameras.find(c => c.label.toLowerCase().includes('back')) || cameras[cameras.length - 1];
                currentCameraId = backCam.id;

                html5QrCode.start(currentCameraId, config, onScanSuccess)
                    .catch(err => {
                        console.error("Camera start error:", err);
                        // Fallback to simpler start
                        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
                    });
            } else {
                html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
            }
        }

        document.getElementById('switchCamBtn').addEventListener('click', async () => {
            if (cameras.length < 2) return;

            if (html5QrCode && html5QrCode.isScanning) {
                await html5QrCode.stop();
                const currentIndex = cameras.findIndex(c => c.id === currentCameraId);
                const nextIndex = (currentIndex + 1) % cameras.length;
                currentCameraId = cameras[nextIndex].id;
                html5QrCode.start(currentCameraId, config, onScanSuccess);
            }
        });

        function updateStats() {
            fetch('{{ route("scanner.stats") }}')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('checkCount').textContent = data.total_checked_in;
                })
                .catch(e => console.error("Stats update error:", e));
        }

        function addToHistory(data) {
            const list = document.getElementById('historyList');
            const item = document.createElement('div');
            item.className = 'history-item';

            let name = '-';
            if (data.attendee) {
                name = typeof data.attendee === 'string' ? data.attendee : data.attendee.name;
            } else if (data.status === 'invalid') {
                name = 'Token Tidak Valid';
            }

            const status = data.status === 'valid' ? '✅' : (data.status === 'used' ? '⚠️' : '❌');

            item.innerHTML = `
        <span style="font-weight:600;">${name}</span>
        <span>${status}</span>
    `;

            list.prepend(item);
            if (list.children.length > 5) list.lastChild.remove();
        }

        document.getElementById('inputBtn').addEventListener('click', () => {
            const manualInput = document.getElementById('manualInput');
            const result = document.getElementById('result');

            if (manualInput.style.display === 'block') {
                manualInput.style.display = 'none';
                startScanner();
            } else {
                manualInput.style.display = 'block';
                result.style.display = 'none';
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop();
                }
            }
        });

        // Start initially
        startScanner();
        updateStats();
    </script>
</body>

</html>