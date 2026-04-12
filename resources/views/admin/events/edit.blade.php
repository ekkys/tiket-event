<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - {{ config('app.name') }}</title>
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
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background: var(--sidebar); border-right: 1px solid #E2E8F0; padding: 32px 24px; display: flex; flex-direction: column; }
        .logo { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-bottom: 48px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; text-decoration: none; color: var(--muted); font-weight: 600; margin-bottom: 8px; transition: 0.2s; }
        .nav-link.active { background: var(--primary); color: white; }
        .nav-link:hover:not(.active) { background: #F1F5F9; color: var(--text); }

        .main-content { flex: 1; padding: 40px; }
        .header { margin-bottom: 32px; }
        .header h1 { font-size: 1.75rem; font-weight: 800; }

        .card { background: white; border-radius: 24px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); max-width: 600px; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
        input, select, textarea { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; outline: none; }
        input:focus { border-color: var(--primary); }

        .btn { padding: 12px 24px; border-radius: 12px; border: none; font-family: inherit; font-weight: 700; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--muted); text-decoration: none; }

        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .checkbox-group { display: flex; align-items: center; gap: 8px; }
        .checkbox-group input { width: auto; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">🎟️ Admin</div>
    <nav style="flex: 1;">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">📊 Dashboard</a>
        <a href="{{ route('admin.events') }}" class="nav-link active">📅 Events</a>
        <a href="{{ route('admin.registrations') }}" class="nav-link">👥 Peserta</a>
        <a href="{{ route('admin.scan-logs') }}" class="nav-link">📋 Log Scan</a>
    </nav>
    <div style="margin-top: auto; padding-top: 24px; border-top: 1px solid #F1F5F9;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link" style="width: 100%; border: none; background: none; cursor: pointer; color: #DC2626;">🚪 Keluar</button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="header">
        <h1>Edit Event</h1>
    </div>

    <div class="card">
        <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Nama Event</label>
                <input type="text" name="name" value="{{ $event->name }}" required>
            </div>

            <div class="form-group">
                <label>Gambar Banner Event</label>
                @if($event->image_path)
                    <div style="margin-bottom: 12px;">
                        <img src="{{ asset('storage/' . $event->image_path) }}" alt="Preview" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border);">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*">
                <p style="font-size: 11px; color: var(--muted); margin-top: 4px;">Pilih file baru untuk mengganti gambar.</p>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Tanggal Event</label>
                    <input type="datetime-local" name="event_date" value="{{ $event->event_date->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="form-group">
                    <label>Kuota Peserta</label>
                    <input type="number" name="quota" value="{{ $event->quota }}" required>
                </div>
            </div>

            <div class="form-group">
                <label>Lokasi / Link Online</label>
                <input type="text" name="location" value="{{ $event->location }}">
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Harga Tiket (Rp)</label>
                    <input type="number" name="price" value="{{ $event->price }}">
                </div>
                <div class="form-group" style="display:flex; flex-direction:column; justify-content:center; gap:10px; padding-top:20px;">
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_free" value="1" {{ $event->is_free ? 'checked' : '' }}> <span>Event Gratis</span>
                    </label>
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_active" value="1" {{ $event->is_active ? 'checked' : '' }}> <span>Aktif / Dibuka</span>
                    </label>
                </div>
            </div>

            <div style="margin-top: 32px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">💾 Update Event</button>
                <a href="{{ route('admin.events') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
