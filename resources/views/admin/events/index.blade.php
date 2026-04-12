<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --bg: #F8F9FD;
            --sidebar: #FFFFFF;
            --card: #FFFFFF;
            --text: #1A1033;
            --muted: #7B7A8E;
            --success: #00C48C;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background: var(--sidebar); border-right: 1px solid #E2E8F0; padding: 32px 24px; display: flex; flex-direction: column; }
        .logo { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-bottom: 48px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; text-decoration: none; color: var(--muted); font-weight: 600; margin-bottom: 8px; transition: 0.2s; }
        .nav-link.active { background: var(--primary); color: white; }
        .nav-link:hover:not(.active) { background: #F1F5F9; color: var(--text); }

        .main-content { flex: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .header h1 { font-size: 1.75rem; font-weight: 800; }

        .card { background: white; border-radius: 24px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        
        .btn { padding: 10px 20px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 700; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 16px; border-bottom: 2px solid #F1F5F9; font-size: 13px; font-weight: 700; color: var(--muted); }
        td { padding: 16px; border-bottom: 1px solid #F1F5F9; font-size: 14px; }
        
        .badge { display: inline-block; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge-active { background: #DCFCE7; color: #166534; }
        .badge-inactive { background: #FEE2E2; color: #991B1B; }
        .badge-free { background: #DBEAFE; color: #1E40AF; }

        .alert { padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; font-size: 14px; }
        .alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
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
        <a href="{{ route('scanner.index') }}" class="nav-link">📷 Scanner</a>
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
        <h1>Manajemen Event</h1>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">➕ Tambah Event</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nama Event</th>
                    <th>Tanggal</th>
                    <th>Harga</th>
                    <th>Kuota</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>
                        @if($event->image_path)
                            <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->name }}" style="width: 50px; height: 35px; border-radius: 6px; object-fit: cover;">
                        @else
                            <div style="width: 50px; height: 35px; background: #EEE; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">No Image</div>
                        @endif
                    </td>
                    <td style="font-weight:600;">{{ $event->name }}</td>
                    <td>{{ $event->event_date->format('d M Y') }}</td>
                    <td>
                        @if($event->is_free)
                            <span class="badge badge-free">Gratis</span>
                        @else
                            Rp {{ number_format($event->price, 0, ',', '.') }}
                        @endif
                    </td>
                    <td>{{ $event->registered_count }} / {{ $event->quota }}</td>
                    <td>
                        <span class="badge badge-{{ $event->is_active ? 'active' : 'inactive' }}">
                            {{ $event->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-success btn-sm">✏️ Edit</a>
                            <form action="{{ route('admin.events.delete', $event->id) }}" method="POST" onsubmit="return confirm('Hapus event ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:#FEE2E2; color:#991B1B;">🗑️ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
