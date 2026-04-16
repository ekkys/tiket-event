<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Platform Manajemen Tiket Event Terpercaya</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A; /* Navy Blue */
            --primary-light: #2563EB;
            --accent: #60A5FA;
            --dark: #0F172A;
            --text: #1E293B;
            --muted: #64748B;
            --bg: #F8FAFC;
            --white: #FFFFFF;
            --success: #10B981;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; overflow-x: hidden; }

        /* Navigation */
        nav { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 1000; border-bottom: 1px solid #E2E8F0; }
        .nav-container { max-width: 1200px; margin: 0 auto; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.5rem; font-weight: 800; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        
        .nav-actions { display: flex; gap: 12px; align-items: center; }
        .btn { padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 14px; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; font-family: inherit; justify-content: center; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--dark); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2); }
        .btn-outline { background: transparent; border: 1.5px solid var(--primary); color: var(--primary); }
        .btn-outline:hover { background: #EEF2FF; }

        /* Hero Section */
        .hero { background: radial-gradient(circle at top right, #1E3A8A, #0F172A); color: white; padding: 120px 20px 160px; text-align: center; position: relative; overflow: hidden; }
        .hero::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; }
        .hero-container { max-width: 800px; margin: 0 auto; position: relative; z-index: 2; }
        .hero h1 { font-size: clamp(2.5rem, 8vw, 4rem); font-weight: 800; line-height: 1.1; margin-bottom: 24px; letter-spacing: -1.5px; }
        .hero h1 span { color: var(--accent); }
        .hero p { font-size: clamp(1rem, 3vw, 1.25rem); color: #CBD5E1; margin-bottom: 40px; max-width: 600px; margin-inline: auto; }

        /* Search Bar */
        .search-container { max-width: 700px; margin: -60px auto 0; padding: 0 20px; position: relative; z-index: 10; }
        .search-box { background: var(--white); padding: 12px; border-radius: 24px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15); display: flex; gap: 12px; border: 1px solid #E2E8F0; }
        .search-box input { flex: 1; border: none; padding: 0 20px; font-size: 16px; outline: none; font-family: inherit; min-width: 0; }
        .search-box .btn { padding: 16px 32px; border-radius: 16px; }

        /* Features / Trust Section */
        .trust-section { max-width: 1200px; margin: 100px auto; padding: 0 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .trust-card { background: var(--white); padding: 40px 32px; border-radius: 28px; text-align: center; border: 1px solid #E2E8F0; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        .trust-card:hover { border-color: var(--accent); transform: translateY(-8px); box-shadow: 0 25px 60px rgba(0,0,0,0.1); }
        .trust-icon { font-size: 3rem; margin-bottom: 24px; display: block; }
        .trust-card h3 { font-size: 1.4rem; font-weight: 800; margin-bottom: 16px; color: var(--dark); letter-spacing: -0.5px; }
        .trust-card p { color: var(--muted); font-size: 15px; line-height: 1.6; }

        /* Main Content */
        .section-header { max-width: 1200px; margin: 80px auto 40px; padding: 0 20px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; }
        .section-header h2 { font-size: 2.25rem; font-weight: 800; color: var(--dark); letter-spacing: -1px; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px 100px; }
        .event-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 32px; }
        
        .event-card { background: var(--white); border-radius: 30px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid #E2E8F0; display: flex; flex-direction: column; }
        .event-card:hover { transform: translateY(-12px); box-shadow: 0 30px 70px rgba(15, 23, 42, 0.15); border-color: var(--accent); }
        
        .event-image-wrap { position: relative; height: 220px; overflow: hidden; }
        .event-image { width: 100%; height: 100%; object-fit: cover; transition: 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
        .event-card:hover .event-image { transform: scale(1.1); }
        .event-badge { position: absolute; top: 20px; left: 20px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); color: white; padding: 8px 16px; border-radius: 12px; font-size: 12px; font-weight: 800; z-index: 10; }

        .event-content { padding: 32px; flex: 1; display: flex; flex-direction: column; }
        .event-date { font-size: 14px; font-weight: 800; color: var(--primary-light); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .event-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 12px; color: var(--dark); line-height: 1.25; letter-spacing: -0.5px; }
        .event-location { font-size: 15px; color: var(--muted); margin-bottom: 24px; display: flex; align-items: center; gap: 6px; }
        
        .event-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1px solid #F1F5F9; margin-top: auto; }
        .event-price { font-size: 1.25rem; font-weight: 900; color: var(--dark); }
        .event-price.free { color: var(--success); }
        
        /* Footer */
        footer { background: var(--dark); color: #94A3B8; padding: 100px 20px 40px; border-top: 1px solid #1E293B; }
        .footer-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 60px; margin-bottom: 80px; }
        .footer-logo { color: white; font-size: 1.75rem; font-weight: 800; margin-bottom: 24px; display: block; text-decoration: none; }
        .footer-title { color: white; font-weight: 800; margin-bottom: 24px; text-transform: uppercase; font-size: 14px; letter-spacing: 1px; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 14px; }
        .footer-links a { color: inherit; text-decoration: none; transition: 0.2s; font-weight: 500; }
        .footer-links a:hover { color: var(--accent); padding-left: 8px; }
        .copyright { text-align: center; padding-top: 40px; border-top: 1px solid #1E293B; font-size: 14px; font-weight: 500; }

        /* Responsive */
        @media (max-width: 1024px) {
            .footer-container { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .nav-actions .btn:not(.btn-primary) { display: none; }
            .hero { padding: 100px 20px 140px; }
            .trust-section { grid-template-columns: 1fr; gap: 24px; }
            .section-header h2 { font-size: 1.75rem; }
            .event-grid { grid-template-columns: 1fr; }
            .search-box { flex-direction: column; padding: 16px; border-radius: 28px; }
            .search-box .btn { width: 100%; border-radius: 20px; }
            .footer-container { grid-template-columns: 1fr; gap: 40px; }
        }

        @media (max-width: 480px) {
            .nav-container { padding: 12px 16px; }
            .logo { font-size: 1.25rem; }
            .hero h1 { font-size: 2.25rem; }
            .event-content { padding: 24px; }
            .event-title { font-size: 1.3rem; }
        }

        .pagination { margin-top: 48px; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; align-items: center; }
        .pagination a, .pagination span { 
            padding: 12px 22px; 
            border-radius: 16px; 
            background: var(--white);
            border: 1px solid #E2E8F0; 
            text-decoration: none; 
            color: var(--text); 
            font-size: 14px; 
            font-weight: 800;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 50px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .pagination a:hover { 
            border-color: var(--primary); 
            color: var(--primary); 
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.1);
            background: #F8F9FF;
        }
        .pagination .active span { 
            background: var(--primary); 
            color: white; 
            border-color: var(--primary); 
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.25);
        }
        .pagination .disabled span { 
            opacity: 0.5; 
            cursor: not-allowed; 
            background: #F8FAFC;
            box-shadow: none;
        }
    </style>
</head>
<body>

<nav>
    <div class="nav-container">
        <a href="{{ route('home') }}" class="logo">🎟️ {{ config('app.name') }}</a>
        <div class="nav-actions">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Panel Admin</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Buka Event</a>
            @endauth
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-container">
        <h1>Temukan & Kelola <span>Event</span> Dengan Lebih Mudah</h1>
        <p>Platform manajemen tiket terlengkap untuk segala jenis acara. Aman, cepat, dan terpercaya bagi ribuan penyelenggara.</p>
        <div style="display: flex; justify-content: center; gap: 24px;">
            <div style="text-align: left;">
                <div style="font-size: 28px; font-weight: 900; letter-spacing: -1px;">500+</div>
                <div style="font-size: 13px; color: var(--accent); font-weight: 800; text-transform: uppercase;">Event Aktif</div>
            </div>
            <div style="width: 1px; background: rgba(255,255,255,0.2);"></div>
            <div style="text-align: left;">
                <div style="font-size: 28px; font-weight: 900; letter-spacing: -1px;">50k+</div>
                <div style="font-size: 13px; color: var(--accent); font-weight: 800; text-transform: uppercase;">Tiket Terbit</div>
            </div>
        </div>
    </div>
</section>

<div class="search-container">
    <form action="{{ route('home') }}" method="GET" class="search-box">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama event, kategori, atau lokasi...">
        <button type="submit" class="btn btn-primary">Cari Event</button>
    </form>
</div>

<section class="trust-section">
    <div class="trust-card">
        <span class="trust-icon">🛡️</span>
        <h3>Sistem Aman</h3>
        <p>Keamanan data dan transaksi adalah prioritas kami dengan enkripsi standar industri terkini.</p>
    </div>
    <div class="trust-card">
        <span class="trust-icon">⚡</span>
        <h3>Proses Instan</h3>
        <p>E-Tiket langsung dikirim ke email peserta dalam hitungan detik setelah verifikasi pembayaran.</p>
    </div>
    <div class="trust-card">
        <span class="trust-icon">📊</span>
        <h3>Analitik Cerdas</h3>
        <p>Pantau penjualan, statistik peserta, dan log scan secara real-time dari dashboard terpusat.</p>
    </div>
</section>

<div class="section-header">
    <h2>Event Mendatang</h2>
    <p style="color: var(--muted); font-size: 15px; font-weight: 700;">{{ $events->count() }} Event ditemukan</p>
</div>

<div class="container">
    @if(session('success') || session('error'))
        <div style="margin-bottom: 32px;">
            @if(session('success'))
                <div style="background: #D1FAE5; color: #065F46; padding: 20px; border-radius: 20px; font-weight: 700; border: 1px solid #A7F3D0;">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="background: #FEE2E2; color: #991B1B; padding: 20px; border-radius: 20px; font-weight: 700; border: 1px solid #FECACA;">❌ {{ session('error') }}</div>
            @endif
        </div>
    @endif

    <div class="event-grid">
        @forelse($events as $event)
        <div class="event-card">
            <div class="event-image-wrap">
                <img src="{{ $event->image_url }}" 
                     alt="Banner {{ $event->name }}" 
                     class="event-image" 
                     loading="lazy" 
                     width="400" height="225"
                     onerror="this.onerror=null;this.src='{{ asset('images/placeholder-event.png') }}';">
                <div class="event-badge">{{ $event->is_free ? '🎁 GRATIS' : '🎟️ BERBAYAR' }}</div>
            </div>
            
            <div class="event-content">
                <div class="event-date">📅 {{ $event->event_date->isoFormat('D MMM YYYY') }} • {{ $event->event_date->format('H:i') }}</div>
                <h3 class="event-title">{{ $event->name }}</h3>
                <div class="event-location">📍 {{ $event->location ?? 'Online / Lokasi Belum Ditentukan' }}</div>
                
                <div class="event-footer">
                    <div>
                        @if($event->is_free)
                            <div class="event-price free">GRATIS</div>
                        @else
                            <div class="event-price">Rp {{ number_format($event->price, 0, ',', '.') }}</div>
                        @endif
                        <div style="font-size: 13px; color: var(--muted); font-weight: 700; margin-top: 6px;">Sisa {{ $event->getRemainingQuota() }} Slot</div>
                    </div>
                    
                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline" style="padding: 12px 20px;">Detail</a>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 100px 0;">
            <div style="font-size: 5rem; margin-bottom: 24px;">🔍</div>
            <h3 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 12px;">Event Tidak Ditemukan</h3>
            <p style="color: var(--muted); font-size: 1.1rem; font-weight: 500;">Coba cari dengan kata kunci lain atau lihat semua koleksi kami.</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top: 32px;">Lihat Semua Event</a>
        </div>
        @endforelse
    </div>

    <div class="pagination-wrapper">
        {{ $events->links('vendor.pagination.custom') }}
    </div>
</div>

<footer>
    <div class="footer-container">
        <div>
            <a href="#" class="footer-logo">🎟️ {{ config('app.name') }}</a>
            <p style="line-height: 1.8; font-size: 15px;">Platform terdepan untuk manajemen tiket dan registrasi event di Indonesia. Memberikan pengalaman pendaftaran yang mulus bagi peserta dan penyelenggara dengan integrasi teknologi terkini.</p>
        </div>
        <div>
            <h4 class="footer-title">Navigasi</h4>
            <ul class="footer-links">
                <li><a href="{{ route('home') }}">Beranda</a></li>
                <li><a href="#">Cari Event</a></li>
                <li><a href="{{ route('register') }}">Buka Event</a></li>
                <li><a href="{{ route('login') }}">Masuk Log</a></li>
            </ul>
        </div>
        <div>
            <h4 class="footer-title">Informasi</h4>
            <ul class="footer-links">
                <li><a href="#">Pusat Bantuan</a></li>
                <li><a href="#">Syarat & Ketentuan</a></li>
                <li><a href="#">Kebijakan Privasi</a></li>
                <li><a href="#">Panduan Panitia</a></li>
            </ul>
        </div>
        <div>
            <h4 class="footer-title">Support</h4>
            <p style="font-size: 15px; margin-bottom: 16px; font-weight: 500;">📧 support@tiketevent.com</p>
            <p style="font-size: 15px; margin-bottom: 16px; font-weight: 500;">📞 +62 812-3456-7890</p>
            <p style="font-size: 15px; font-weight: 500;">📍 Jakarta, Indonesia</p>
        </div>
    </div>
    <div class="copyright">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. Managed with ❤️ in Indonesia.
    </div>
</footer>

</body>
</html>
