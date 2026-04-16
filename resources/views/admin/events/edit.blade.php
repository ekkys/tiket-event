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
        
        .sidebar { width: 260px; background: var(--sidebar); border-right: 1px solid #E2E8F0; padding: 32px 24px; display: flex; flex-direction: column; transition: transform 0.3s ease; z-index: 1000; }
        .logo { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-bottom: 48px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; text-decoration: none; color: var(--muted); font-weight: 600; margin-bottom: 8px; transition: 0.2s; }
        .nav-link.active { background: var(--primary); color: white; }
        .nav-link:hover:not(.active) { background: #F1F5F9; color: var(--text); }

        .main-content { flex: 1; padding: 40px; width: 100%; transition: all 0.3s ease; }
        .header { margin-bottom: 32px; }
        .header h1 { font-size: 1.75rem; font-weight: 800; }

        .card { background: white; border-radius: 24px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); max-width: 600px; width: 100%; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
        input, select, textarea { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; outline: none; }
        input:focus { border-color: var(--primary); }

        .btn { padding: 12px 24px; border-radius: 12px; border: none; font-family: inherit; font-weight: 700; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--muted); text-decoration: none; }

        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .checkbox-group { display: flex; align-items: center; gap: 8px; }
        .checkbox-group input { width: auto; }

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
            .card { max-width: 100%; }
        }

        @media (max-width: 640px) {
            .header h1 { font-size: 1.5rem; }
            .row { grid-template-columns: 1fr; gap: 0; }
            .card { padding: 20px; }
            .main-content { padding: 16px; }
            form > div:last-child { flex-direction: column; }
            form > div:last-child .btn { width: 100%; }
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
                    <label>Total Kuota Saat Ini</label>
                    <input type="text" value="{{ $event->quota }}" disabled style="background: #F1F5F9; color: var(--muted); font-weight: 700;">
                </div>
                <div class="form-group">
                    <label>Tambah Kuota (Opsional)</label>
                    <input type="number" name="added_quota" value="0" min="0" placeholder="Misal: 5">
                    <p style="font-size: 11px; color: var(--primary); font-weight: 600; margin-top: 4px;">Angka ini akan ditambahkan ke total sebelumnya.</p>
                </div>
            </div>

            <div class="form-group" style="margin-top: -10px; border-bottom: 1px dashed var(--border); padding-bottom: 20px;">
                <label>Atur Total Kuota Baru (Timpa)</label>
                <input type="number" name="quota" value="{{ $event->quota }}">
                <p style="font-size: 11px; color: var(--muted); margin-top: 4px;">Isi ini HANYA jika ingin mengubah total kuota secara langsung.</p>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Nama Lokasi</label>
                    <input type="text" name="location_name" value="{{ $event->location_name ?: $event->location }}">
                </div>
                <div class="form-group">
                    <label>Link Lokasi (Google Maps / Zoom)</label>
                    <input type="url" name="location_link" value="{{ $event->location_link }}">
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Buka Pendaftaran (Opsional)</label>
                    <input type="datetime-local" name="booking_starts_at" value="{{ $event->booking_starts_at?->format('Y-m-d\TH:i') }}">
                </div>
                <div class="form-group">
                    <label>Tutup Pendaftaran (Opsional)</label>
                    <input type="datetime-local" name="booking_ends_at" value="{{ $event->booking_ends_at?->format('Y-m-d\TH:i') }}">
                </div>
            </div>

            <div class="form-group">
                <label>Keterangan / Deskripsi Event</label>
                <textarea name="description" rows="4">{{ $event->description }}</textarea>
            </div>

            <div class="form-group">
                <label>Sorotan Acara (Highlights)</label>
                <textarea name="highlights" rows="3">{{ $event->highlights }}</textarea>
                <p style="font-size: 11px; color: var(--muted); margin-top: 4px;">Gunakan baris baru atau Markdown untuk list.</p>
            </div>

            <div class="form-group">
                <label>Syarat & Ketentuan</label>
                <textarea name="terms_and_conditions" rows="3">{{ $event->terms_and_conditions }}</textarea>
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
