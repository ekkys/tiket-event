{{-- FILE: resources/views/ticket/show.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket - {{ $ticket->registration->full_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5C3BFE;
            --primary-dark: #4A2ED4;
            --accent: #8B5CF6;
            --bg-dark: #0F0A2A;
            --surface: #FFFFFF;
            --text-dark: #1A1033;
            --text-muted: #7B7A8E;
            --success: #10B981;
            --warning: #F59E0B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-dark);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(92, 59, 254, 0.15) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 20%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            color: white;
        }

        .ticket-wrapper {
            max-width: 440px;
            width: 100%;
            animation: slideUp 0.6s cubic-bezier(0.2, 0, 0, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ticket {
            background: var(--surface);
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.6);
            color: var(--text-dark);
        }

        .ticket-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            padding: 40px 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .ticket-banner-print {
            display: none;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .ticket-banner-print img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ticket-header::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }

        .ticket-label {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            opacity: 0.8;
            margin-bottom: 12px;
            display: block;
        }

        .event-name {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .event-meta {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        /* Notched divider */
        .ticket-divider {
            display: flex;
            align-items: center;
            background: var(--surface);
            position: relative;
            margin: -1px 0;
        }

        .notch {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--bg-dark);
            flex-shrink: 0;
        }

        .notch-left { margin-left: -20px; }
        .notch-right { margin-right: -20px; }

        .perforation {
            flex: 1;
            height: 0;
            border-top: 3px dashed #E2DEF9;
            margin: 0 10px;
            opacity: 0.5;
        }

        .ticket-body {
            padding: 32px;
        }

        .attendee-info {
            text-align: center;
            margin-bottom: 32px;
        }

        .attendee-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .attendee-sub {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 32px;
        }

        .detail-item {
            background: #F7F6FF;
            border-radius: 20px;
            padding: 16px;
            border: 1px solid #EEECFF;
        }

        .detail-key {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .detail-val {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-dark);
        }

        /* QR Code section */
        .qr-section {
            background: #FFFFFF;
            border: 2px dashed #DED8FF;
            border-radius: 28px;
            padding: 32px 24px;
            text-align: center;
            transition: 0.3s;
        }

        .qr-section img {
            width: 200px;
            height: 200px;
            border-radius: 16px;
            margin-bottom: 16px;
        }

        .qr-hint {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .ticket-code {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 15px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 2px;
            background: #EFECFF;
            padding: 6px 16px;
            border-radius: 100px;
            display: inline-block;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #D1FAE5;
            color: #065F46;
            border-radius: 100px;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 24px;
        }

        /* Action buttons */
        .actions {
            margin-top: 32px;
            display: grid;
            gap: 12px;
        }

        .btn {
            padding: 18px;
            border-radius: 20px;
            border: none;
            font-family: inherit;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            box-shadow: 0 10px 25px rgba(92,59,254,0.3);
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(92,59,254,0.4);
        }

        .btn-secondary {
            background: #F4F2FF;
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: #EEECFF;
            transform: translateY(-2px);
        }

        .warning-used {
            background: #FEF3C7;
            border: 1px solid #FCD34D;
            border-radius: 20px;
            padding: 16px;
            font-size: 14px;
            color: #92400E;
            font-weight: 700;
            text-align: center;
            margin-top: 20px;
            line-height: 1.5;
        }

        @media print {
            body { background: white; padding: 0; }
            .ticket { box-shadow: none; border: 1px solid #eee; }
            .actions, .footer-note { display: none; }
            .ticket-banner-print { display: block !important; }
        }

        @media (max-width: 480px) {
            .ticket-header { padding: 32px 24px; }
            .event-name { font-size: 1.5rem; }
            .ticket-body { padding: 24px; }
            .attendee-name { font-size: 1.3rem; }
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="ticket-wrapper">
    <div class="ticket">
        <div class="ticket-banner-print">
            <img src="{{ $ticket->registration->event->image_url }}" alt="Banner Event" onerror="this.onerror=null;this.src='{{ asset('images/placeholder-event.png') }}';">
        </div>
        <div class="ticket-header">
            <span class="ticket-label">E-Ticket Resmi</span>
            <h1 class="event-name">{{ $ticket->registration->event->name }}</h1>
            <div class="event-meta">
                <span>📅 {{ $ticket->registration->event->event_date->isoFormat('dddd, D MMMM Y') }}</span>
                @if($ticket->registration->event->location_name || $ticket->registration->event->location)
                    <span>
                        📍 
                        @if($ticket->registration->event->location_link)
                            <a href="{{ $ticket->registration->event->location_link }}" target="_blank" style="color: inherit; text-decoration: underline;">
                                {{ $ticket->registration->event->location_name ?: $ticket->registration->event->location }}
                            </a>
                        @else
                            {{ $ticket->registration->event->location_name ?: $ticket->registration->event->location }}
                        @endif
                    </span>
                @endif
            </div>
        </div>

        <div class="ticket-divider">
            <div class="notch notch-left"></div>
            <div class="perforation"></div>
            <div class="notch notch-right"></div>
        </div>

        <div class="ticket-body">
            <div class="attendee-info">
                <div class="status-badge">
                    ✅ Tiket Aktif & Valid
                </div>
                <h2 class="attendee-name">{{ $ticket->registration->full_name }}</h2>
                <div class="attendee-sub">
                    {{ $ticket->registration->email }}
                    @if($ticket->registration->institution)
                        · {{ $ticket->registration->institution }}
                    @endif
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-key">Kode Registrasi</div>
                    <div class="detail-val">{{ $ticket->registration->registration_code }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-key">Status Pembayaran</div>
                    <div class="detail-val">
                        {{ $ticket->registration->payment_status === 'free' ? '🎁 Gratis' : '✅ Lunas' }}
                    </div>
                </div>
                @if($ticket->registration->amount_paid > 0)
                <div class="detail-item">
                    <div class="detail-key">Total Bayar</div>
                    <div class="detail-val">{{ $ticket->registration->formatted_amount }}</div>
                </div>
                @endif
                <div class="detail-item">
                    <div class="detail-key">No. Telepon</div>
                    <div class="detail-val">{{ $ticket->registration->phone }}</div>
                </div>
            </div>

            <div class="qr-section">
                <img src="{{ $ticket->qr_url }}" alt="QR Code Tiket" onerror="this.onerror=null;this.src='{{ asset('images/placeholder-event.png') }}';">
                <p class="qr-hint">Scan QR code ini pada pintu masuk area event</p>
                <div class="ticket-code">{{ $ticket->registration->registration_code }}</div>

                @if($ticket->is_used)
                    <div class="warning-used">
                        ⚠️ Tiket ini telah diverifikasi pada<br>
                        <span style="font-size: 12px; opacity: 0.8;">{{ $ticket->used_at?->isoFormat('D MMM Y, HH:mm') }} WIB</span>
                    </div>
                @endif
            </div>

            <div class="actions">
                <a href="{{ route('ticket.download', $ticket->token) }}" class="btn btn-primary">
                    📥 Simpan QR ke Galeri
                </a>
                <button class="btn btn-secondary" onclick="window.print()">
                    🖨️ Cetak atau Simpan PDF
                </button>
            </div>
        </div>
    </div>

    <p class="footer-note" style="color: rgba(255,255,255,0.4); font-size: 13px; text-align: center; margin-top: 24px; font-weight: 500;">
        Simpan screenshot tiket ini untuk akses lebih cepat
    </p>
</div>

<style>
@keyframes pulse {
    0% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: 0.5; }
}
</style>

</body>
</html>
