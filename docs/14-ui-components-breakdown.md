# 14. Breakdown Komponen UI & Variasi Style

Dokumen ini memecah seluruh elemen visual dari [`public/style-design.png`](../public/style-design.png) menjadi **komponen Blade + Livewire + Alpine.js** yang siap digunakan pada proyek Laravel **Timesheet MagangHub**. Setiap komponen memiliki variasi *style* sesuai fungsi di aplikasi logbook.

---

## Daftar Komponen Utama

1. **Top App Bar (`<x-ui.top-bar />`)** — Header profil + tombol aksi bulat
2. **Hero Progress & Jam Kerja (`<x-dashboard.hero-timer />`)** — Kartu Lavender berpola grid + kapsul hitam + ilustrasi 3D
3. **Kartu Logbook Pastel (`<x-logbook.card />`)** — 4 variasi warna pastel (Mint, Periwinkle, Butter Yellow, Lavender)
4. **Header Detail & Bento 2 Kolom (`<x-logbook.detail-header />`)** — Header detail + kartu info kembar
5. **Segmented Pill Tabs (`<x-ui.segmented-tabs />`)** — Tab kapsul `Goals` / `Chat`
6. **Kartu Sub-Item / Checklist (`<x-logbook.sub-item />`)** — Baris aktivitas putih dengan ikon centang mint & badge pastel
7. **Kalender Bulanan Interaktif (`<x-calendar.month-grid />`)** — Grid tanggal dengan highlight kuning mentega & hitam
8. **Kartu Timeline Harian (`<x-logbook.daily-timeline />`)** — Daftar jam kegiatan + tombol pill hitam/putih
9. **Floating Neo-Dock + Detached FAB (`<x-nav.floating-dock />`)** — Navigasi bawah ikonik dengan border hitam tebal
10. **Panel AI Gemini (`<x-ai.polish-sheet />`)** — Adaptasi gaya Lavender Grid + Charcoal Pill untuk fitur AI

---

## 1. Top App Bar (`<x-ui.top-bar />`)

Mengacu pada bagian atas **Layar 1**:
- **Kiri:** Avatar bulat (`w-11 h-11 rounded-full bg-pastel-coral`) + Nama lengkap pengguna (`font-semibold text-gray-900`).
- **Kanan:** Dua tombol ikon bulat putih (`w-10 h-10 rounded-full bg-white flex items-center justify-center`):
  - Lonceng notifikasi dengan titik merah (`w-2 h-2 bg-red-500 rounded-full`) bila ada revisi mentor / pengingat.
  - Tombol pencarian cepat (`🔍`).

```html
<header class="flex items-center justify-between px-5 pt-4 pb-3">
    <div class="flex items-center gap-3">
        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar"
             class="w-11 h-11 rounded-full bg-pastel-coral object-cover ring-2 ring-white" />
        <div>
            <p class="text-xs text-gray-500">Selamat datang,</p>
            <h1 class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</h1>
        </div>
    </div>
    <div class="flex items-center gap-2.5">
        <button class="relative w-10 h-10 rounded-full bg-white flex items-center justify-center text-gray-800 shadow-sm">
            <!-- Bell Icon -->
            <span class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-red-500"></span>
        </button>
        <button class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-gray-800 shadow-sm">
            <!-- Search Icon -->
        </button>
    </div>
</header>
```

---

## 2. Hero Banner & Dark Timer Card (`<x-dashboard.hero-timer />`)

Mengacu pada kartu besar ungu di **Layar 1** (`Mastering projects with management` + `7h 34 m`):
- **Kontainer Luar:** `bg-lavender-grid rounded-card p-5 relative overflow-hidden`
- **Judul Hero:** Sapaan motivasi atau ringkasan periode magang (mis. *" Konsisten catat kegiatan magangmu hari ini"*).
- **Kontainer Dalam (Dark Island):** `bg-charcoal text-white rounded-inner p-4 mt-4 flex items-center justify-between relative`
  - **Teks Utama:** Total jam kerja minggu/bulan ini (`32j 45m` atau `Hari ke-18 / 90`).
  - **Subteks:** Status target (`Target minggu ini hampir tercapai`).
  - **Ilustrasi 3D:** Gambar jam beker 3D warna kuning amber (`w-20 h-20 -mt-6 -mr-1 drop-shadow-lg`) yang sedikit keluar dari batas atas kotak hitam.

