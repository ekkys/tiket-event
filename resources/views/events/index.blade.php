<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --secondary: #6366f1;
            --bg: #F8F9FD;
            --text: #1A1033;
            --muted: #7B7A8E;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
        
        .header { text-align: center; margin-bottom: 60px; }
        .header h1 { font-size: 3rem; font-weight: 800; margin-bottom: 16px; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header p { font-size: 1.125rem; color: var(--muted); max-width: 600px; margin: 0 auto; }

        .event-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 32px; }
        
        .event-card { background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: 0.3s; border: 1px solid #E2E8F0; display: flex; flex-direction: column; }
        .event-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(92, 59, 254, 0.1); border-color: var(--primary); }
        
        .event-image { width: 100%; height: 220px; object-fit: cover; }
        .event-image-placeholder { width: 100%; height: 220px; background: linear-gradient(135deg, #F1F5F9, #E2E8F0); display: flex; align-items: center; justify-content: center; color: var(--muted); font-weight: 700; }
        
        .event-content { padding: 32px; flex: 1; display: flex; flex-direction: column; }
        .event-date { font-size: 13px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .event-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 12px; color: var(--text); }
        .event-location { font-size: 14px; color: var(--muted); margin-bottom: 24px; display: flex; align-items: center; gap: 8px; }
        
        .event-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 24px; border-top: 1px solid #F1F5F9; }
        .event-price { font-size: 1.25rem; font-weight: 800; color: var(--text); }
        .event-price.free { color: #00C48C; }
        
        .btn { padding: 12px 24px; border-radius: 14px; text-decoration: none; font-weight: 700; font-size: 14px; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #4B2EE0; }

        .badge-full { background: #FEE2E2; color: #DC2626; padding: 4px 12px; border-radius: 100px; font-size: 11px; font-weight: 800; }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: flex-end; gap: 16px; margin-bottom: 20px;">
        @auth
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" style="background: white; border: 1px solid var(--primary); color: var(--primary);">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-secondary" style="background: white; border: 1px solid var(--primary); color: var(--primary);">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Buka Event</a>
        @endauth
    </div>

    <div class="header" style="margin-bottom: 40px;">
        <h1>Temukan Event Seru</h1>
        <p>Jelajahi berbagai event menarik dan amankan tiket Anda sekarang juga sebelum kehabisan!</p>
    </div>

    <div style="max-width: 600px; margin: 0 auto 60px;">
        <form action="{{ route('home') }}" method="GET" style="display: flex; gap: 12px;">
            <input type="text" name="q" value="{{ request('q') }}" 
                   placeholder="Cari nama event atau lokasi..." 
                   style="flex: 1; padding: 16px 24px; border-radius: 16px; border: 1px solid #E2E8F0; font-family: inherit; font-size: 16px; outline: none; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <button type="submit" class="btn btn-primary" style="padding: 16px 32px;">Cari</button>
        </form>
        @if(request('q'))
            <div style="margin-top: 16px; text-align: center; color: var(--muted); font-size: 14px;">
                Menampilkan hasil pencarian untuk: <strong>"{{ request('q') }}"</strong> 
                <a href="{{ route('home') }}" style="color: var(--primary); margin-left: 8px; text-decoration: none;">Bersihkan</a>
            </div>
        @endif
    </div>

    <div class="event-grid">
        @foreach($events as $event)
        <div class="event-card">
            @if($event->image_path)
                <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->name }}" class="event-image">
            @else
                <div class="event-image-placeholder">No Image Provided</div>
            @endif
            
            <div class="event-content">
                <div class="event-date">📅 {{ $event->event_date->format('d M Y') }} • {{ $event->event_date->format('H:i') }}</div>
                <h2 class="event-title">{{ $event->name }}</h2>
                <div class="event-location">📍 {{ $event->location ?? 'Online / TBD' }}</div>
                
                <div class="event-footer">
                    <div>
                        @if($event->is_free)
                            <div class="event-price free">GRATIS</div>
                        @else
                            <div class="event-price">Rp {{ number_format($event->price, 0, ',', '.') }}</div>
                        @endif
                        <div style="font-size: 12px; color: var(--muted); font-weight: 600;">Sisa: {{ $event->getRemainingQuota() }} Tiket</div>
                    </div>
                    
                    @if($event->isQuotaAvailable())
                        <a href="{{ route('registration.form.specific', $event->id) }}" class="btn btn-primary">Daftar Sekarang</a>
                    @else
                        <span class="badge-full">KUOTA PENUH</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

</body>
</html>
