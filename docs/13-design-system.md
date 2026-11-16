# 13. Design System & Style Guide (Berbasis `style-design.png`)

Dokumen ini menjabarkan sistem desain visual untuk **Timesheet MagangHub (PWA)** yang diekstrak langsung dari referensi [`public/style-design.png`](../public/style-design.png). Gaya utama mengusung konsep **Soft Pastel Bento & High-Contrast Pill UI** yang ramah di mata, modern, dan sangat optimal untuk layar mobile.

---

## 1. Karakteristik Utama Visual (Visual DNA)

Berdasarkan 3 layar utama pada `style-design.png`, identitas visual aplikasi memiliki 6 ciri khas utama:

1. **Soft Pastel Surface Cards:** Setiap kartu entri/kegiatan tidak menggunakan warna putih polos yang membosankan, melainkan diberi latar warna pastel lembut (**Lavender**, **Mint/Sage**, **Periwinkle Blue**, **Butter Yellow**) untuk membedakan kategori atau status kegiatan secara instan.
2. **Subtle Tonal Checker/Grid Pattern:** Kartu utama (Hero Banner & Header Detail) menggunakan latar **Pastel Lavender (`#E5D9FA`)** yang dilapisi motif kotak-kotak (*checkerboard/grid*) transparan halus untuk memberi kedalaman tekstur.
3. **High-Contrast Dark Charcoal Islands:** Elemen aksi utama, tanggal aktif, kartu jam kerja (*time tracker* `7h 34m`), dan tombol CTA utama menggunakan warna **Charcoal Hitam Pekat (`#18181B`)** dengan teks putih agar langsung menarik perhatian mata (*focal point*).
4. **Super-Rounded Bento Geometry:** Semua kontainer kartu menggunakan sudut membulat ekstra besar (`24px`–`28px` / `rounded-3xl`), dipadukan dengan elemen badge dan tombol berbentuk kapsul penuh (`rounded-full`).
5. **Signature Floating Dock + Detached FAB:** Navigasi bawah tidak menempel penuh di tepi bawah layar, melainkan melayang (*floating*) dengan **border tebal hitam (`3px solid #18181B`)** yang terpisah menjadi 2 bagian: **Pill 4 Menu Utama** di kiri dan **Tombol Squircle `+` (Tambah Logbook)** di kanan.
6. **3D Pop-out Micro-Illustrations:** Penggunaan aksen ilustrasi 3D hangat (seperti jam beker kuning amber `#F59E0B` yang menonjol keluar dari kartu gelap) untuk memberikan kesan hidup dan *playful*.

---

## 2. Palet Warna (Color Tokens)

### A. Warna Latar & Permukaan (Canvas & Surfaces)

| Token | Hex | Tailwind Class | Penggunaan pada `style-design.png` |
|-------|-----|----------------|------------------------------------|
| `bg-canvas` | `#F6F7F9` | `bg-[#F6F7F9]` | Latar belakang utama layar aplikasi (abu-abu sejuk sangat muda) |
| `bg-surface` | `#FFFFFF` | `bg-white` | Kartu kalender, kartu info 2 kolom (Deadline/People), kartu sub-item, pill putih |
| `bg-muted` | `#F1F2F6` | `bg-[#F1F2F6]` | Latar track *segmented tab* (`Goals` / `Chat`), tanggal bulan lain di kalender |
| `bg-dark-island` | `#18181B` | `bg-[#18181B]` | Kartu gelap *timer* (`7h 34m`), tanggal aktif (`10`), tombol utama (`Join meet`), border navigasi bawah |

### B. Warna Kartu Pastel (Pastel Card Accents)

