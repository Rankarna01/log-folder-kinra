# Timesheet MagangHub (PWA) — Soft Pastel Bento & AI Gemini

Aplikasi **Logbook Harian Peserta Magang (PWA)** berbasis **Laravel + Tailwind CSS + Google Gemini AI** dengan identitas desain **Soft Pastel Bento & High-Contrast Neo-Dock** sesuai referensi [`public/style-design.png`](public/style-design.png).

![Style Design Reference](public/style-design.png)

---

## Fitur Utama

1. **Dashboard Peserta (Layar 1 `style-design.png`):**
   - Header profil dengan notifikasi revisi & pencarian cepat.
   - **Hero Lavender Grid Card** dengan *Dark Timer Island* (`32h 30m`) dan ilustrasi jam beker 3D.
   - **Kartu Logbook Pastel (4 Variasi Style):** Mint (`Approved`), Periwinkle (`Submitted`), Butter Yellow (`Revisi`), dan Lavender (`Draft / AI`).
   - **Floating Neo-Dock + Detached Squircle FAB `[⊕]`** berborder hitam tegas (`3px solid #18181B`).
2. **Detail Logbook & Kolaborasi Mentor (Layar 2 `style-design.png`):**
   - Header Lavender Grid + **2 Kartu Bento Putih** (*Deadline/Tanggal* & *People/Mentor*).
   - **Segmented Pill Tabs:** `Goals & Output` (daftar sub-item dengan centang mint & badge prioritas) dan `Chat` (diskusi & catatan revisi mentor).
3. **Kalender Bulanan Interaktif & Rekap PDF (Layar 3 `style-design.png`):**
   - Navigasi bulanan dengan highlight tanggal terisi berwarna **Butter Yellow (`#FBE8A6`)** dan tanggal aktif **Charcoal (`#18181B`)**.
   - Kartu jadwal kegiatan harian (`Today's meeting & logbook`) serta progress laporan bulanan (`Delivery App UI Kit` — `65%`).
4. **Integrasi AI Google Gemini (`GeminiService` & `PromptBuilder`):**
   - **✨ Rapikan Teks** dalam 3 gaya bahasa (*Formal*, *Singkat*, *Detail*) tanpa menimpa catatan kasar asli (`raw_description`).
   - **🎤 Voice-to-Log** (dikte suara langsung dirapikan AI).
   - **📷 Saran dari Foto** & evaluasi skor kualitas entri.
   - **Ringkasan Eksekutif & Ekstraksi Kompetensi (Skill)** otomatis pada laporan bulanan.
5. **Ekspor PDF A4 & Verifikasi QR Publik:**
   - Cetak laporan harian, mingguan, bulanan, atau akhir lengkap dengan kop instansi, tabel kegiatan, tanda tangan, dan URL verifikasi QR (`/verify/{token}`).
6. **PWA & Mode Offline:**
   - Dilengkapi `manifest.webmanifest`, Service Worker (`sw.js`), dan antrean sinkronisasi otomatis (`/api/logbooks/sync`).

---

## Daftar Dokumentasi Teknis (`docs/`)

| No | Dokumen | Deskripsi |
|----|---------|-----------|
| 01 | [docs/01-overview.md](docs/01-overview.md) | Tujuan, ruang lingkup, peran, dan user stories |
| 02 | [docs/02-architecture.md](docs/02-architecture.md) | Arsitektur monolith modular & service layer |
| 03 | [docs/03-tech-stack.md](docs/03-tech-stack.md) | Tech stack, token warna, dan konfigurasi environment |
| 04 | [docs/04-features-per-menu.md](docs/04-features-per-menu.md) | Fitur per menu untuk Peserta, Mentor, dan Admin |
| 05 | [docs/05-database-schema.md](docs/05-database-schema.md) | Skema database & ERD |
| 06 | [docs/06-ai-gemini-integration.md](docs/06-ai-gemini-integration.md) | Integrasi Google Gemini API & PromptBuilder |
| 07 | [docs/07-pdf-export.md](docs/07-pdf-export.md) | Desain cetak PDF A4 & QR Verifikasi |
| 08 | [docs/08-api-routes.md](docs/08-api-routes.md) | Spesifikasi route web dan endpoint JSON |
| 09 | [docs/09-pwa-offline.md](docs/09-pwa-offline.md) | Strategi PWA, offline sync, dan notifikasi |
| 10 | [docs/10-security-privacy.md](docs/10-security-privacy.md) | Standar keamanan, otorisasi, dan privasi AI |
| 11 | [docs/11-deployment-shared-hosting.md](docs/11-deployment-shared-hosting.md) | Panduan deploy & optimasi di shared hosting |
| 12 | [docs/12-roadmap.md](docs/12-roadmap.md) | Roadmap fase pengembangan & checklist |
| 13 | [docs/13-design-system.md](docs/13-design-system.md) | **Design System & Style Guide** dari `style-design.png` |
| 14 | [docs/14-ui-components-breakdown.md](docs/14-ui-components-breakdown.md) | **Breakdown 10 Komponen UI & 4 Variasi Style Kartu** |
| 15 | [docs/15-screen-style-mapping.md](docs/15-screen-style-mapping.md) | **Mapping Visual 3 Layar `style-design.png` ke Seluruh Menu** |

---

## Cara Menjalankan di Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Akun Demo (Bisa Beralih 1-Klik di Bar Atas Aplikasi)
- **Peserta:** `peserta@maganghub.id` / `password` (*Esther Howard*)
- **Mentor:** `mentor@maganghub.id` / `password` (*Agnes Nielsen*)
- **Admin:** `admin@maganghub.id` / `password` (*Muhammad Randy Karna*)

### Pengujian Otomatis
```bash
php artisan test
```
