# 11. Deployment dan Optimasi Shared Hosting

## Persyaratan Hosting
PHP 8.2+, MySQL/MariaDB, SSH atau Terminal cPanel, Cron Job, OPcache aktif, ekstensi (mbstring, openssl, pdo_mysql, gd/imagick, curl, fileinfo, zip), SSL gratis (Let's Encrypt), disk SSD/NVMe, lebih baik LiteSpeed, dan lokasi server Indonesia/Singapura.

## Struktur di Server

Pilihan A (ideal): document root diarahkan ke `public/`.

Pilihan B (jika document root tidak bisa diubah):
```
/home/user/
├── laravel-app/          # seluruh project (di luar public_html)
└── public_html/          # isi folder public/ Laravel
    ├── index.php         # ubah path ke ../laravel-app/...
    ├── build/
    ├── manifest.webmanifest
    └── sw.js
```
Edit `index.php`:
```php
require __DIR__.'/../laravel-app/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';
```

## Langkah Deploy

1. **Di lokal:**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```
2. Upload project (zip, lalu ekstrak) tanpa `node_modules`, `.git`, `tests`
3. Buat database dan user di cPanel, isi `.env`
4. Jalankan di server:
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```
5. Atur permission: `storage/` dan `bootstrap/cache/` writable (775)
6. Pasang SSL dan paksa HTTPS
7. Pasang cron (lihat di bawah)
8. Uji: login, upload foto, AI, PDF, install PWA

> Jika `storage:link` tidak bisa (symlink diblokir), buat route penyaji file atau salin lewat script.

## Cron Job

```
* * * * * cd /home/user/laravel-app && php artisan schedule:run >> /dev/null 2>&1
```

Di `routes/console.php` (atau `Kernel`):
```php
Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()->withoutOverlapping();
Schedule::command('logbook:remind')->dailyAt('16:00');
Schedule::command('reports:cleanup')->weekly();
Schedule::command('ai:reset-quota')->daily();
```

## Tuning Performa

| Area | Tindakan |
|------|----------|
| PHP | OPcache aktif, `memory_limit` 256M+, `upload_max_filesize` 10M, `max_execution_time` 60+ |
| Laravel | Cache config/route/view/event, `APP_DEBUG=false` |
| Database | Index (lihat dok 05), eager loading, pagination |
| Aset | Vite build + hash, kompres gzip/brotli, header cache panjang untuk `/build` |
| Gambar | Kompres client-side, thumbnail, lazy loading (`loading="lazy"`) |
| Livewire | Gunakan `wire:model.lazy`/`.blur`, hindari render ulang daftar besar, `wire:key` |
| Session/cache | Driver `file` (atau `database`) |
| CDN | Cloudflare gratis untuk aset statis + proteksi dasar |

Contoh `.htaccess` tambahan (cache aset):
```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
</IfModule>
```

## Backup
- Database: cron `mysqldump` harian, simpan 7 hari, salin ke penyimpanan lain
- Storage: backup mingguan (foto dan PDF)
- Opsional: `spatie/laravel-backup` ke Google Drive/R2

## Monitoring
- Uptime: UptimeRobot (gratis)
- Error: log harian, opsional Sentry free tier
- Pantau penggunaan disk (foto cepat membengkak) dan kuota AI

## Batas Shared Hosting dan Jalan Keluar

| Gejala | Solusi |
|--------|--------|
| Disk penuh karena foto | Pindah ke Cloudflare R2/S3, atau kompres lebih agresif |
| PDF timeout/habis memori | Queue, kurangi foto, naikkan limit, atau pindah VPS |
| Queue lambat (cron 1 menit) | Pindah ke VPS dengan supervisor |
| Traffic tinggi | Upgrade ke VPS 1-2 GB (aaPanel/Forge) |
| Butuh real-time | Pakai polling dulu, VPS + WebSocket jika perlu |

## Alternatif Pindah ke VPS Nanti
Struktur Laravel yang sama bisa dipindah tanpa ubah kode: tambahkan Redis (cache/queue), supervisor untuk worker, dan Nginx + PHP-FPM.
