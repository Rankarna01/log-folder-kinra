# 04. Fitur per Menu

## Peta Navigasi (Gaya Floating Neo-Dock `style-design.png`)

Mengikuti desain [`public/style-design.png`](../public/style-design.png) (lihat detail di [14-ui-components-breakdown.md](14-ui-components-breakdown.md) & [15-screen-style-mapping.md](15-screen-style-mapping.md)):

**Peserta** (Floating Dock berborder hitam `3px` + Tombol `+` Squircle terpisah di kanan)
`[ 🏠 Beranda · 📅 Kalender/Logbook · 📊 Laporan · 👤 Profil ]` + `[ ⊕ Tambah ]`

**Mentor** : `[ 🏠 Beranda · ✅ Review · 👥 Peserta · 👤 Profil ]` + `[ 📄 Cetak ]`

**Admin** : `Dashboard` · `Pengguna` · `Instansi & Periode` · `Rekap` · `Pengaturan`

---

## A. Peserta

### 1. Beranda (Dashboard)
- Sapaan + status hari ini (sudah/belum isi logbook)
- Kartu ringkas: total jam minggu ini, jumlah entri bulan ini, streak hari berturut-turut
- Progress magang (hari ke-X dari Y) dengan progress bar
- Kalender mini: hari terisi (hijau), kosong (merah), libur (abu), menunggu review (kuning)
- Pintasan: "Isi logbook hari ini", "Rekam suara"
- Pengingat jika ada entri berstatus **Revisi** dari mentor
- Grafik jam per kategori (minggu/bulan)

### 2. Logbook

**Daftar**
- Filter: tanggal/rentang, status, kategori; pencarian teks
- Tampilan: list per hari dengan thumbnail foto
- Badge status: Draft · Submitted · Approved · Revisi
- Pagination atau infinite scroll

**Form Tambah/Edit**
| Field | Keterangan |
|-------|-----------|
| Tanggal | Default hari ini; boleh mundur sesuai batas yang diatur admin |
| Jam mulai / selesai | Durasi dihitung otomatis |
| Judul kegiatan | Wajib |
| Kategori | Tugas, Meeting, Belajar, Pelatihan, Lapangan, Administrasi, Lainnya |
| Deskripsi | Teks bebas atau dikte suara |
| Hasil/Output | Opsional (dokumen, fitur, laporan yang dihasilkan) |
| Kendala | Opsional |
| Foto/dokumentasi | 1-5 foto, kamera langsung atau galeri, caption per foto |
| Lokasi | Opsional (WFO/WFH/Lapangan), geotag opsional |

**Aksi AI di form**
- Tombol **✨ Rapikan** (pilih gaya: Formal, Singkat, Detail)
- Tombol **🎤 Rekam** (voice-to-log)
- Tombol **📷 Saran dari foto**
- Panel perbandingan: Teks asli vs Hasil AI, tombol *Terapkan* / *Edit* / *Batal*
- Indikator kualitas entri ("Terlalu singkat, tambahkan hasil kerja")

**Detail Entri**
- Semua data, galeri foto (lightbox), riwayat komentar mentor, riwayat status

**Aturan**
- Edit/hapus hanya selama status Draft atau Revisi
- Satu hari boleh banyak entri
- Foto otomatis dikompres + diberi watermark (tanggal, jam, nama)

### 3. Laporan
- Pilih jenis: Harian · Mingguan · Bulanan · Rentang bebas · Laporan akhir
- Opsi: sertakan foto (ya/tidak, 1-3 per entri), sertakan ringkasan AI, hanya entri Approved
- Preview sebelum cetak
- Riwayat laporan yang sudah dibuat (unduh ulang)
- **Ringkasan AI mingguan/bulanan** (narasi rapi dari seluruh entri)
- **Draf laporan akhir magang** (AI)
- **Ekstrak skill** (daftar kompetensi yang diperoleh)

### 4. Statistik (di Beranda atau tab sendiri)
- Jam per kategori, entri per minggu, konsistensi pengisian
- Rasio Approved vs Revisi

### 5. Profil & Pengaturan
- Data diri: nama, NIM/NIK, kampus, jurusan, kontak
- Data magang: instansi, divisi, mentor, tanggal mulai-selesai, hari kerja
- Preferensi: gaya AI default, bahasa (ID/EN), dark mode
- Notifikasi: jam pengingat harian, aktif/nonaktif push
- Ganti password, unggah foto profil dan tanda tangan (gambar)
- Ekspor seluruh data (Excel/CSV)

---

## B. Mentor

### 1. Beranda
- Jumlah entri menunggu review, peserta belum mengisi hari ini

### 2. Review
- Antrean entri berstatus Submitted
- Aksi: **Approve**, **Minta revisi** (wajib komentar), komentar saja
- Approve massal per minggu per peserta
- Lihat teks asli dan versi AI secara berdampingan

### 3. Peserta Bimbingan
- Daftar peserta, persentase kepatuhan pengisian, total jam
- Detail logbook per peserta dan kalender

### 4. Laporan
- Cetak rekap peserta bimbingan
- Tanda tangan digital / QR verifikasi pada laporan yang di-approve

---

## C. Admin

### 1. Dashboard
- Total peserta aktif, tingkat pengisian hari ini, entri belum direview, penggunaan AI

### 2. Pengguna
- CRUD user (peserta, mentor), impor CSV, reset password, aktif/nonaktif

### 3. Instansi & Periode Magang
- Kelola instansi, divisi, penempatan, periode magang, hari libur

### 4. Rekap
- Rekap seluruh peserta (filter instansi/periode), ekspor PDF/Excel
- Daftar peserta yang tidak aktif mengisi

### 5. Pengaturan
- Batas backdate entri, batas jumlah foto, kategori kegiatan
- Template/kop PDF (logo, nama instansi)
- Kuota AI per pengguna, pilihan model Gemini, prompt template
- Log aktivitas (audit trail)

---

## D. Fitur Lintas Menu

| Fitur | Detail |
|-------|--------|
| Notifikasi | Pengingat harian, status review, PDF siap |
| Offline mode | Isi logbook tanpa internet, sinkron otomatis |
| Template kegiatan | Simpan kegiatan berulang, pakai ulang sekali tap |
| Duplikat entri | Salin entri kemarin sebagai dasar |
| Pencarian global | Cari kegiatan di semua entri |
| QR verifikasi | Keaslian PDF bisa dicek lewat URL publik |
| Dark mode | Ikuti sistem atau manual |
| Multi-bahasa | ID/EN |
