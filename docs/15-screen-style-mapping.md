# 15. Mapping Style Layar per Menu & Role

Dokumen ini memetakan bagaimana ketiga layar referensi di [`public/style-design.png`](../public/style-design.png) diterapkan ke seluruh menu **Peserta**, **Mentor**, dan **Admin** yang ada pada [`04-features-per-menu.md`](04-features-per-menu.md).

---

## Ringkasan Pemetaan 3 Layar Referensi

| Layar di `style-design.png` | Ciri Visual Utama | Dipetakan ke Menu Aplikasi |
|-----------------------------|-------------------|----------------------------|
| **Layar Kiri (Screen 1)** | Header profil + Hero Lavender Grid dengan Dark Timer & Jam 3D + Daftar Kartu Pastel (Mint, Periwinkle, Yellow) + Floating Dock | **Beranda Peserta**, **Daftar Logbook**, **Dashboard Mentor**, **Dashboard Admin** |
| **Layar Tengah (Screen 2)** | Header Detail Lavender Grid + 2 Kartu Bento Putih (Deadline & People) + Segmented Pill Tabs (`Goals`/`Chat`) + Daftar Sub-Item Putih | **Detail Logbook & Review Mentor**, **Form Tambah/Edit + Panel AI**, **Detail Peserta (Mentor)** |
| **Layar Kanan (Screen 3)** | Navigasi Bulan + Kalender Grid Putih dengan Highlight Kuning Mentega & Hitam + Kartu Kegiatan Harian + Kartu Progress PDF/Magang | **Kalender Logbook**, **Menu Laporan & Ekspor PDF**, **Rekap Kehadiran & Kepatuhan** |

---

## A. Peran: Peserta (Participant)

### 1. Menu Beranda (`/dashboard`) — *Mengadopsi Layar 1*
```
┌──────────────────────────────────────────────┐
│ [Avatar] Halo, Budi Santoso       [🔔•] [🔍] │  ← Top Bar (Layar 1)
│                                              │
│ ┌──────────────────────────────────────────┐ │
│ │ Progres Magang Minggu Ini     (Grid Ungu)│ │  ← Hero Lavender Grid
│ │ ┌──────────────────────────────────────┐ │ │
│ │ │ 32j 30m                    [⏰ 3D]   │ │ │  ← Dark Island + Jam 3D
│ │ │ Hari ke-24 dari 90 • Sudah isi hari ini│ │
│ │ └──────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────┘ │
│                                              │
│ Kegiatan Terakhir                  Lihat Semua│
│                                              │
│ ┌──────────────────────────────────────────┐ │
│ │ [🗂] Integrasi API Pembayaran        [✏️] │ │  ← Kartu Pastel Mint
│ │ ⏱ 08:30 - 12:00   [⚑ Approved]           │ │    (Status: Approved)
│ │ ████████████████████░░░░░                │ │
│ │ [📷][📷] +2       [📅 16 Nov ⌄] [💬 02]  │ │
│ └──────────────────────────────────────────┘ │
│                                              │
│ ┌──────────────────────────────────────────┐ │
│ │ [🗂] Review Desain UI Dashboard      [✏️] │ │  ← Kartu Pastel Periwinkle
│ │ ⏱ 13:00 - 16:30   [⚑ Submitted]          │ │    (Status: Submitted)
│ │ ██████████████░░░░░░░░░░░                │ │
│ │ [📷]              [📅 15 Nov ⌄] [💬 01]  │ │
│ └──────────────────────────────────────────┘ │
│                                              │
│     ┏━━━━━━━━━━━━━━━━━━━━━━━┓  ┏━━━━━┓       │
│     ┃ [🏠]  [📅]  [📊]  [👤] ┃  ┃ [⊕] ┃       │  ← Floating Neo-Dock + FAB
│     ┗━━━━━━━━━━━━━━━━━━━━━━━┛  ┗━━━━━┛       │
└──────────────────────────────────────────────┘
```

### 2. Menu Detail Logbook (`/logbook/{id}`) — *Mengadopsi Layar 2*
- **Kartu Atas (Lavender Grid):** Menampilkan kategori di kotak ikon putih kiri atas, judul kegiatan besar, nama peserta + status badge, serta tombol edit bulat di kanan bawah.
- **2 Kartu Bento Putih di Bawahnya:**
  - **Kartu Kiri (`Waktu & Durasi`):** Ikon kalender/jam hitam + `16 Nov • 3j 30m` + lokasi `WFO`.
  - **Kartu Kanan (`Mentor & Bukti`):** Tumpukan avatar mentor & thumbnail 3 foto dokumentasi (klik untuk membuka lightbox).
