{{-- FILE: resources/views/emails/ticket.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tiket Anda</title>
</head>
<body style="margin:0;padding:0;background:#F4F2FF;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F2FF;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  {{-- Header --}}
  <tr>
    <td style="background:linear-gradient(135deg,#5C3BFE,#8B5CF6);border-radius:20px 20px 0 0;padding:40px 40px 32px;text-align:center;">
      <p style="color:rgba(255,255,255,0.7);font-size:13px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin:0 0 8px;">🎟️ E-TICKET</p>
      <h1 style="color:white;font-size:28px;font-weight:800;margin:0 0 8px;line-height:1.2;">{{ $registration->event->name }}</h1>
      <p style="color:rgba(255,255,255,0.8);font-size:15px;margin:0;">
        📅 {{ $registration->event->event_date->isoFormat('dddd, D MMMM Y') }}
      </p>
    </td>
  </tr>

  {{-- Body --}}
  <tr>
    <td style="background:white;padding:40px;">

      <p style="color:#1A1033;font-size:16px;margin:0 0 24px;">
        Halo, <strong>{{ $registration->full_name }}</strong>! 👋
      </p>
      <p style="color:#5B5B72;font-size:14px;line-height:1.7;margin:0 0 32px;">
        Pendaftaran kamu <strong>berhasil</strong>! Simpan tiket digital ini dan tunjukkan QR Code-nya ke petugas saat hari acara.
      </p>

      {{-- QR Code --}}
      <div style="text-align:center;background:#F9F8FF;border:2px dashed #C4B8F8;border-radius:16px;padding:32px;margin-bottom:32px;">
        <img src="{{ url(Storage::url($ticket->qr_code_path)) }}" width="200" height="200" style="border-radius:12px;display:block;margin:0 auto;">
        <p style="color:#7B7A8E;font-size:12px;font-weight:700;margin:16px 0 4px;letter-spacing:1px;">KODE TIKET</p>
        <p style="color:#5C3BFE;font-size:16px;font-weight:800;letter-spacing:2px;margin:0;">{{ $registration->registration_code }}</p>
      </div>

      {{-- Details --}}
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F2FF;border-radius:12px;padding:20px;margin-bottom:32px;">
        @foreach([
          ['Nama Lengkap', $registration->full_name],
          ['Email', $registration->email],
          ['No. WhatsApp', $registration->phone],
          ['Status', $registration->payment_status === 'free' ? '🎁 Gratis' : '✅ Sudah Dibayar'],
        ] as [$key, $val])
        <tr>
          <td style="color:#7B7A8E;font-size:13px;font-weight:600;padding:8px 16px;width:40%;">{{ $key }}</td>
          <td style="color:#1A1033;font-size:13px;font-weight:700;padding:8px 16px;">{{ $val }}</td>
        </tr>
        @endforeach
      </table>

      {{-- CTA Button --}}
      <div style="text-align:center;margin-bottom:32px;">
        <a href="{{ route('ticket.show', $ticket->token) }}"
           style="display:inline-block;background:linear-gradient(135deg,#5C3BFE,#8B5CF6);color:white;text-decoration:none;padding:16px 40px;border-radius:12px;font-weight:700;font-size:15px;">
          🎟️ Lihat Tiket Online
        </a>
      </div>

      <p style="color:#9CA3AF;font-size:12px;line-height:1.7;text-align:center;border-top:1px solid #F0EEFF;padding-top:24px;">
        Jika ada pertanyaan, balas email ini atau hubungi panitia.<br>
        Simpan email ini sebagai bukti pendaftaran.
      </p>
    </td>
  </tr>

  {{-- Footer --}}
  <tr>
    <td style="background:#1A1033;border-radius:0 0 20px 20px;padding:24px;text-align:center;">
      <p style="color:rgba(255,255,255,0.5);font-size:12px;margin:0;">
        {{ $registration->event->name }} &copy; {{ date('Y') }}
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
