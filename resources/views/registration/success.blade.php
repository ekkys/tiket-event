<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - {{ $registration->event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A;
            --primary-light: #3B82F6;
            --bg-light: #F8FAFC;
            --text-dark: #0F172A;
            --text-muted: #64748B;
            --success: #10B981;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: var(--bg-light);
            background-image:
                radial-gradient(circle at 10% 20%, rgba(30, 58, 138, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.05) 0%, transparent 20%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            margin: 0;
            color: var(--text-dark);
        }

        .card {
            background: white;
            border-radius: 32px;
            padding: 48px 32px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            animation: fadeInScale 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: #D1FAE5;
            color: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 32px;
            animation: bounceIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        p {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .event-name-tag {
            color: var(--primary);
            font-weight: 800;
            background: #EEF2FF;
            padding: 4px 12px;
            border-radius: 8px;
            display: block;
            margin: 12px auto;
            width: fit-content;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            text-decoration: none;
            padding: 18px 36px;
            border-radius: 18px;
            font-weight: 800;
            font-size: 1rem;
            transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.3);
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(30, 58, 138, 0.4);
        }

        .loading-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--primary);
            font-weight: 700;
            font-size: 14px;
        }

        .dot-flashing {
            position: relative;
            width: 10px;
            height: 10px;
            border-radius: 5px;
            background-color: var(--primary);
            color: var(--primary);
            animation: dotFlashing 1s infinite linear alternate;
            animation-delay: .5s;
        }

        @keyframes dotFlashing {
            0% {
                background-color: var(--primary);
            }

            50%,
            100% {
                background-color: #EEF2FF;
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 40px 24px;
            }

            h1 {
                font-size: 1.5rem;
            }

            p {
                font-size: 0.95rem;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon-wrapper">✅</div>
        <h1>Pendaftaran Berhasil!</h1>
        <p>
            Pendaftaran Anda untuk <span class="event-name-tag">{{ $registration->event->name }}</span> telah berhasil
            kami terima.
            <br><br>
            @if($registration->payment_status === 'free')
                Tiket digital Anda sedang dibuat. Silakan cek email Anda secara berkala atau klik tombol di bawah untuk
                melihat tiket langsung.
            @else
                Silakan selesaikan pembayaran sesuai instruksi yang telah dikirim agar tiket dapat segera kami terbitkan.
            @endif
        </p>

        @if($registration->ticket)
            <a href="{{ route('ticket.show', $registration->ticket->token) }}" class="btn">🎟️ Lihat Tiket Digital</a>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (!urlParams.has('downloaded')) {
                        const downloadUrl = "{{ route('ticket.download', $registration->ticket->token) }}";
                        // Beri jeda sedikit agar user bisa melihat pesan berhasil
                        setTimeout(() => {
                            window.location.href = downloadUrl;
                            // Tandai sudah download agar tidak berulang saat refresh manual
                            urlParams.set('downloaded', '1');
                            const newUrl = window.location.pathname + '?' + urlParams.toString();
                            window.history.replaceState(null, '', newUrl);
                        }, 1500);
                    }
                });
            </script>
        @else
            <div class="loading-text">
                <span>Sedang menyiapkan tiket Anda</span>
                <div class="dot-flashing"></div>
            </div>
            <script>setTimeout(() => window.location.reload(), 3000);</script>
        @endif
    </div>
</body>

</html>