- **Segmented Pill Tabs:**
  - **Tab 1: `Rincian & AI`** → Menampilkan kartu putih berisi:
    1. *Deskripsi Kegiatan (Final)* dengan badge `✨ Dirapikan AI`
    2. *Output / Hasil Kerja* dengan ikon centang mint
    3. *Kendala di Lapangan*
  - **Tab 2: `Diskusi Mentor (2)`** → Menampilkan riwayat komentar dan catatan revisi dari mentor beserta form balas cepat.

### 3. Menu Form Tambah/Edit Logbook (`/logbook/create`) — *Kombinasi Layar 2 & Panel AI*
- Header menggunakan gaya bersih dengan tombol kembali bulat (`<`) di kiri dan status simpan otomatis (`Draft`) di kanan.
- Input dikelompokkan dalam **Kartu Putih `rounded-[28px]`**:
  - Pilihan Tanggal & Jam Mulai–Selesai (2 kolom bergaya Bento Layar 2).
  - Pilihan Kategori berupa deretan **Pill Pastel** (`Tugas`, `Meeting`, `Belajar`, `Lapangan`).
- **Area Deskripsi + Asisten AI Gemini (Kartu Lavender Grid):**
  - Textarea catatan kasar peserta.
  - Baris tombol aksi kapsul di bawah textarea:
    - `[🎤 Rekam Suara]` (pill putih)
    - `[📷 Saran dari Foto]` (pill putih)
    - `[✨ Rapikan Teks]` (pill hitam `bg-charcoal text-white`)
  - Saat hasil AI keluar, muncul **Segmented Tab** (`Versi Asli` | `Hasil AI: Formal / Singkat / Detail`) dan tombol konfirmasi.

### 4. Menu Kalender & Laporan (`/logbook` & `/reports`) — *Mengadopsi Layar 3*
- **Bagian Atas:** Kalender bulanan persis **Layar 3**:
  - Tanggal yang sudah terisi logbook berwarna **Butter Yellow (`#FBE8A6`)**.
  - Tanggal yang dipilih berwarna **Hitam Charcoal (`#18181B`)**.
  - Tanggal yang berstatus revisi diberi titik merah kecil di sudut atas sel.
- **Bagian Tengah (`Kegiatan Tanggal 16 Nov`):**
  - Menampilkan daftar entri pada tanggal yang diklik lengkap dengan jam (`08.00 - 12.00`) dan tombol pill hitam `Detail` atau `+ Tambah Entri`.
- **Bagian Bawah (`Cetak Laporan PDF & Ringkasan AI`):**
  - Mengadopsi kartu `Delivery App UI Kit` di bagian bawah **Layar 3**:
  - Menampilkan cuplikan **Ringkasan AI Bulanan**, progress kelengkapan hari kerja (`85%`), dan tombol pill hitam `Unduh PDF Bulanan`.

---

## B. Peran: Mentor

Mentor menggunakan sistem desain yang sama agar pengalaman lintas peran tetap konsisten:

1. **Beranda Mentor:**
   - **Hero Lavender Grid:** Menampilkan jumlah entri yang menunggu persetujuan (`12 Entri Menunggu Review` di dalam kotak hitam + ilustrasi 3D).
   - **Daftar Antrean Review:** Menggunakan kartu pastel:
     - **Pastel Periwinkle (`#E6E8FA`):** Entri baru berstatus `Submitted`.
     - **Pastel Butter Yellow (`#FBE8A6`):** Entri yang baru diperbaiki peserta setelah `Revisi`.
2. **Halaman Review Detail (Gaya Layar 2):**
   - Mentor melihat Header Kegiatan, Bento Tanggal & Bukti Foto, serta Segmented Tab `Deskripsi (Asli vs AI)` | `Berikan Komentar`.
   - Di bagian bawah terdapat 2 tombol pill kontras tinggi:
     - Kiri: Tombol `Minta Revisi` (`bg-pastel-butter text-charcoal font-semibold rounded-full`)
     - Kanan: Tombol `✓ Setujui Entri` (`bg-charcoal text-white font-semibold rounded-full`)
   - Tersedia juga tombol **Approve Satu Minggu Sekaligus** di tampilan kalender mingguan peserta.

---

## C. Peran: Admin

1. **Dashboard Admin (Adaptasi Responsif Desktop & Mobile):**
   - Di layar mobile, tetap menggunakan **Floating Neo-Dock** 4 menu.
   - Di layar tablet/desktop (`md:` ke atas), Floating Dock bertransformasi menjadi **Floating Left Sidebar** dengan border `3px solid #18181B` yang ikonik, sementara konten utama menampilkan grid 3 kolom dari kartu-kartu pastel (Statistik Peserta Aktif di kartu Mint, Kuota AI Gemini di kartu Lavender, Kepatuhan Harian di kartu Butter Yellow).