| Nama Gaya | Surface Hex | Aksen/Progress Hex | Penggunaan di Timesheet MagangHub |
|-----------|-------------|--------------------|-----------------------------------|
| **Pastel Lavender** | `#E5D9FA` | `#D6C4F7` (grid) / `#8B5CF6` | Hero card beranda, Header detail logbook, Badge kategori/prioritas *High*, Panel fitur AI Gemini |
| **Pastel Mint / Sage** | `#D5EDE8` | `#B2DDD4` (bar) / `#2D8A78` | Kartu logbook status **Approved** / kategori *Tugas Utama*, ikon centang selesai |
| **Pastel Periwinkle** | `#E6E8FA` | `#C7CCF4` (bar) / `#4F5BD5` | Kartu logbook status **Submitted** / kategori *Meeting & Diskusi* |
| **Pastel Butter Yellow** | `#FBE8A6` | `#F5D061` / `#D97706` | Highlight tanggal terisi pada kalender, kartu logbook status **Draft / Revisi** |
| **Pastel Coral Pink** | `#F8C8C4` | `#EF4444` | Latar avatar pengguna, indikator dot notifikasi merah |

### C. Warna Tipografi (Text Colors)

| Token | Hex | Penggunaan |
|-------|-----|------------|
| `text-primary` | `#111827` | Judul halaman, judul kartu, angka jam, tanggal kalender |
| `text-secondary` | `#6B7280` | Subjudul (`Created by`, `Ongoing`, jam meeting, label `Deadline`) |
| `text-muted` | `#9CA3AF` | Tanggal di luar bulan aktif, placeholder input |
| `text-on-dark` | `#FFFFFF` | Teks di atas `bg-dark-island` (`7h 34m`, tombol hitam) |
| `text-on-dark-muted` | `#A1A1AA` | Subteks di dalam kartu hitam (`Your task almost done`) |

---

## 3. Tipografi (Typography Scale)

Font yang paling mendekati karakter geometris, bersih, dan ramah pada `style-design.png` adalah **Plus Jakarta Sans** (atau **DM Sans** / **Inter**).

```html
<!-- Google Fonts di layouts/app.blade.php -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
```

| Elemen | Ukuran | Ketebalan | Line Height | Contoh di `style-design.png` |
|--------|--------|-----------|-------------|------------------------------|
| **Hero Stat / Jam** | `24px` (`text-2xl`) | `700` (`font-bold`) | `1.2` | `7h 34 m` |
| **Judul Detail Halaman** | `22px` (`text-[22px]`) | `600` (`font-semibold`) | `1.3` | `Website Design` (Layar 2) |
| **Judul Hero Banner** | `20px` (`text-xl`) | `600` (`font-semibold`) | `1.35` | `Mastering projects with management` |
| **Judul Seksi / Bulan** | `17px` (`text-[17px]`) | `600` (`font-semibold`) | `1.4` | `Your task`, `November 2025`, `Today's meeting` |
| **Judul Kartu / Item** | `15px` (`text-[15px]`) | `600` (`font-semibold`) | `1.4` | `Design system`, `Delivery App UI Kit` |
| **Body / Nama User** | `14px` (`text-sm`) | `500` (`font-medium`) | `1.5` | `Esther Howard`, `6 August` |
| **Caption / Badge / Pill** | `12px` (`text-xs`) | `500`–`600` | `1.4` | `⚑ High`, `12 January`, `1.30 AM - 2.00 AM`, `Join meet` |

---

## 4. Sudut Membulat, Border, & Bayangan (Radius, Borders & Shadows)

Desain ini **minim bayangan gelap (drop shadow)** dan mengandalkan **kontras warna latar kartu** terhadap kanvas `#F6F7F9`.

| Komponen | Radius | Kelas Tailwind | Catatan |
|----------|--------|----------------|---------|
| Kartu Utama (Hero, Task, Kalender) | `28px` | `rounded-[28px]` | Sudut ekstra lembut khas kartu utama |
| Kartu Dalam (Dark Timer, Bento 2 Kolom) | `22px` | `rounded-[22px]` | Sedikit lebih kecil agar proporsional di dalam kartu |
| Floating Dock & Tombol `+` | `20px` | `rounded-[20px]` | Ditambah `border-[3px] border-[#18181B]` |
| Kotak Ikon & Sel Tanggal Kalender | `14px` | `rounded-[14px]` | Squircle untuk ikon kiri atas dan angka tanggal |
| Tombol, Badge Status, Segmented Tab | `9999px` | `rounded-full` | Bentuk kapsul penuh (pill) |

