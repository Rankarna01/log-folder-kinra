# 12. Roadmap Pengembangan

Estimasi untuk 1 developer fullstack, bisa bergeser sesuai ketersediaan waktu.

## Fase 0: Persiapan & Design System (2-3 hari)
- [ ] Finalisasi kebutuhan dan alur dengan calon pengguna (peserta dan mentor)
- [x] Breakdown sistem desain & komponen UI dari `public/style-design.png` ([13-design-system.md](13-design-system.md), [14-ui-components-breakdown.md](14-ui-components-breakdown.md), [15-screen-style-mapping.md](15-screen-style-mapping.md))
- [ ] Setup repo, Laravel, Livewire, Tailwind (`tailwind.config.js` dengan palet Pastel Bento), Vite, Pest
- [ ] Siapkan komponen Blade dasar: `<x-nav.floating-dock />`, `<x-dashboard.hero-timer />`, `<x-logbook.card />`, `<x-calendar.month-grid />`
- [ ] Setup environment staging di hosting
- [ ] Buat API key Gemini dan uji coba prompt di AI Studio

## Fase 1: MVP Logbook (2-3 minggu)
- [ ] Autentikasi + peran (peserta, mentor, admin)
- [ ] Profil dan data penempatan magang
- [ ] CRUD logbook (form, daftar, detail, filter)
- [ ] Upload foto + kompres client-side + thumbnail + watermark
- [ ] Dashboard peserta (ringkasan, kalender, progress)
- [ ] Ekspor PDF harian, mingguan, bulanan (tanpa AI)
- [ ] Layout mobile-first + dark mode
- [ ] Deploy staging, uji di HP asli

**Definisi selesai:** peserta bisa login, isi logbook dengan foto, dan cetak PDF bulanan.

## Fase 2: AI dan PWA (2 minggu)
- [ ] `GeminiService` + `PromptBuilder` + kuota + log `ai_usages`
- [ ] Tombol Rapikan (3 gaya) dengan panel bandingkan
- [ ] Voice-to-log (MediaRecorder + transkripsi Gemini)
- [ ] Ringkasan mingguan dan bulanan (queue) + masuk ke PDF
- [ ] Manifest, service worker, halaman offline, tombol instal
- [ ] Offline logbook + sinkron (Dexie)

**Definisi selesai:** peserta bisa bicara, hasil rapi, dan tetap bisa mengisi saat offline.

## Fase 3: Kolaborasi Mentor (2 minggu)
- [ ] Workflow status (Draft, Submitted, Approved, Revisi)
- [ ] Antrean review, komentar, approve massal
- [ ] Notifikasi in-app + Web Push + pengingat harian
- [ ] QR verifikasi + halaman verifikasi publik
- [ ] Tanda tangan digital di PDF
- [ ] Panel admin: user, instansi, periode, rekap, pengaturan

**Definisi selesai:** alur peserta → mentor → laporan ter-approve berjalan penuh.

## Fase 4: Penyempurnaan (2 minggu)
- [ ] Saran deskripsi dari foto
- [ ] Deteksi entri lemah
- [ ] Ekstrak skill + draf laporan akhir
- [ ] Statistik dan grafik
- [ ] Template kegiatan, duplikat entri
- [ ] Ekspor Excel/CSV
- [ ] Multi-bahasa ID/EN
- [ ] Audit trail, backup otomatis

## Fase 5: Rilis dan Evaluasi
- [ ] Uji beban ringan (30-100 pengguna)
- [ ] Uji keamanan (checklist dok 10)
- [ ] Pilot dengan satu kelompok peserta
- [ ] Kumpulkan umpan balik, perbaiki, rilis penuh
- [ ] Dokumentasi pengguna (panduan singkat bergambar)

## Ringkasan Jadwal

| Fase | Durasi | Output |
|------|--------|--------|
| 0 | 2-3 hari | Setup dan wireframe |
| 1 | 2-3 minggu | MVP logbook + PDF |
| 2 | 2 minggu | AI + PWA offline |
| 3 | 2 minggu | Mentor + notifikasi |
| 4 | 2 minggu | Fitur lanjutan |
| 5 | 1 minggu | Uji dan rilis |
| **Total** | **± 9-11 minggu** | |

## Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|--------|--------|----------|
| Kuota/biaya Gemini membengkak | Biaya naik | Model Flash, kuota harian, cache ringkasan |
| Transkripsi kurang akurat | Frustrasi pengguna | Layar cek hasil, istilah teknis di prompt, fallback ketik manual |
| Safari iOS pembatasan PWA | Fitur tidak konsisten | Uji dini di iOS, siapkan fallback |
| Disk hosting penuh karena foto | Aplikasi error | Kompres agresif, R2, retensi |
| PDF berat timeout | Laporan gagal | Queue, thumbnail, batas rentang |
| Peserta malas mengisi | Adopsi rendah | Pengingat, voice, template, UX < 1 menit |
| Data sensitif bocor lewat AI | Masalah privasi | Minimalkan data terkirim, opsi nonaktif, pemberitahuan |

## Backlog Ide (setelah rilis)
- Absensi + geolocation / geofence
- Voice note untuk komentar mentor
- Text-to-speech ringkasan mingguan
- Integrasi kalender dan Google Drive
- Voice command ("tambah kegiatan", "simpan")
- Gamifikasi (lencana streak)
- Impor data dari Excel
- Mode multi-instansi (white-label)
