# Timesheet MagangHub (PWA)

Aplikasi logbook harian untuk peserta magang: catat kegiatan, lampirkan foto/dokumentasi, rapikan deskripsi dengan AI (Gemini), dan cetak laporan PDF per hari/minggu/bulan.

## Daftar Dokumen

| No | File | Isi |
|----|------|-----|
| 01 | [01-overview.md](01-overview.md) | Tujuan, ruang lingkup, peran, user story |
| 02 | [02-architecture.md](02-architecture.md) | Arsitektur sistem, layer, struktur folder Laravel |
| 03 | [03-tech-stack.md](03-tech-stack.md) | Pilihan teknologi dan alasannya |
| 04 | [04-features-per-menu.md](04-features-per-menu.md) | Detail fitur per menu per peran |
| 05 | [05-database-schema.md](05-database-schema.md) | Skema database, relasi, index |
| 06 | [06-ai-gemini-integration.md](06-ai-gemini-integration.md) | Integrasi Gemini: teks, voice, foto, prompt |
| 07 | [07-pdf-export.md](07-pdf-export.md) | Desain dan implementasi ekspor PDF |
| 08 | [08-api-routes.md](08-api-routes.md) | Daftar route dan endpoint |
| 09 | [09-pwa-offline.md](09-pwa-offline.md) | PWA, offline mode, sinkronisasi, push |
| 10 | [10-security-privacy.md](10-security-privacy.md) | Keamanan, privasi foto/audio, validasi |
| 11 | [11-deployment-shared-hosting.md](11-deployment-shared-hosting.md) | Deploy dan optimasi di shared hosting |
| 12 | [12-roadmap.md](12-roadmap.md) | Fase pengerjaan, estimasi, checklist |
| 13 | [13-design-system.md](13-design-system.md) | Design System & Style Guide (palet pastel, tipografi, radius, CSS grid dari `style-design.png`) |
| 14 | [14-ui-components-breakdown.md](14-ui-components-breakdown.md) | Breakdown komponen UI Blade/Livewire & 4 variasi style kartu |
| 15 | [15-screen-style-mapping.md](15-screen-style-mapping.md) | Pemetaan visual 3 layar `style-design.png` ke setiap menu Peserta, Mentor, dan Admin |

## Ringkasan Keputusan Utama

- **Backend + UI:** Laravel 11/12 + Livewire + Alpine.js + Tailwind (cocok shared hosting)
- **Gaya Visual (UI/UX):** *Soft Pastel Bento & Neo-Dock* berbasis [`public/style-design.png`](../public/style-design.png) — kombinasi kartu Lavender Grid (`#E5D9FA`), Mint (`#D5EDE8`), Periwinkle (`#E6E8FA`), Butter Yellow (`#FBE8A6`), kapsul Charcoal (`#18181B`), dan Floating Dock berborder tegas
- **Database:** MySQL/MariaDB
- **AI:** Google Gemini API (model Flash, hemat biaya), satu API untuk teks, audio, dan gambar
- **PDF:** DomPDF/mPDF
- **PWA:** Service worker + manifest + IndexedDB untuk offline
- **Peran:** Peserta, Mentor, Admin
