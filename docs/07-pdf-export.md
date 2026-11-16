# 07. Ekspor PDF

## Jenis Laporan

| Jenis | Isi |
|-------|-----|
| Harian | Satu hari, semua entri + foto |
| Mingguan | Tabel kegiatan Senin-Jumat, total jam, ringkasan AI, foto pilihan |
| Bulanan | Rekap per minggu, statistik kategori, ringkasan AI, lampiran foto |
| Rentang bebas | Tanggal awal-akhir pilihan |
| Laporan akhir | Ringkasan seluruh magang, skill, capaian, dokumentasi terpilih |

## Struktur Dokumen

1. **Kop**: logo instansi, nama instansi, judul laporan
2. **Identitas**: nama, NIM/NIK, kampus, divisi, mentor, periode
3. **Ringkasan** (opsional, dari AI)
4. **Tabel kegiatan**: No · Tanggal · Jam · Kegiatan · Output · Status
5. **Rekap**: total jam, jumlah hari terisi, jumlah entri, grafik kategori sederhana
6. **Lampiran dokumentasi**: grid foto (2-4 per halaman) dengan caption dan tanggal
7. **Tanda tangan**: peserta dan mentor (gambar tanda tangan atau kolom kosong)
8. **QR verifikasi** + nomor dokumen di footer

## Opsi Cetak (dipilih pengguna)
- Sertakan foto: tidak / 1 foto per entri / semua
- Sertakan ringkasan AI: ya/tidak
- Hanya entri berstatus Approved: ya/tidak
- Ukuran kertas: A4 (default)
- Orientasi: portrait (landscape untuk tabel lebar)

## Implementasi

```
ReportService::build($internship, $type, $start, $end, $options)
   → ambil logbooks + attachments (eager load)
   → hitung rekap (SUM duration, COUNT, group by category)
   → [opsional] ambil/buat ai_summary
   → dispatch GeneratePdfJob($report)

GeneratePdfJob
   → PdfService::render(view("pdf.$type"), $data)
   → simpan storage/app/reports/{user}/{id}.pdf
   → update reports.status = ready, kirim notifikasi
```

Contoh:
```php
$pdf = Pdf::loadView('pdf.monthly', $data)->setPaper('a4', 'portrait');
Storage::put($path, $pdf->output());
```

## Template Blade
```
resources/views/pdf/
├── layout.blade.php      # kop, footer, CSS
├── daily.blade.php
├── weekly.blade.php
├── monthly.blade.php
├── custom.blade.php
├── final.blade.php
└── partials/
    ├── identity.blade.php
    ├── activity-table.blade.php
    ├── photo-grid.blade.php
    └── signature.blade.php
```

## Optimasi Memori (penting di shared hosting)
- Pakai **thumbnail/foto terkompres** (maks 800px) di PDF, bukan foto asli
- Batasi foto per entri (default 1-2) dan total foto per PDF
- Proses via **queue**, bukan di request web
- Naikkan `memory_limit` sementara di job (`ini_set('memory_limit','512M')`) jika diizinkan
- Pecah laporan besar per bagian lalu gabung, atau batasi rentang maksimal 1 bulan per file
- Cache PDF periode yang sudah lewat dan berstatus approved
- Hindari CSS kompleks (flex/grid tidak sepenuhnya didukung DomPDF, pakai `table` dan `float`)
- Font: tanam font (mis. DejaVu Sans / Poppins) agar karakter dan tampilan konsisten

## Verifikasi Keaslian
- Tiap laporan punya `verify_token`
- QR mengarah ke `https://domain/verify/{token}`
- Halaman publik menampilkan: nama peserta, periode, tanggal dibuat, status approval (tanpa data sensitif)

## Pengujian
- Uji PDF dengan 0 entri, 1 entri, 31 hari penuh, dan entri dengan 5 foto
- Uji karakter khusus (nama dengan apostrof, emoji dihapus)
- Uji teks panjang agar tabel tidak overflow