---

## 3. Kartu Logbook Pastel (`<x-logbook.card />`) — 4 Variasi Style

Mengacu pada daftar kartu di bawah `Your task` pada **Layar 1**. Di Timesheet MagangHub, warna pastel kartu otomatis merepresentasikan **status logbook** atau **kategori kegiatan**:

| Style Variant | Kelas Latar | Kelas Progress Fill | Digunakan Untuk Status / Kategori |
|---------------|-------------|---------------------|-----------------------------------|
| **Style A: Mint Sage** | `bg-pastel-mint` (`#D5EDE8`) | `bg-pastel-mint-deep` (`#B4DCD3`) | Status **Approved** / Kategori *Tugas & Proyek* |
| **Style B: Periwinkle** | `bg-pastel-periwinkle` (`#E6E8FA`) | `bg-pastel-periwinkle-deep` (`#C7CCF4`) | Status **Submitted (Menunggu Review)** / Kategori *Meeting* |
| **Style C: Butter Yellow** | `bg-pastel-butter` (`#FBE8A6`) | `bg-pastel-butter-deep` (`#F5D061`) | Status **Revisi / Draft** / Kategori *Lapangan & Pelatihan* |
| **Style D: Soft Lavender** | `bg-pastel-lavender` (`#E5D9FA`) | `bg-pastel-lavender-deep` (`#D5C2F7`) | Entri yang dirapikan **AI Gemini** / Ringkasan Mingguan |

### Anatomi Kartu Logbook Pastel:
1. **Baris 1 (Header Kartu):**
   - Kiri: Kotak putih squircle (`w-9 h-9 rounded-squircle bg-white flex items-center justify-center`) berisi ikon kategori + Judul kegiatan (`font-semibold text-[15px] text-gray-900`).
   - Kanan: Tombol edit bulat (`w-8 h-8 rounded-full bg-white/60 flex items-center justify-center`) dengan ikon pensil.
2. **Baris 2 (Status & Prioritas/Kategori):**
   - Indikator jam/durasi (`⏱ 08:30 - 12:00 • 3j 30m` dengan teks abu-abu `#4B5563`).
   - Pill putih (`bg-white px-3 py-1 rounded-full text-xs font-semibold text-gray-900`) berisi status (`⚑ Approved`, `⚑ Submitted`, atau kategori).
3. **Baris 3 (Progress / Kelengkapan Entri):**
   - Track putih (`w-full h-2.5 bg-white/80 rounded-full overflow-hidden`) dengan fill warna pastel lebih tua sesuai kelengkapan dokumentasi/jam.
4. **Baris 4 (Footer Metadata):**
   - Kiri: Thumbnail foto dokumentasi bertumpuk (`flex -space-x-2`) + jumlah lampiran (`+3`).
   - Kanan: Pill tanggal (`bg-white px-3 py-1.5 rounded-full text-xs font-medium` → `📅 16 Nov`) + Pill jumlah komentar mentor (`💬 03`).

---

## 4. Header Detail & Bento 2 Kolom (`<x-logbook.detail-header />`)

Mengacu pada bagian atas **Layar 2**:
1. **Header Kartu Lavender:**
   - Latar `bg-lavender-grid rounded-card p-5`.
   - Kotak ikon putih (`w-11 h-11 rounded-squircle bg-white mb-4`).
   - Judul besar (`text-[22px] font-semibold text-gray-900`).
   - Baris bawah: `Dicatat oleh` + Avatar kecil + Nama Peserta di kiri; tombol bulat Edit (`bg-white/70 w-9 h-9 rounded-full`) di kanan.
