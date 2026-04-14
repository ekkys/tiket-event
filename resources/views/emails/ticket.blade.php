{{-- FILE: resources/views/emails/ticket.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tiket Digital Anda</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
    body {
        margin: 0;
        padding: 0;
        background-color: #f8fafc;
        font-family: 'Plus Jakarta Sans', 'Helvetica Neue', Arial, sans-serif;
    }
    .wrapper {
        width: 100%;
        table-layout: fixed;
        background-color: #f8fafc;
        padding-bottom: 60px;
    }
    .main {
        background-color: #ffffff;
        margin: 0 auto;
        width: 100%;
        max-width: 600px;
        border-spacing: 0;
        color: #1e293b;
        border-radius: 24px;
        overflow: hidden;
        margin-top: 40px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }
    .header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        padding: 48px 40px;
        text-align: center;
    }
    .header h1 {
        color: #ffffff;
        font-size: 28px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.025em;
    }
    .header p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 16px;
        margin-top: 8px;
    }
    .content {
        padding: 40px;
    }
    .greeting {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .message {
        font-size: 15px;
        line-height: 1.6;
        color: #64748b;
        margin-bottom: 32px;
    }
    .ticket-card {
        background-color: #f1f5f9;
        border-radius: 20px;
        padding: 32px;
        text-align: center;
        border: 2px dashed #cbd5e1;
        margin-bottom: 32px;
    }
    .qr-container {
        background: white;
        padding: 16px;
        display: inline-block;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .ticket-code-label {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-top: 24px;
        margin-bottom: 4px;
    }
    .ticket-code {
        font-size: 24px;
        font-weight: 800;
        color: #4f46e5;
        letter-spacing: 0.05em;
    }
    .details-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 32px;
    }
    .details-table td {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .label {
        color: #94a3b8;
        font-weight: 600;
        width: 40%;
    }
    .value {
        color: #1e293b;
        font-weight: 700;
        text-align: right;
    }
    .btn-container {
        text-align: center;
    }
    .btn {
        display: inline-block;
        background-color: #4f46e5;
        color: #ffffff !important;
        text-decoration: none;
        padding: 16px 32px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        transition: background-color 0.2s;
    }
    .footer {
        text-align: center;
        padding: 32px;
        color: #94a3b8;
        font-size: 13px;
    }
</style>
</head>
<body>
    <div class="wrapper">
        <center>
            <table class="main" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td class="header">
                        <h1>{{ $registration->event->name }}</h1>
                        <p>📍 {{ $registration->event->location ?? 'Online Event' }}</p>
                        <p>📅 {{ $registration->event->event_date->isoFormat('dddd, D MMMM Y') }}</p>
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <div class="greeting">Halo, {{ $registration->full_name }}! 👋</div>
                        <div class="message">
                            Terima kasih telah mendaftar! Pendaftaran Anda telah kami terima dan tiket Anda kini telah siap digunakan. Mohon simpan tiket ini dan tunjukkan QR Code di bawah kepada petugas saat registrasi di lokasi acara.
                        </div>

                        <div class="ticket-card">
                            <div class="qr-container">
                                <img src="{{ $message->embed(storage_path('app/public/' . $ticket->qr_code_path)) }}" width="200" height="200" alt="QR Code Ticket">
                            </div>
                            <div class="ticket-code-label">ID Registrasi</div>
                            <div class="ticket-code">{{ $registration->registration_code }}</div>
                        </div>

                        <table class="details-table">
                            <tr>
                                <td class="label">Nama Lengkap</td>
                                <td class="value">{{ $registration->full_name }}</td>
                            </tr>
                            <tr>
                                <td class="label">Email</td>
                                <td class="value">{{ $registration->email }}</td>
                            </tr>
                            <tr>
                                <td class="label">Status Pembayaran</td>
                                <td class="value">
                                    <span style="color: {{ $registration->isPaid() ? '#10b981' : '#f59e0b' }}">
                                        {{ $registration->payment_status === 'free' ? '🎁 GRATIS' : '✅ LUNAS' }}
                                    </span>
                                </td>
                            </tr>
                        </table>

                        <div class="btn-container">
                            <a href="{{ route('ticket.show', $ticket->token) }}" class="btn">
                                🎟️ Lihat Tiket Online
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px;">
                        <div style="border-top: 1px solid #f1f5f9; padding: 24px 0;">
                            <p style="font-size: 12px; color: #94a3b8; line-height: 1.6; margin: 0;">
                                <strong>Penting:</strong> Tiket ini bersifat pribadi. Jangan membagikan QR Code atau link tiket kepada orang lain. Jika Anda memiliki kendala, silakan hubungi tim pendukung kami.
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <p>Anda menerima email ini karena Anda terdaftar pada event {{ $registration->event->name }}.</p>
            </div>
        </center>
    </div>
</body>
</html>
