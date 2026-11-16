# 09. PWA, Offline, dan Notifikasi

## Syarat PWA
- **HTTPS** wajib (juga untuk kamera, mikrofon, service worker)
- `manifest.webmanifest`, service worker, ikon 192/512 px (termasuk maskable)

## Manifest

```json
{
  "name": "Timesheet MagangHub",
  "short_name": "Timesheet",
  "start_url": "/dashboard",
  "scope": "/",
  "display": "standalone",
  "orientation": "portrait",
  "background_color": "#F6F7F9",
  "theme_color": "#18181B",
  "lang": "id",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" },
    { "src": "/icons/maskable-512.png", "sizes": "512x512", "type": "image/png", "purpose": "maskable" }
  ],
  "shortcuts": [
    { "name": "Tambah Logbook", "url": "/logbook/create" },
    { "name": "Laporan", "url": "/reports" }
  ]
}
```

## Strategi Cache Service Worker

| Aset | Strategi |
|------|----------|
| Shell (CSS, JS, font, ikon) | Cache First (versi hash dari Vite) |
| Halaman HTML | Network First, fallback ke halaman offline |
| Gambar thumbnail | Stale While Revalidate |
| Request API (GET) | Network First dengan timeout |
| Request API (POST) | Tidak di-cache, masuk antrean offline |

Sediakan halaman `/offline` sebagai fallback.

## Mode Offline untuk Logbook

**Yang bisa offline**
- Membuka form dan menulis entri
- Mengambil foto (disimpan sebagai Blob terkompres)
- Merekam suara (disimpan, ditranskrip saat online)
- Melihat entri yang sudah pernah dimuat (cache terakhir)

**Yang butuh online**
- AI rapikan/transkrip/foto, ekspor PDF, review mentor

### Skema IndexedDB (Dexie)

```js
db.version(1).stores({
  pendingLogs:   '++id, client_uuid, date, status',   // entri belum sinkron
  pendingFiles:  '++id, client_uuid, logbook_uuid',   // foto/audio Blob
  cachedLogs:    'id, date, status',                  // salinan baca-saja
  meta:          'key'
});
```

### Alur Sinkron

```
Simpan entri
  ├─ online  → POST /api/logbooks
  └─ offline → simpan ke pendingLogs (status: pending_sync), tampilkan badge "Menunggu sinkron"

Event 'online' / app dibuka / Background Sync
  → kirim pendingLogs berurutan (batch /api/logbooks/sync)
  → upload pendingFiles per entri
  → server balas id → tandai synced, hapus dari antrean
  → gagal → retry dengan backoff, tampilkan indikator
```

- `client_uuid` (UUID v4) mencegah entri ganda jika permintaan terulang.
- Konflik: server menang untuk entri yang sudah berstatus Submitted/Approved.
- Background Sync API tidak ada di semua browser, jadi sediakan fallback sinkron saat aplikasi dibuka dan tombol "Sinkron sekarang".

## Kamera dan Foto (client-side)

1. `<input type="file" accept="image/*" capture="environment">` atau `getUserMedia`
2. Kompres: resize sisi terpanjang 1280 px, JPEG kualitas 0.75, target < 400 KB
3. Watermark via Canvas: tanggal, jam, nama peserta (pojok bawah)
4. Simpan EXIF waktu bila ada (`taken_at`)
5. Upload dengan progress bar, maksimal 5 foto per entri

## Voice di Frontend

```js
const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
const rec = new MediaRecorder(stream, { mimeType: pickSupportedMime() });
// pickSupportedMime: coba 'audio/webm;codecs=opus', lalu 'audio/mp4' (iOS Safari)
```
- Batasi durasi 90 detik, tampilkan timer dan gelombang suara
- Tangani penolakan izin mikrofon dengan pesan petunjuk
- Opsi cepat: Web Speech API (`id-ID`) untuk dikte langsung di Chrome Android; fallback ke Gemini untuk browser lain

## Push Notification

| Pemicu | Penerima | Pesan |
|--------|----------|-------|
| Jam pengingat (cron) | Peserta yang belum isi hari ini | "Jangan lupa isi logbook hari ini" |
| Entri disubmit | Mentor | "Ada logbook baru dari {nama}" |
| Approve / revisi | Peserta | Status dan komentar |
| PDF siap | Pemilik laporan | "Laporan bulan {bulan} siap diunduh" |

- Web Push dengan kunci VAPID
- iOS: push hanya bekerja jika PWA sudah **dipasang ke Home Screen** (iOS 16.4+)
- Sediakan fallback notifikasi di dalam aplikasi (ikon lonceng)
- Jadwal pengingat lewat Laravel Scheduler (cron tiap menit di shared hosting)

## Update Aplikasi
- Versi service worker mengikuti hash build
- Saat versi baru tersedia, tampilkan banner "Perbarui aplikasi"

## Instal
- Tangkap event `beforeinstallprompt` (Android/Chrome) → tombol "Pasang Aplikasi"
- iOS: tampilkan panduan "Bagikan → Tambah ke Layar Utama"

## Pengujian
- Lighthouse PWA audit
- Uji mode pesawat, jaringan 3G lambat, dan toggle online/offline saat upload
- Uji di Android Chrome, iOS Safari (terpasang dan tidak), dan desktop