2. **Grid Info 2 Kolom (`grid grid-cols-2 gap-3 mt-3`):**
   - **Kartu Kiri (Tanggal & Durasi):** `bg-white rounded-card p-4 text-center` → Label `Tanggal Kegiatan` (`text-xs text-gray-400`), ikon kalender hitam di tengah (`w-8 h-8 mx-auto my-2`), teks tebal `16 November` (`text-sm font-semibold`).
   - **Kartu Kanan (Mentor & Bukti Foto):** `bg-white rounded-card p-4 text-center` → Label `Mentor & Foto` (`text-xs text-gray-400`), tumpukan 3 avatar/thumbnail bulat berwarna pastel (`flex justify-center -space-x-2 mt-2`).

---

## 5. Segmented Pill Tabs (`<x-ui.segmented-tabs />`)

Mengacu pada tab `Goals` | `Chat` di tengah **Layar 2**:
- Kontainer luar: `bg-[#F1F2F6] p-1.5 rounded-full grid grid-cols-2 gap-1`
- Tab Aktif: `bg-white text-gray-900 font-semibold text-sm py-2.5 rounded-full shadow-sm text-center transition`
- Tab Nonaktif: `text-gray-500 font-medium text-sm py-2.5 rounded-full text-center`
- **Penggunaan di Timesheet MagangHub:**
  - Di Detail Logbook: Tab **`Rincian & AI`** vs **`Komentar Mentor (3)`**
  - Di Halaman Form AI: Tab **`Teks Asli`** vs **`Hasil AI ✨`**
  - Di Halaman Mentor: Tab **`Menunggu Review`** vs **`Sudah Disetujui`**

---

## 6. Kartu Sub-Item / Rincian Aktivitas (`<x-logbook.sub-item />`)

Mengacu pada daftar item putih (`Design system`, `Landing Page`, `Pricing Page`) di bawah tab pada **Layar 2**:
- Kontainer: `bg-white rounded-card p-4 flex items-center justify-between`
- Kiri: Lingkaran centang mint (`w-9 h-9 rounded-full bg-pastel-mint text-[#2D8A78] flex items-center justify-center`).
- Tengah: Judul item (`font-semibold text-[15px] text-gray-900`) + Subjudul/output (`text-xs text-gray-400`).
- Kanan: Badge pill pastel (`bg-pastel-lavender` untuk *Utama* atau `bg-pastel-mint` untuk *Selesai*, `px-3 py-1 rounded-full text-xs font-semibold`) + tombol titik tiga (`⋮`).

---

## 7. Kalender Bulanan Interaktif (`<x-calendar.month-grid />`)

Mengacu pada bagian atas **Layar 3** (`November 2025`):
- **Header Navigasi Bulan:** Tombol bulat putih kiri (`<`), judul bulan & tahun tebal di tengah (`November 2026`), tombol bulat putih kanan (`>`).
- **Kontainer Kalender:** `bg-white rounded-card p-4` dengan grid 7 kolom (`grid grid-cols-7 gap-2 text-center`).
- **4 Status Visual Sel Tanggal (`w-10 h-10 rounded-squircle flex items-center justify-center text-xs font-semibold`):**
  1. **Tanggal Bulan Lain (Out of Month):** `bg-[#F3F4F6] text-gray-400` (angka `1, 2, 3, 4` di baris pertama/terakhir).
  2. **Hari Kerja Kosong / Belum Diisi:** `bg-transparent text-gray-800 hover:bg-gray-100`.
  3. **Hari Sudah Mengisi Logbook (Filled/Approved):** `bg-pastel-butter text-gray-900` (`#FBE8A6` kuning mentega seperti tanggal `12, 14, 18, 20, 25, 28`).
  4. **Tanggal yang Sedang Dipilih / Hari Ini:** `bg-charcoal text-white shadow-sm` (`#18181B` hitam pekat seperti tanggal `10`).

---

