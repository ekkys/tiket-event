# 🎟️ Sistem Tiket Event - Panduan Setup Lengkap

## Stack Teknologi
- **Laravel 11** (PHP 8.2+)
- **MySQL 8** + Redis (performa tinggi)
- **Midtrans** (QRIS payment gateway)
- **endroid/qr-code** (generate QR Code)
- **Laravel Queues** (async ticket generation & email)
- **Mailtrap / SMTP** (kirim email tiket)

---

## 1. Instalasi Project

```bash
composer create-project laravel/laravel tiket-event
cd tiket-event

# Install packages
composer require endroid/qr-code
composer require midtrans/midtrans-php
composer require laravel/horizon   # monitor queue
composer require spatie/laravel-rate-limited-job-middleware

# Frontend
npm install && npm run build
```

---

## 2. Konfigurasi .env

```env
APP_NAME="Tiket Event"
APP_URL=https://tiket.domainanda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=tiket_event
DB_USERNAME=root
DB_PASSWORD=password

# Redis (WAJIB untuk performa 4000+ user)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Midtrans
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email@gmail.com
MAIL_PASSWORD=app_password

# Event Config
EVENT_NAME="Nama Event Anda"
EVENT_DATE="2025-08-01"
EVENT_PRICE=50000
EVENT_IS_FREE=false
EVENT_QUOTA=4500
```

---

## 3. Jalankan Migration & Seeder

```bash
php artisan migrate
php artisan db:seed --class=EventSeeder

# Buat storage link untuk QR code
php artisan storage:link
```

---

## 4. Jalankan Queue Worker

```bash
# Development
php artisan queue:work redis --queue=tickets,emails,default --tries=3

# Production (gunakan Supervisor)
# Lihat file supervisor.conf yang disertakan
```

---

## 5. Optimasi Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Horizon untuk monitor queue
php artisan horizon
```

---

## 6. Struktur URL

| URL | Fungsi |
|-----|--------|
| `/daftar` | Form pendaftaran |
| `/bayar/{order_id}` | Halaman pembayaran QRIS |
| `/tiket/{token}` | Tampilkan tiket + QR code |
| `/cek-tiket` | Cek status tiket (publik) |
| `/scan` | Scanner QR (petugas) |
| `/scan/verify` | API verify QR (AJAX) |
| `/admin` | Dashboard admin |
| `/midtrans/webhook` | Webhook pembayaran |

---

## 7. Flow Sistem

```
User isi form → Simpan ke DB → 
  ├── Event GRATIS → Generate QR langsung → Email tiket
  └── Event BERBAYAR → Tampilkan QRIS Midtrans → 
        └── Webhook konfirmasi → Generate QR → Email tiket

Petugas scan QR → API cek status → Tandai sudah masuk
```

---

## 8. Supervisor Config (Production)

```ini
[program:tiket-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/tiket-event/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/tiket-event/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 9. Nginx Config

```nginx
server {
    listen 80;
    server_name tiket.domainanda.com;
    root /var/www/tiket-event/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```
