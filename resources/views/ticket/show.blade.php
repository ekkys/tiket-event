{{-- FILE: resources/views/ticket/show.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket - {{ $ticket->registration->full_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0F0A2A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .ticket-wrapper {
            max-width: 420px;
            width: 100%;
        }

        .ticket {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05);
        }

        .ticket-header {
            background: linear-gradient(135deg, #5C3BFE 0%, #8B5CF6 100%);
            color: white;
            padding: 32px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .ticket-header::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 140px; height: 140px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }

        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -30px; left: -20px;
            width: 100px; height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .ticket-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.7;
            margin-bottom: 8px;
        }

        .event-name {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.2;
            position: relative;
            z-index: 1;
        }

        .event-meta {
            margin-top: 12px;
            font-size: 13px;
            opacity: 0.85;
            position: relative;
            z-index: 1;
        }

        /* Notched divider */
        .ticket-divider {
            display: flex;
            align-items: center;
            background: white;
            position: relative;
        }

        .notch {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #0F0A2A;
            flex-shrink: 0;
        }

        .perforation {
            flex: 1;
            height: 2px;
            border-top: 2px dashed #E2DEF9;
            margin: 0 4px;
        }

        .ticket-body {
            padding: 28px;
        }

        .attendee-name {
            font-size: 1.3rem;
            font-weight: 800;
            color: #1A1033;
            margin-bottom: 4px;
        }

        .attendee-sub {
            font-size: 13px;
            color: #7B7A8E;
            margin-bottom: 20px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .detail-item {
            background: #F4F2FF;
            border-radius: 12px;
            padding: 12px;
        }

        .detail-key {
            font-size: 11px;
            font-weight: 700;
            color: #7B7A8E;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .detail-val {
            font-size: 14px;
            font-weight: 700;
            color: #1A1033;
        }

        /* QR Code section */
        .qr-section {
            background: #F9F8FF;
            border: 2px dashed #C4B8F8;
            border-radius: 20px;
            padding: 24px;
            text-align: center;
        }

        .qr-section img {
            width: 200px;
            height: 200px;
            border-radius: 12px;
        }

        .qr-hint {
            font-size: 12px;
            color: #7B7A8E;
            margin-top: 12px;
            font-weight: 500;
        }

        .ticket-code {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            color: #5C3BFE;
            letter-spacing: 1px;
            margin-top: 8px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #D1FAE5;
            color: #065F46;
            border-radius: 100px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        /* Action buttons */
        .actions {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #5C3BFE, #8B5CF6);
            color: white;
            box-shadow: 0 6px 20px rgba(92,59,254,0.3);
        }

        .btn-secondary {
            background: #F4F2FF;
            color: #5C3BFE;
        }

        .warning-used {
            background: #FFF3CD;
            border: 1px solid #FFEAA7;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            color: #856404;
            font-weight: 600;
            text-align: center;
            margin-top: 12px;
        }
    </style>
</head>
<body>
<div class="ticket-wrapper">
    <div class="ticket">
        <div class="ticket-header">
            <div class="ticket-label">✨ E-Ticket</div>
            <div class="event-name">{{ $ticket->registration->event->name }}</div>
            <div class="event-meta">
                📅 {{ $ticket->registration->event->event_date->isoFormat('dddd, D MMMM Y') }}
                @if($ticket->registration->event->location)
                    <br>📍 {{ $ticket->registration->event->location }}
                @endif
            </div>
        </div>

        <div class="ticket-divider">
            <div class="notch"></div>
            <div class="perforation"></div>
            <div class="notch"></div>
        </div>

        <div class="ticket-body">
            <div class="status-badge">
                ✅ Tiket Valid
            </div>

            <div class="attendee-name">{{ $ticket->registration->full_name }}</div>
            <div class="attendee-sub">
                {{ $ticket->registration->email }}
                @if($ticket->registration->institution)
                    · {{ $ticket->registration->institution }}
                @endif
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-key">No. Tiket</div>
                    <div class="detail-val">{{ $ticket->registration->registration_code }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-key">Status Bayar</div>
                    <div class="detail-val">
                        {{ $ticket->registration->payment_status === 'free' ? '🎁 Gratis' : '✅ Lunas' }}
                    </div>
                </div>
                @if($ticket->registration->amount_paid > 0)
                <div class="detail-item">
                    <div class="detail-key">Dibayar</div>
                    <div class="detail-val">{{ $ticket->registration->formatted_amount }}</div>
                </div>
                @endif
                <div class="detail-item">
                    <div class="detail-key">Phone</div>
                    <div class="detail-val">{{ $ticket->registration->phone }}</div>
                </div>
            </div>

            <div class="qr-section">
                @if($ticket->qr_code_path)
                    <img src="{{ Storage::url($ticket->qr_code_path) }}" alt="QR Code Tiket">
                @else
                    <div style="width:200px;height:200px;background:#eee;display:flex;align-items:center;justify-content:center;border-radius:12px;margin:0 auto;">
                        <span style="font-size:3rem;">⏳</span>
                    </div>
                @endif
                <div class="qr-hint">Tunjukkan QR code ini ke petugas saat masuk</div>
                <div class="ticket-code">{{ $ticket->registration->registration_code }}</div>

                @if($ticket->is_used)
                    <div class="warning-used">
                        ⚠️ Tiket ini sudah digunakan pada<br>
                        {{ $ticket->used_at?->isoFormat('dddd, D MMM Y [pukul] HH:mm') }}
                    </div>
                @endif
            </div>

            <div class="actions">
                <a href="{{ route('ticket.download', $ticket->token) }}" class="btn btn-primary">
                    📥 Download QR Code
                </a>
                <button class="btn btn-secondary" onclick="window.print()">
                    🖨️ Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </div>

    <p style="color: rgba(255,255,255,0.4); font-size: 12px; text-align: center; margin-top: 16px;">
        Screenshot atau cetak tiket ini sebagai backup
    </p>
</div>
</body>
</html>