## 8. Kartu Jadwal / Entri Hari Ini (`<x-logbook.daily-timeline />`) & Kartu Progress Rekap

Mengacu pada bagian tengah dan bawah **Layar 3**:
1. **Kartu `Today's meeting` (Entri Hari Ini):**
   - Kontainer: `bg-white rounded-card p-5`
   - Judul: `Kegiatan Hari Ini` (`font-semibold text-[16px] mb-3`)
   - Baris Item: Avatar kecil + ikon jam + rentang waktu (`08.30 - 11.30 WIB`) di kiri; tombol kapsul di kanan:
     - Tombol Utama (Hitam): `bg-charcoal text-white text-xs font-medium px-4 py-2 rounded-full` (mis. `Lihat Detail` / `Isi Sekarang`)
     - Tombol Sekunder (Putih): `bg-white border border-gray-200 text-gray-800 text-xs font-medium px-4 py-2 rounded-full`
2. **Kartu `Delivery App UI Kit` (Progress Periode Magang / Laporan Bulanan):**
   - Kontainer: `bg-white rounded-card p-5`
   - Kanan atas: Kotak ikon squircle ungu muda (`w-9 h-9 rounded-squircle bg-pastel-lavender`).
   - Judul + 2 baris ringkasan AI mingguan/bulanan.
   - Bawah: Tumpukan thumbnail foto + persentase `65%` di kanan + progress bar ungu (`h-2.5 rounded-full bg-pastel-lavender` dengan fill `bg-charcoal` atau `#6D28D9`).

---

## 9. Floating Neo-Dock Navigation + Detached FAB (`<x-nav.floating-dock />`)

Komponen paling khas di bagian bawah **Layar 1 dan Layar 3**:
- Posisi: `fixed bottom-5 inset-x-0 z-50 flex items-center justify-center gap-2.5 px-5 pointer-events-none`
- **Bagian Kiri (Main Dock Pill):**
  - `pointer-events-auto flex items-center gap-1.5 bg-white border-[3px] border-charcoal rounded-dock p-1.5 shadow-lg`
  - Berisi 4 tab ikon (`w-11 h-10 rounded-xl flex items-center justify-center`):
    1. **Beranda** (ikon rumah)
    2. **Kalender / Logbook** (ikon kalender)
    3. **Laporan & Statistik** (ikon grafik dalam kotak)
    4. **Profil** (ikon pengguna)
  - Tab yang aktif memiliki latar `bg-[#F1F2F6] text-charcoal`.
- **Bagian Kanan (Detached Squircle FAB `+`):**
  - `pointer-events-auto w-[52px] h-[52px] bg-white border-[3px] border-charcoal rounded-dock flex items-center justify-center shadow-lg active:scale-95 transition`
  - Ikon: Lingkaran dengan tanda tambah (`⊕`) di tengah untuk langsung membuka **Form Tambah Logbook / Rekam Suara AI**.

---

## 10. Komponen Khusus AI Gemini (`<x-ai.polish-sheet />`)

Agar fitur AI Gemini (Rapikan Teks, Voice-to-Log, Saran Foto) menyatu 100% dengan `style-design.png`:
- **Tombol Trigger AI di Form:** Menggunakan kapsul hitam `bg-charcoal text-white rounded-full px-4 py-2 text-xs font-semibold flex items-center gap-1.5` (`✨ Rapikan dengan AI`) dan kapsul lavender `bg-pastel-lavender text-charcoal rounded-full px-4 py-2 text-xs font-semibold` (`🎤 Dikte Suara`).
- **Pilihan Gaya Bahasa (Formal, Singkat, Detail):** Menggunakan komponen **Segmented Pill Tabs** (`bg-[#F1F2F6] rounded-full p-1`).
- **Kartu Hasil AI:** Menggunakan latar **Pastel Lavender Grid (`bg-lavender-grid rounded-card p-5`)** dengan kapsul perbandingan `Teks Asli` vs `Hasil AI` serta tombol konfirmasi hitam `Terapkan ke Logbook`.
