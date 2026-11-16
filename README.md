# Timesheet MagangHub (PWA) — Soft Pastel Bento & AI Gemini

Aplikasi **Logbook Harian Peserta Magang (PWA)** berbasis **Laravel + Tailwind CSS + Google Gemini AI** dengan identitas desain **Soft Pastel Bento & High-Contrast Neo-Dock** sesuai referensi [`public/style-design.png`](public/style-design.png).

![Style Design Reference](public/style-design.png)

---

## Alur Utama Peserta (Single-Role Participant Flow)

1. **Registrasi Cepat Peserta (`/register`):**
   - Peserta cukup mengisi **4 data inti**:
     1. **Nama Lengkap Peserta**
     2. **Divisi**
     3. **Bagian / Posisi**
     4. **Nama Mentor Pembimbing**
   - Setelah klik **Simpan & Tambah Logbook Harian**, peserta **otomatis masuk ke halaman Tambah Logbook Harian (`/logbook/create`)** tanpa proses approval admin/mentor yang menghambat.
2. **Tambah Logbook Harian + Asisten AI Gemini (`/logbook/create`):**
   - Input tanggal, jam mulai–selesai, judul, kategori, lokasi kerja (`WFO / WFH / Lapangan`), output, kendala, dan bukti foto.
   - **✨ Rapikan Teks** dalam 3 gaya bahasa (*Formal*, *Singkat*, *Detail*), **🎤 Rekam Suara (Voice-to-Log)**, dan **📷 Saran dari Foto**.
3. **Beranda Peserta (`/dashboard` — Layar 1 `style-design.png`):**
   - Hero Lavender Grid Card dengan *Dark Timer Island* + ilustrasi jam beker 3D.
   - Kartu Logbook Pastel (**Mint**, **Periwinkle**, **Butter Yellow**, **Lavender**) dengan indikator jam, kategori kegiatan, dan lokasi kerja.
   - **Floating Neo-Dock + Detached Squircle FAB `[⊕]`** berborder hitam tegas (`3px solid #18181B`).
4. **Detail Logbook (`/logbook/{id}` — Layar 2 `style-design.png`):**
   - Header Lavender Grid + **2 Kartu Bento Putih** (*Tanggal & Durasi* serta *Divisi & Mentor*).
   - **Segmented Pill Tabs:** `Goals & Output` (daftar sub-item dengan centang mint) dan `Catatan` (catatan tambahan peserta).
5. **Kalender Bulanan Interaktif & Cetak PDF (`/logbook` & `/reports` — Layar 3 `style-design.png`):**
   - Kalender bulanan dengan highlight tanggal terisi berwarna **Butter Yellow (`#FBE8A6`)** dan tanggal aktif **Charcoal (`#18181B`)**.
   - Cetak laporan PDF A4 lengkap dengan kop instansi, identitas peserta (Nama, Divisi, Bagian, Mentor), ringkasan AI Gemini, tabel kegiatan, tanda tangan, dan QR Verifikasi (`/verify/{token}`).

---

## Cara Menjalankan di Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Pengujian Otomatis
```bash
php artisan test
```
