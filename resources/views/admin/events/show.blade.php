<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event - {{ $event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --bg: #F8F9FD;
            --sidebar: #FFFFFF;
            --card: #FFFFFF;
            --text: #1A1033;
            --muted: #7B7A8E;
            --border: #E2E8F0;
            --success: #00C48C;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background: var(--sidebar); border-right: 1px solid #E2E8F0; padding: 32px 24px; display: flex; flex-direction: column; transition: transform 0.3s ease; z-index: 1000; }
        .logo { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-bottom: 48px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; text-decoration: none; color: var(--muted); font-weight: 600; margin-bottom: 8px; transition: 0.2s; }
        .nav-link.active { background: var(--primary); color: white; }
        .nav-link:hover:not(.active) { background: #F1F5F9; color: var(--text); }

        .main-content { flex: 1; padding: 40px; width: 100%; transition: all 0.3s ease; }
        .header { margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.75rem; font-weight: 800; }

        .grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 32px; }
        .card { background: white; border-radius: 24px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #F1F5F9; }
        
        .promo-section { text-align: center; }
        .qr-card { background: #F8F9FD; border-radius: 24px; padding: 24px; margin-bottom: 24px; }
        .qr-image { width: 100%; max-width: 240px; border-radius: 16px; mix-blend-mode: multiply; }
        
        .info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #F1F5F9; font-size: 14px; }
        .info-label { color: var(--muted); font-weight: 500; }
        .info-value { font-weight: 700; color: var(--text); }

        .btn { padding: 12px 24px; border-radius: 12px; border: none; font-family: inherit; font-weight: 700; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; transition: 0.2s; width: 100%; margin-bottom: 12px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--muted); }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }

        .alert { background: #D1FAE5; color: #065F46; padding: 16px; border-radius: 16px; margin-bottom: 24px; font-weight: 700; font-size: 14px; }

        .mobile-header { display: none; background: white; padding: 16px 24px; border-bottom: 1px solid #E2E8F0; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 999; }
        .menu-toggle { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text); }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; }

        @media (max-width: 1024px) {
            body { flex-direction: column; }
            .sidebar { position: fixed; left: 0; top: 0; bottom: 0; transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { padding: 24px; }
            .mobile-header { display: flex; }
            .sidebar-overlay.show { display: block; }
            .grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .header { flex-direction: column; align-items: flex-start; gap: 16px; }
            .header h1 { font-size: 1.5rem; }
            .header .btn { width: auto; margin-bottom: 0; }
            .card { padding: 24px; }
            .main-content { padding: 16px; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="mobile-header">
    <div class="logo" style="margin-bottom: 0; font-size: 1.2rem;">🎟️ Admin</div>
    <button class="menu-toggle" id="menuToggle">☰</button>
</div>

<div class="sidebar" id="sidebar">
    <div class="logo">🎟️ Admin</div>
    <nav style="flex: 1;">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">📊 Dashboard</a>
        <a href="{{ route('admin.events') }}" class="nav-link active">📅 Events</a>
        <a href="{{ route('admin.registrations') }}" class="nav-link">👥 Peserta</a>
        <a href="{{ route('admin.scan-logs') }}" class="nav-link">📋 Log Scan</a>
        <a href="{{ route('scanner.index') }}" class="nav-link">📷 Scanner</a>
        <a href="{{ route('admin.profile') }}" class="nav-link">👤 Profil</a>
    </nav>
    <div style="margin-top: auto; padding-top: 24px; border-top: 1px solid #F1F5F9;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link" style="width: 100%; border: none; background: none; cursor: pointer; color: #DC2626;">🚪 Keluar</button>
        </form>
    </div>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert">✅ {{ session('success') }}</div>
    @endif

    <div class="header">
        <div>
            <h1 style="color: var(--primary);">{{ $event->name }}</h1>
            <p style="color: var(--muted); font-weight: 600; font-size: 14px;">Dashboard Admin > Detail Event</p>
        </div>
        <div style="display:flex; gap:12px;">
            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-outline" style="width:auto; margin-bottom:0;">✏️ Edit Data</a>
            <a href="{{ route('admin.events') }}" class="btn btn-outline" style="width:auto; margin-bottom:0;">← Kembali</a>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h2 style="font-size: 1.25rem; margin-bottom: 24px;">Ringkasan Informasi</h2>
            
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value" style="color: {{ $event->is_active ? 'var(--success)' : '#DC2626' }}">
                    {{ $event->is_active ? '● Aktif' : '○ Nonaktif' }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Pelaksanaan</span>
                <span class="info-value">{{ $event->event_date->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu</span>
                <span class="info-value">{{ $event->event_date->format('H:i') }} WIB</span>
            </div>
            <div class="info-row">
                <span class="info-label">Lokasi</span>
                <span class="info-value">
                    {{ $event->location_name ?: ($event->location ?? '-') }}
                    @if($event->location_link)
                        <br><a href="{{ $event->location_link }}" target="_blank" style="font-size: 11px; color: var(--primary);">🔗 Lihat Link</a>
                    @endif
                </span>
            </div>
            <div class="info-row" style="border:none;">
                <span class="info-label">Harga</span>
                <span class="info-value">{{ $event->is_free ? 'Gratis' : 'Rp '.number_format($event->price, 0, ',', '.') }}</span>
            </div>

            <div style="margin-top: 32px; padding: 24px; background: #FAF9FF; border-radius: 20px;">
                <h3 style="font-size: 14px; margin-bottom: 16px;">Statistik Quota</h3>
                <div style="display: flex; gap: 40px; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Terdaftar</div>
                        <div style="font-size: 24px; font-weight: 800; color: var(--primary);">{{ $event->registered_count }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total Kuota</div>
                        <div style="font-size: 24px; font-weight: 800;">{{ $event->quota }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card promo-section">
            <h2 style="font-size: 1.25rem; margin-bottom: 24px;">Alat Promosi</h2>
            <div class="qr-card">
                <p style="font-size: 12px; font-weight: 700; color: var(--muted); margin-bottom: 12px;">SCAN UNTUK DAFTAR</p>
                <img src="{{ $qrCodeDataUri }}" alt="QR Code" class="qr-image">
                <p style="margin-top: 12px; font-size: 11px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $url }}</p>
            </div>

            <a href="{{ route('admin.events.flyer', $event->id) }}" class="btn btn-primary">
                📥 Download Flyer (Banner + QR)
            </a>
            
            <a href="https://api.whatsapp.com/send?text=Halo! Yuk ajak teman Anda mendaftar di event {{ $event->name }} melalui link berikut: {{ $url }}" 
               target="_blank" class="btn btn-success">
                📱 Bagikan ke WhatsApp
            </a>
            
            <a href="{{ $url }}" target="_blank" class="btn btn-outline">
                🌐 Lihat Halaman Publik
            </a>
        </div>
    </div>
</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleMenu() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    menuToggle.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);
</script>

</body>
</html>
