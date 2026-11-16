# 10. Keamanan dan Privasi

## Autentikasi dan Sesi
- Password di-hash (bcrypt/argon2, bawaan Laravel)
- Rate limit login (5/menit), lockout sementara
- Verifikasi email dan reset password
- Sesi `secure`, `httpOnly`, `sameSite=lax`
- Opsional: Google Login, 2FA untuk admin

## Otorisasi (Policy)

| Aksi | Aturan |
|------|--------|
| Lihat logbook | Pemilik, mentor penempatan tersebut, admin |
| Ubah/hapus logbook | Pemilik, hanya saat status Draft atau Revisi |
| Approve/revisi | Mentor dari penempatan tersebut |
| Unduh laporan | Pemilik, mentor terkait, admin |
| Kelola user/pengaturan | Admin |

Selalu cek policy di server, jangan mengandalkan penyembunyian tombol di UI.

## Keamanan Upload

- Validasi **mime sebenarnya** (bukan hanya ekstensi): jpeg, png, webp untuk gambar; webm, mp4, ogg, m4a untuk audio
- Batas ukuran (gambar maks 5 MB sebelum server kompres, audio maks 2 MB)
- Ganti nama file dengan UUID, jangan pakai nama asli
- Proses ulang gambar lewat library (re-encode) untuk membuang payload tersembunyi dan EXIF sensitif (lokasi), kecuali yang memang dibutuhkan
- Simpan di `storage/app` (private), **bukan** di `public/`
- Sajikan lewat route terautentikasi atau signed URL berumur pendek
- Larang eksekusi PHP di folder upload

## Proteksi Aplikasi Web
- CSRF aktif (bawaan Laravel/Livewire)
- Escape output Blade (`{{ }}`), hindari `{!! !!}` untuk input pengguna
- Content Security Policy dasar, header `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`
- Paksa HTTPS (redirect + HSTS)
- Mass assignment: gunakan `$fillable`
- Pakai Eloquent/query binding, hindari raw query dari input
- `APP_DEBUG=false`, `.env` tidak dapat diakses publik
- Update dependency berkala (`composer audit`, `npm audit`)

## Keamanan Integrasi AI
- API key Gemini hanya di server (`.env`), **jangan pernah** di JavaScript
- Semua panggilan AI lewat backend dengan kuota dan throttle
- Waspadai **prompt injection**: teks pengguna diperlakukan sebagai data. Instruksi sistem dipisah di `systemInstruction`, hasil AI selalu di-escape saat ditampilkan
- Jangan kirim data pribadi yang tidak perlu (NIM, email, nomor HP) ke Gemini
- Catat penggunaan di `ai_usages`, tanpa menyimpan isi sensitif secara berlebihan

## Privasi Data
- Foto dan audio dapat memuat informasi internal instansi → akses terbatas, tidak publik
- Audio dihapus segera setelah transkripsi
- Pemberitahuan di halaman profil: data diproses layanan Google (Gemini) saat fitur AI dipakai
- Opsi admin menonaktifkan AI foto/suara
- Hak pengguna: ekspor data dan hapus akun
- Halaman verifikasi PDF publik hanya menampilkan data minimal
- Retensi: tentukan masa simpan (mis. 1 tahun setelah magang selesai) lalu arsip atau hapus

## Audit dan Pemantauan
- Audit trail perubahan status logbook (siapa, kapan)
- Log error ke `storage/logs` dengan rotasi harian
- Notifikasi admin bila error AI atau job gagal berulang
- Backup database harian, backup storage mingguan

## Checklist Sebelum Rilis
- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] HTTPS dan HSTS aktif
- [ ] Folder `storage` dan `.env` tidak bisa diakses dari web
- [ ] Policy diuji untuk tiga peran
- [ ] Validasi upload diuji dengan file berbahaya
- [ ] Throttle AI dan login aktif
- [ ] Backup otomatis berjalan
- [ ] Kunci API tidak ada di repo (`.gitignore`)