---

## 5. Pola Latar & Efek Khusus (CSS Recipes)

### A. Pola Grid/Checkerboard Halus pada Kartu Lavender
Pada layar 1 dan 2, kartu ungu memiliki pola kotak-kotak tonal di sisi kanan. Berikut implementasi CSS murninya:

```css
/* resources/css/app.css */
@layer utilities {
  .bg-lavender-grid {
    background-color: #E5D9FA;
    background-image: 
      linear-gradient(45deg, rgba(196, 173, 240, 0.35) 25%, transparent 25%), 
      linear-gradient(-45deg, rgba(196, 173, 240, 0.35) 25%, transparent 25%), 
      linear-gradient(45deg, transparent 75%, rgba(196, 173, 240, 0.35) 75%), 
      linear-gradient(-45deg, transparent 75%, rgba(196, 173, 240, 0.35) 75%);
    background-size: 64px 64px;
    background-position: 0 0, 0 32px, 32px -32px, -32px 0;
  }
}
```

### B. Floating Neo-Dock Navigation
Navigasi bawah memiliki garis tepi hitam tegas (`#18181B`) yang membuatnya tampak kontras di atas kartu pastel apa pun saat di-scroll:

```css
.floating-dock-pill {
  background: #FFFFFF;
  border: 3px solid #18181B;
  border-radius: 20px;
  box-shadow: 0 10px 25px -5px rgba(24, 24, 27, 0.12);
}
```

---

## 6. Konfigurasi `tailwind.config.js`

```js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                canvas: '#F6F7F9',
                charcoal: {
                    DEFAULT: '#18181B',
                    soft: '#27272A',
                },
                pastel: {
                    lavender: '#E5D9FA',
                    'lavender-deep': '#D5C2F7',
                    mint: '#D5EDE8',
                    'mint-deep': '#B4DCD3',
                    periwinkle: '#E6E8FA',
                    'periwinkle-deep': '#C7CCF4',
                    butter: '#FBE8A6',
                    'butter-deep': '#F5D061',
                    coral: '#F8C8C4',
                },
            },
            borderRadius: {
                'card': '28px',
                'inner': '22px',
                'dock': '20px',
                'squircle': '14px',
            },
        },
    },
    plugins: [forms],
};
```

---

## 7. Adaptasi Dark Mode (Opsional Sesuai Dokumen 04)

Meskipun `style-design.png` menampilkan tema terang (*Light Pastel Mode*) sebagai identitas utama, ketika pengguna mengaktifkan **Dark Mode**, pemetaan warnanya diadaptasi agar tetap mempertahankan nuansa pastel lembut di atas latar gelap:

| Elemen | Light Mode (`style-design.png`) | Dark Mode Adaptation |
|--------|---------------------------------|----------------------|
| Canvas | `#F6F7F9` | `#0F1115` |
| Kartu Putih (`bg-white`) | `#FFFFFF` | `#181B22` |
| Pastel Lavender Card | `#E5D9FA` (teks `#111827`) | `#2A223D` (border `#4C3B75`, teks `#F3E8FF`) |
| Pastel Mint Card | `#D5EDE8` (teks `#111827`) | `#18312E` (border `#28544E`, teks `#CCFBF1`) |
| Pastel Periwinkle Card | `#E6E8FA` (teks `#111827`) | `#1E243B` (border `#35416B`, teks `#E0E7FF`) |
| Pastel Butter Yellow | `#FBE8A6` (teks `#111827`) | `#382F16` (border `#665422`, teks `#FEF3C7`) |
| Floating Dock | `bg-white border-[#18181B]` | `bg-[#181B22] border-white/20` |
