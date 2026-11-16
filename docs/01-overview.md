# 01. Overview

## Latar Belakang
Peserta magang wajib mencatat kegiatan harian beserta bukti dokumentasi. Pencatatan manual (kertas/Word/Excel) rawan terlewat, sulit dirapikan, dan menyulitkan mentor mereview.

## Tujuan
1. Mencatat kegiatan harian dengan cepat dari HP (mobile-first).
2. Melampirkan bukti foto/dokumentasi yang valid.
3. Merapikan catatan kasar menjadi kalimat laporan yang jelas dengan AI.
4. Mencetak rekap PDF per hari, minggu, bulan, atau rentang bebas.
5. Memudahkan mentor mereview dan menyetujui logbook.

## Ruang Lingkup

**Termasuk (In Scope)**
- Autentikasi, profil, manajemen periode magang
- CRUD logbook + lampiran foto
- AI: rapikan teks, ringkasan mingguan/bulanan, voice-to-log, saran deskripsi dari foto
- Approval mentor
- Ekspor PDF
- PWA: install, offline, push notification

**Tidak Termasuk (Out of Scope, versi awal)**
- Aplikasi native Android/iOS
- Absensi berbasis wajah
- Integrasi langsung ke sistem MagangHub resmi
- Chat real-time

## Peran & Alur Pengguna (Single-Role: Peserta Magang)

Aplikasi difokuskan khusus untuk **Peserta Magang** tanpa hambatan birokrasi approval admin/mentor di dalam sistem:
1. **Registrasi Cepat Peserta (`/register`):** Peserta cukup mengisi **Nama Lengkap**, **Divisi**, **Bagian / Posisi**, dan **Nama Mentor**.
2. **Otomatis Masuk ke Tambah Logbook Harian (`/logbook/create`):** Setelah mengisi form registrasi, peserta langsung diarahkan ke halaman tambah kegiatan harian.
3. **Semua Status Approval / Admin Disembunyikan:** Tidak ada antrean status *Draft / Submitted / Approved / Revisi*; seluruh entri yang disimpan langsung aktif dan siap dicetak ke laporan PDF kapan saja.

## User Stories (inti)

**Peserta**
- Sebagai peserta, aku ingin mengisi logbook dalam kurang dari 1 menit agar tidak merepotkan.
- Sebagai peserta, aku ingin bicara lewat mic lalu teksnya otomatis rapi.
- Sebagai peserta, aku ingin foto kegiatan otomatis diberi watermark tanggal dan jam.
- Sebagai peserta, aku ingin mencetak laporan bulanan lengkap dengan foto dan kolom tanda tangan.
- Sebagai peserta, aku ingin tetap bisa mengisi saat sinyal buruk.

**Mentor**
- Sebagai mentor, aku ingin melihat daftar logbook yang menunggu review.
- Sebagai mentor, aku ingin memberi komentar atau meminta revisi per entri.
- Sebagai mentor, aku ingin approve satu minggu sekaligus.

**Admin**
- Sebagai admin, aku ingin melihat peserta yang belum mengisi logbook.
- Sebagai admin, aku ingin mengekspor rekap seluruh peserta.

## Indikator Keberhasilan
- Waktu input satu entri rata-rata di bawah 60 detik
- Minimal 90% hari kerja terisi per peserta
- PDF bulanan terbuat di bawah 30 detik
- Waktu respons halaman utama di bawah 2 detik di jaringan 4G
