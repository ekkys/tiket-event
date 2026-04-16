<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Scan - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --bg: #F8F9FD;
            --sidebar: #FFFFFF;
            --card: #FFFFFF;
            --text: #1A1033;
            --muted: #7B7A8E;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background: var(--sidebar); border-right: 1px solid #E2E8F0; padding: 32px 24px; display: flex; flex-direction: column; transition: transform 0.3s ease; z-index: 1000; }
        .logo { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-bottom: 48px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; text-decoration: none; color: var(--muted); font-weight: 600; margin-bottom: 8px; transition: 0.2s; }
        .nav-link.active { background: var(--primary); color: white; }
        .nav-link:hover:not(.active) { background: #F1F5F9; color: var(--text); }

        .main-content { flex: 1; padding: 40px; width: 100%; transition: all 0.3s ease; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .header h1 { font-size: 1.75rem; font-weight: 800; }

        .card { background: white; border-radius: 24px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }

        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { text-align: left; padding: 12px 16px; border-bottom: 2px solid #F1F5F9; font-size: 13px; font-weight: 700; color: var(--muted); }
        td { padding: 16px; border-bottom: 1px solid #F1F5F9; font-size: 14px; }
        
        .badge { display: inline-block; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge-success { background: #DCFCE7; color: #166534; }
        .badge-error { background: #FEE2E2; color: #991B1B; }

        .pagination { margin-top: 24px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .pagination a, .pagination span { 
            padding: 8px 14px; 
            border-radius: 10px; 
            border: 1px solid #E2E8F0; 
            text-decoration: none; 
            color: var(--text); 
            font-size: 13px; 
            font-weight: 700;
            background: white;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
        }
        .pagination a:hover { 
            border-color: var(--primary); 
            color: var(--primary); 
            background: #F8F7FF;
            transform: translateY(-1px);
        }
        .pagination .active span { 
            background: var(--primary); 
            color: white; 
            border-color: var(--primary); 
            box-shadow: 0 4px 10px rgba(92, 59, 254, 0.2);
        }
        .pagination .disabled span { 
            opacity: 0.5; 
            cursor: not-allowed; 
            background: #F8FAFC;
        }

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
        }

        @media (max-width: 640px) {
            .header h1 { font-size: 1.5rem; }
            .card { padding: 20px; }
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
        <a href="{{ route('admin.events') }}" class="nav-link">📅 Events</a>
        <a href="{{ route('admin.registrations') }}" class="nav-link">👥 Peserta</a>
        <a href="{{ route('admin.scan-logs') }}" class="nav-link active">📋 Log Scan</a>
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
            <h1>Log Scanner</h1>
        </div>

        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px;">
                <form action="{{ route('admin.scan-logs') }}" method="GET" class="search-box" style="flex: 1; position: relative;">
                    <input type="text" name="search" placeholder="Cari nama, token, atau scanner..." value="{{ request('search') }}" style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #E2E8F0; font-family: inherit; outline: none;">
                </form>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peserta</th>
                            <th>NIK</th>
                            <th>Status</th>
                            <th>Pesan</th>
                            <th>Scanner</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}</td>
                            <td>
                                @if($log->ticket?->registration)
                                    <div style="font-weight:700;">{{ $log->ticket->registration->full_name }}</div>
                                    <div style="font-size:11px; color:var(--muted);">{{ $log->token }}</div>
                                @else
                                    <span style="font-family: monospace; font-size: 12px; color: var(--muted);">{{ $log->token }}</span>
                                @endif
                            </td>
                            <td style="font-size: 12px;">{{ $log->ticket?->registration->id_number ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $log->success ? 'success' : 'error' }}">
                                    {{ $log->success ? 'Berhasil' : 'Gagal' }}
                                </span>
                            </td>
                            <td>{{ $log->message }}</td>
                            <td style="font-weight:600;">{{ $log->scanner_name }}</td>
                            <td style="color:var(--muted); font-size:12px;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        <div class="pagination-wrapper">
            {{ $logs->links('vendor.pagination.custom') }}
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
