# ⚡ Panduan Performa & Deployment Production

## Mengapa Sistem Ini Kuat untuk 4000+ User

### 1. Redis sebagai Cache & Session
```
Semua session user disimpan di Redis (bukan file/DB)
→ 10x lebih cepat dari session berbasis file
→ Tidak ada file lock di server

Cache event info 5 menit → Kurangi 99% query "get event"
Cache hasil scan 10 detik → Proteksi double-scan spam
```

### 2. Atomic Database Operations
```php
// SALAH - Race condition (bisa oversell):
if ($event->registered_count < $event->quota) {
    $event->registered_count++;
    $event->save();
}

// BENAR - Atomic dengan DB transaction + lockForUpdate:
DB::transaction(function() {
    $event = Event::lockForUpdate()->find($id);
    if ($event->registered_count < $event->quota) {
        Event::where('id', $id)->increment('registered_count');
        // aman meski 1000 user submit bersamaan
    }
});
```

### 3. Queue untuk Proses Berat
```
Generate QR code → Queue (tidak blocking user)
Kirim email       → Queue (tidak blocking user)
Log scan          → afterResponse() (tidak memperlambat scan)

User submit form → langsung dapat response
Generate tiket terjadi di background
```

### 4. Database Indexing
```sql
-- Semua kolom yang sering dicari sudah di-index:
ALTER TABLE registrations ADD INDEX idx_email (email);
ALTER TABLE registrations ADD INDEX idx_code (registration_code);
ALTER TABLE tickets ADD INDEX idx_token (token);  -- ← PALING PENTING
ALTER TABLE tickets ADD INDEX idx_used (is_used);
```

### 5. Rate Limiting Pendaftaran
```
Max 5 submit per IP per 10 menit
→ Mencegah spam/bot menghabiskan kuota
→ Tidak mempengaruhi user normal
```

---

## Server Minimum Requirements

| Komponen | Minimum | Rekomendasi |
|----------|---------|-------------|
| CPU | 2 core | 4 core |
| RAM | 4 GB | 8 GB |
| Storage | 20 GB SSD | 50 GB SSD |
| PHP | 8.2 | 8.3 |
| MySQL | 8.0 | 8.0+ |
| Redis | 7.0 | 7.2 |
| PHP-FPM workers | 20 | 50 |

**Rekomendasi Provider:**
- VPS: DigitalOcean ($24/bulan), Vultr, Biznetgio, IDCloudHost
- Managed: Laravel Forge + DigitalOcean (paling mudah)

---

## Konfigurasi PHP-FPM (/etc/php/8.2/fpm/pool.d/www.conf)

```ini
pm = dynamic
pm.max_children = 50        ; Sesuaikan dengan RAM
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500       ; Restart worker tiap 500 request (cegah memory leak)
```

---

## MySQL Tuning (/etc/mysql/mysql.conf.d/mysqld.cnf)

```ini
[mysqld]
innodb_buffer_pool_size = 2G     ; 50-70% dari RAM
innodb_log_file_size = 256M
max_connections = 200
query_cache_type = 0             ; Matikan (deprecated MySQL 8)
innodb_flush_log_at_trx_commit = 2  ; Sedikit lebih cepat, aman untuk ini
```

---

## Load Test Sebelum Hari H

```bash
# Install Apache Bench
sudo apt install apache2-utils

# Test endpoint registrasi (100 request, 20 concurrent)
ab -n 100 -c 20 -m POST \
   -H "Content-Type: application/x-www-form-urlencoded" \
   -p /tmp/form_data.txt \
   https://tiket.domainanda.com/daftar

# Test endpoint scan (simulasi 50 petugas scan bersamaan)
ab -n 1000 -c 50 -m POST \
   -H "Content-Type: application/json" \
   -H "X-CSRF-TOKEN: your_token" \
   -p /tmp/scan_data.json \
   https://tiket.domainanda.com/scan/verify
```

---

## Checklist Sebelum Launch

- [ ] `APP_DEBUG=false` di .env
- [ ] `APP_ENV=production` di .env
- [ ] Ganti `SCANNER_PIN` di .env (jangan pakai default 123456)
- [ ] Midtrans Production key sudah diisi
- [ ] SSL certificate terpasang (HTTPS)
- [ ] `php artisan optimize` sudah dijalankan
- [ ] Queue worker running dengan Supervisor
- [ ] Redis berjalan
- [ ] Backup database otomatis terjadwal
- [ ] Test full flow: daftar → bayar → dapat tiket → scan
- [ ] Test scanner di HP Android dan iPhone
- [ ] Load test dengan ab atau k6

---

## Monitoring Hari-H

```bash
# Monitor queue real-time
php artisan horizon

# Lihat log error
tail -f storage/logs/laravel.log

# Monitor jumlah tiket ter-scan
watch -n 5 'mysql -u root -p tiket_event -e "SELECT COUNT(*) as checked_in FROM tickets WHERE is_used=1;"'
```

---

## Proteksi Tambahan yang Bisa Ditambahkan

1. **Cloudflare** - DDoS protection gratis
2. **Captcha** di form pendaftaran (hCaptcha/Turnstile)
3. **Email verification** sebelum tiket di-generate
4. **OTP WhatsApp** via Fonnte/WhatsApp Cloud API
5. **Backup tiket** via WhatsApp (kirim QR ke WA)

---

## Struktur File Lengkap

```
app/
├── Http/Controllers/
│   ├── RegistrationController.php
│   ├── PaymentController.php
│   ├── TicketController.php
│   ├── ScannerController.php
│   └── AdminController.php
├── Jobs/
│   └── GenerateTicketJob.php
├── Mail/
│   └── TicketMail.php
└── Models/
    ├── Event.php
    ├── Registration.php
    ├── Ticket.php
    └── ScanLog.php

database/
├── migrations/
│   ├── ..._create_events_table.php
│   ├── ..._create_registrations_table.php
│   ├── ..._create_tickets_table.php
│   └── ..._create_scan_logs_table.php
└── seeders/
    └── EventSeeder.php

resources/views/
├── registration/
│   ├── form.blade.php        ← Form pendaftaran
│   ├── success.blade.php     ← Halaman berhasil daftar
│   └── full.blade.php        ← Kuota penuh
├── payment/
│   └── show.blade.php        ← Halaman bayar QRIS
├── ticket/
│   └── show.blade.php        ← Tampilan tiket + QR
├── scanner/
│   └── index.blade.php       ← Scanner untuk petugas
├── admin/
│   ├── dashboard.blade.php
│   └── registrations.blade.php
└── emails/
    └── ticket.blade.php      ← Email tiket

routes/
├── web.php
└── api.php

config/
├── midtrans.php
└── scanner.php
```
