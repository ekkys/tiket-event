<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name }} - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A;
            /* Navy Blue */
            --primary-light: #2563EB;
            --accent: #60A5FA;
            --dark: #0F172A;
            --bg: #F8FAFC;
            --text: #1E293B;
            --muted: #64748B;
            --border: #E2E8F0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .nav-back {
            margin-bottom: 32px;
        }

        .nav-back a {
            text-decoration: none;
            color: var(--muted);
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .nav-back a:hover {
            color: var(--primary);
            transform: translateX(-4px);
        }

        .event-header {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            margin-bottom: 40px;
            border: 1px solid #E2E8F0;
        }

        .event-banner {
            width: 100%;
            height: 400px;
            object-fit: cover;
            cursor: zoom-in;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .event-banner:hover {
            opacity: 0.95;
        }

        .event-banner-placeholder {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #1E3A8A, #0F172A);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: clamp(1.5rem, 5vw, 2.5rem);
            text-align: center;
            padding: 40px;
        }

        .event-header-content {
            padding: 40px;
        }

        .event-date-badge {
            display: inline-block;
            background: #EEF2FF;
            color: var(--primary);
            padding: 8px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .event-title {
            font-size: clamp(1.8rem, 5vw, 3rem);
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.1;
            color: var(--dark);
            letter-spacing: -1.5px;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 600;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        .section {
            background: white;
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.06);
            margin-bottom: 32px;
            border: 1px solid #F1F5F9;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #F1F5F9;
        }

        .content-body {
            color: #4B5563;
            white-space: pre-line;
            font-size: 16px;
            line-height: 1.8;
        }

        .highlights-list {
            list-style: none;
            display: grid;
            /* gap: 5px; */
        }

        .highlights-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            font-weight: 700;
            color: #1F2937;
            /* background: #f5f6f7ff; */
            padding: 12px 16px;
            /* border-radius: 10px; */
            /* border: 1px solid #f8f8f8ff; */
        }

        .highlights-item::before {
            content: '✨';
            font-size: 18px;
        }

        .sidebar-card {
            position: sticky;
            top: 40px;
        }

        .price-card {
            background: white;
            border-radius: 32px;
            padding: 40px;
            border: 1px solid var(--border);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.1);
        }

        .price-label {
            color: var(--muted);
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .price-value {
            font-size: 2.25rem;
            font-weight: 900;
            margin-bottom: 32px;
            letter-spacing: -1px;
            color: var(--dark);
        }

        .price-value.free {
            color: var(--success);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 800;
            text-align: center;
            font-size: 18px;
            transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.4);
        }

        .btn-disabled {
            background: #F3F4F6;
            color: #9CA3AF;
            cursor: not-allowed;
        }

        .status-badge {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 800;
            color: var(--muted);
            background: #F8FAFC;
            padding: 8px;
            border-radius: 100px;
        }

        /* Lightbox Modal */
        .img-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding: 40px 20px;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            overflow: auto;
            text-align: center;
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 100%;
            max-height: 85vh;
            border-radius: 24px;
            box-shadow: 0 0 60px rgba(0, 0, 0, 0.5);
            animation: zoom 0.4s cubic-bezier(0.2, 0, 0, 1);
        }

        @keyframes zoom {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .modal-actions {
            margin-top: 32px;
            display: flex;
            justify-content: center;
            gap: 16px;
        }

        .btn-download {
            background: white;
            color: var(--dark);
            padding: 14px 28px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 800;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.2s;
        }

        .btn-download:hover {
            transform: scale(1.05);
        }

        @media (max-width: 992px) {
            .grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .sidebar-card {
                position: static;
            }

            .event-banner {
                height: 300px;
            }

            .event-banner-placeholder {
                height: 300px;
            }
        }

        @media (max-width: 640px) {
            .container {
                padding: 24px 16px;
            }

            .event-header-content {
                padding: 32px 24px;
            }

            .section {
                padding: 32px 24px;
            }

            .price-card {
                padding: 32px 24px;
            }

            .event-title {
                font-size: 1.8rem;
            }

            .event-meta {
                gap: 16px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="nav-back">
            <a href="{{ route('home') }}">← Kembali ke Beranda</a>
        </div>

        @if(session('success'))
            <div
                style="background: #D1FAE5; color: #065F46; padding: 20px; border-radius: 20px; margin-bottom: 32px; font-weight: 700; border: 1px solid #A7F3D0;">
                ✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div
                style="background: #FEE2E2; color: #991B1B; padding: 20px; border-radius: 20px; margin-bottom: 32px; font-weight: 700; border: 1px solid #FECACA;">
                ❌ {{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div
                style="background: #DBEAFE; color: #1E40AF; padding: 20px; border-radius: 20px; margin-bottom: 32px; font-weight: 700; border: 1px solid #BFDBFE;">
                ℹ️ {{ session('info') }}</div>
        @endif

        <div class="event-header">
            @if($event->image_path)
                <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->name }}" class="event-banner"
                    onclick="openModal()">
            @else
                <div class="event-banner-placeholder">{{ $event->name }}</div>
            @endif

            <div class="event-header-content">
                <div class="event-date-badge">Tersisa {{ number_format($event->getRemainingQuota()) }} Tiket</div>
                <h1 class="event-title">{{ $event->name }}</h1>
                <div class="event-meta">
                    <div class="meta-item">📅 {{ $event->event_date->isoFormat('dddd, D MMMM Y') }}</div>
                    <div class="meta-item">⏰ {{ $event->event_date->format('H:i') }} WIB</div>
                    <div class="meta-item">
                        📍 
                        @if($event->location_link)
                            <a href="{{ $event->location_link }}" target="_blank" style="color: inherit; text-decoration: underline;">{{ $event->location_name ?: ($event->location ?? 'Lokasi') }}</a>
                        @else
                            {{ $event->location_name ?: ($event->location ?? 'Online / Lokasi Belum Ditentukan') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="main-content">
                @if($event->highlights)
                    <div class="section">
                        <h2 class="section-title">Sorotan Acara</h2>
                        <ul class="highlights-list">
                            @foreach(explode("\n", $event->highlights) as $highlight)
                                @if(trim($highlight))
                                    <li class="highlights-item">{{ trim($highlight, "- \t\n\r\0\x0B") }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="section">
                    <h2 class="section-title">Deskripsi Event</h2>
                    <div class="content-body">
                        {{ $event->description ?: 'Penyelenggara belum memberikan deskripsi lengkap untuk event ini.' }}
                    </div>
                </div>

                @if($event->terms_and_conditions)
                    <div class="section">
                        <h2 class="section-title">Syarat & Ketentuan</h2>
                        <div class="content-body" style="font-size: 14px; opacity: 0.85;">
                            {{ $event->terms_and_conditions }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="sidebar">
                <div class="sidebar-card">
                    <div class="section" style="padding: 32px; margin-bottom: 24px;">
                        <h3
                            style="font-size: 14px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                            🏢 Penyelenggara
                        </h3>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div
                                style="width: 48px; height: 48px; background: var(--primary); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px; flex-shrink: 0;">
                                {{ substr($event->user->name ?? 'P', 0, 1) }}
                            </div>
                            <div style="overflow: hidden;">
                                <div
                                    style="font-weight: 800; color: var(--dark); font-size: 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $event->user->name ?? 'Penyelenggara' }}
                                </div>
                                <div style="font-size: 13px; color: var(--muted); font-weight: 600;">
                                    ✉️ {{ $event->user->email ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="price-card">
                        <div class="price-label">Harga Tiket</div>
                        @if($event->is_free)
                            <div class="price-value free">GRATIS</div>
                        @else
                            <div class="price-value">Rp {{ number_format($event->price, 0, ',', '.') }}</div>
                        @endif

                        @php $status = $event->getBookingStatus(); @endphp

                        @if($status === 'Buka')
                            <a href="{{ route('registration.form.specific', $event->id) }}" class="btn btn-primary">Daftar
                                Sekarang</a>
                        @else
                            <button class="btn btn-disabled" disabled>Pendaftaran {{ $status }}</button>
                        @endif

                        @if($event->booking_ends_at && $status === 'Buka')
                            <div class="status-badge">⏰ Berakhir {{ $event->booking_ends_at->diffForHumans() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($event->image_path)
        <div id="imageModal" class="img-modal">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="imgFull" src="{{ asset('storage/' . $event->image_path) }}">
            <div class="modal-actions">
                <a href="{{ asset('storage/' . $event->image_path) }}"
                    download="{{ \Illuminate\Support\Str::slug($event->name) }}.jpg" class="btn-download">
                    📥 Download Banner
                </a>
            </div>
        </div>

        <script>
            function openModal() {
                document.getElementById("imageModal").style.display = "block";
                document.body.style.overflow = "hidden"; // Disable scroll
            }

            function closeModal() {
                document.getElementById("imageModal").style.display = "none";
                document.body.style.overflow = "auto"; // Enable scroll
            }

            // Close modal when clicking outside the image
            window.onclick = function (event) {
                let modal = document.getElementById("imageModal");
                if (event.target == modal) {
                    closeModal();
                }
            }
        </script>
    @endif

</body>

</html>