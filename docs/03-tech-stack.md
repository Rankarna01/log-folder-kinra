# 03. Tech Stack

## Ringkasan

| Layer | Teknologi | Alasan |
|-------|-----------|--------|
| Bahasa | PHP 8.2+ | Didukung semua shared hosting |
| Framework | Laravel 11/12 | Ekosistem lengkap, auth, queue, scheduler |
| UI reaktif | Livewire 3 + Alpine.js | Tanpa build SPA rumit, ringan |
| Styling & UI | Tailwind CSS + Plus Jakarta Sans | Mobile-first, gaya *Soft Pastel Bento & Neo-Dock* (lihat [13-design-system.md](13-design-system.md)) |
| Ikon & Ilustrasi | Lucide Icons (`blade-lucide-icons`) + Aset 3D PNG/WebP | Sesuai estetika kartu pastel & ilustrasi 3D di `style-design.png` |
| Database | MySQL / MariaDB | Bawaan hosting |
| Auth | Laravel Breeze/Fortify + role | Sederhana dan teruji |
| Role/permission | Enum role + Policy (atau spatie/laravel-permission) | Cukup 3 peran |
| AI | **Google Gemini API** (model Flash) | Murah, multimodal (teks + audio + gambar) |
| PDF | barryvdh/laravel-dompdf (alt: mPDF) | Jalan di shared hosting |
| Gambar | intervention/image | Resize, thumbnail, watermark server-side |
| Client image | Canvas API / browser-image-compression | Kompres sebelum upload |
| Offline | Service Worker + Dexie.js (IndexedDB) | Inti PWA |
| Voice | MediaRecorder API (+ Web Speech API opsional) | Rekam di browser |
| Push | laravel-notification-channels/webpush | Web Push standar |
| Queue | Driver `database` + cron | Tanpa Redis |
| Cache/session | `file` atau `database` | Redis biasanya tidak ada |
| Storage | Local `storage/app` (+ opsional Cloudflare R2) | Hemat dan fleksibel |
| Build aset | Vite (di lokal, hasil di-upload) | Server tidak butuh Node |
| Testing | Pest/PHPUnit | Standar Laravel |

## Kenapa Gemini

| Kebutuhan | Cara dengan Gemini |
|-----------|--------------------|
| Rapikan teks | `generateContent` input teks |
| Ringkasan mingguan/bulanan | Input kumpulan entri, output teks/JSON |
| Voice-to-text | Kirim audio (inline base64) + instruksi transkripsi bahasa Indonesia |
| Deskripsi dari foto | Kirim gambar + instruksi |
| Output terstruktur | Mode JSON (`responseMimeType: application/json` + schema) |
| Hemat biaya | Pakai varian Flash/Flash-Lite, ada free tier untuk tahap awal |

> Nama model berubah dari waktu ke waktu. Simpan di `.env` (`GEMINI_MODEL`) dan cek daftar terbaru di dokumentasi Google AI Studio sebelum implementasi.

## Package Composer

```bash
composer require livewire/livewire
composer require mallardduck/blade-lucide-icons
composer require barryvdh/laravel-dompdf
composer require intervention/image
composer require laravel-notification-channels/webpush
composer require spatie/laravel-activitylog   # opsional: audit trail
composer require --dev pestphp/pestphp
```

## Package NPM & Aset Desain (dibuild di lokal)

```bash
npm i alpinejs tailwindcss @tailwindcss/forms dexie browser-image-compression
npm i -D vite laravel-vite-plugin
```

- **Font Utama:** `Plus Jakarta Sans` (Google Fonts: weight `400`, `500`, `600`, `700`)
- **Token Warna Desain (`style-design.png`):**
  - Canvas: `#F6F7F9`
  - Dark Island / Border Dock: `#18181B`
  - Pastel Cards: Lavender (`#E5D9FA`), Mint (`#D5EDE8`), Periwinkle (`#E6E8FA`), Butter Yellow (`#FBE8A6`)
  - Detail lengkap lihat [13-design-system.md](13-design-system.md) dan [14-ui-components-breakdown.md](14-ui-components-breakdown.md).

## Versi Minimum

| Komponen | Versi |
|----------|-------|
| PHP | 8.2 |
| MySQL | 8.0 / MariaDB 10.6 |
| Ekstensi PHP | mbstring, openssl, pdo_mysql, gd (atau imagick), curl, fileinfo, zip |

## Variabel Environment

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-kamu.com

DB_CONNECTION=mysql

QUEUE_CONNECTION=database
CACHE_STORE=file
SESSION_DRIVER=file

GEMINI_API_KEY=isi_api_key
GEMINI_MODEL=gemini-flash-latest
GEMINI_TIMEOUT=30
AI_DAILY_LIMIT_PER_USER=30

VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=

FILESYSTEM_DISK=local
```
