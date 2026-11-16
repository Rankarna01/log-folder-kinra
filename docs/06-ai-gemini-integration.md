# 06. Integrasi AI (Google Gemini)

## Prinsip
1. AI adalah **asisten**, bukan penulis otomatis: peserta selalu bisa menerima, mengedit, atau menolak.
2. **Teks asli tidak pernah ditimpa** (`raw_description` tetap disimpan).
3. **Jangan mengarang fakta:** AI hanya merapikan, tidak menambah kegiatan yang tidak disebut.
4. Semua panggilan dibatasi kuota harian dan dicatat di `ai_usages`.
5. Jika Gemini gagal, aplikasi tetap berfungsi tanpa AI.

## Fitur AI

| Fitur | Input | Output | Eksekusi |
|-------|-------|--------|----------|
| Rapikan teks | Teks kasar + gaya | Deskripsi rapi | Sync (timeout pendek) |
| Voice-to-log | Audio (webm/mp4/ogg) | Transkrip + draf rapi | Sync untuk audio pendek, queue jika lebih panjang |
| Saran dari foto | 1-3 foto | Draf deskripsi + caption | Sync |
| Deteksi entri lemah | Entri | Skor + saran | Sync (ringan) atau rule-based dulu |
| Ringkasan mingguan | Entri 1 minggu | Narasi + poin utama | Queue |
| Ringkasan bulanan | Entri 1 bulan | Narasi + capaian + kendala | Queue |
| Ekstrak skill | Seluruh entri | Daftar skill + kategori | Queue |
| Draf laporan akhir | Seluruh entri/ringkasan | Laporan terstruktur | Queue |

## Konfigurasi

`config/services.php`
```php
'gemini' => [
    'key'     => env('GEMINI_API_KEY'),
    'model'   => env('GEMINI_MODEL', 'gemini-flash-latest'),
    'timeout' => env('GEMINI_TIMEOUT', 30),
    'base'    => 'https://generativelanguage.googleapis.com/v1beta',
],
```

## GeminiService (kerangka)

```php
<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    public function generate(array $parts, ?string $system = null, bool $json = false, float $temp = 0.3): string
    {
        $cfg = config('services.gemini');
        $url = "{$cfg['base']}/models/{$cfg['model']}:generateContent";

        $payload = [
            'contents' => [['role' => 'user', 'parts' => $parts]],
            'generationConfig' => [
                'temperature' => $temp,
                'maxOutputTokens' => 1024,
            ],
        ];

        if ($system) {
            $payload['systemInstruction'] = ['parts' => [['text' => $system]]];
        }
        if ($json) {
            $payload['generationConfig']['responseMimeType'] = 'application/json';
        }

        $res = Http::withHeaders(['x-goog-api-key' => $cfg['key']])
            ->timeout($cfg['timeout'])
            ->retry(2, 500, throw: false)
            ->post($url, $payload);

        if ($res->failed()) {
            throw new RuntimeException('Gemini error: ' . $res->status());
        }

        return $res->json('candidates.0.content.parts.0.text') ?? '';
    }

    public function polish(string $raw, string $style = 'formal'): string
    {
        return $this->generate(
            [['text' => $raw]],
            PromptBuilder::polishSystem($style)
        );
    }

    public function transcribe(string $audioPath, string $mime): string
    {
        $parts = [
            ['text' => PromptBuilder::transcribeInstruction()],
            ['inline_data' => [
                'mime_type' => $mime,
                'data' => base64_encode(file_get_contents($audioPath)),
            ]],
        ];
        return $this->generate($parts, temp: 0.0);
    }

    public function describePhoto(string $imagePath, string $mime): string
    {
        $parts = [
            ['text' => PromptBuilder::photoInstruction()],
            ['inline_data' => [
                'mime_type' => $mime,
                'data' => base64_encode(file_get_contents($imagePath)),
            ]],
        ];
        return $this->generate($parts);
    }
}
```

> Detail nama field dan parameter bisa berubah. Verifikasi ke dokumentasi resmi Gemini API sebelum implementasi.

## Prompt Template

### Rapikan teks (system instruction)
```
Kamu asisten penulisan logbook magang berbahasa Indonesia.
Tugas: ubah catatan kasar menjadi deskripsi kegiatan yang rapi dan jelas.

Aturan:
- Gunakan bahasa Indonesia baku sesuai gaya: {gaya}.
- JANGAN menambah kegiatan, angka, nama, atau hasil yang tidak ada di catatan.
- Perbaiki typo, singkatan, dan istilah teknis.
- Gunakan kalimat aktif, diawali kata kerja (Menyusun, Menganalisis, Menghadiri, dst.).
- Panjang: {singkat: 1 kalimat | formal: 2-3 kalimat | detail: 4-6 kalimat}.
- Keluarkan hanya teksnya, tanpa pembuka atau penutup.
```

### Transkripsi suara
```
Transkripsikan audio ini ke teks bahasa Indonesia apa adanya.
Pertahankan istilah teknis berbahasa Inggris.
Hapus kata pengisi (eh, emm, anu). Jangan menambah isi.
Keluarkan hanya transkrip.
```

### Saran dari foto
```
Lihat foto kegiatan magang ini. Buat 1 kalimat caption netral dan
1 saran deskripsi kegiatan. Jangan menebak identitas orang.
Jika foto tidak jelas, katakan "Tidak cukup informasi".
Keluarkan JSON: {"caption": "...", "suggestion": "..."}
```

### Ringkasan mingguan / bulanan
```
Berikut entri logbook magang periode {periode}.
Buat ringkasan dalam bahasa Indonesia formal:
1. Ringkasan umum (1 paragraf)
2. Kegiatan utama (poin-poin, dikelompokkan per kategori)
3. Capaian/output
4. Kendala dan solusi (jika ada)
5. Skill yang dipelajari
Hanya gunakan informasi dari entri. Jangan mengarang.
Keluarkan JSON dengan kunci: overview, main_activities[], outputs[], obstacles[], skills[].
```

### Deteksi entri lemah (rule-based dulu, AI opsional)
- Deskripsi kurang dari 25 karakter
- Tidak ada kata kerja aktivitas
- Deskripsi sama dengan entri kemarin
→ tampilkan saran, AI dipanggil hanya jika peserta menekan tombol.

## Alur Voice-to-Log

```
[Frontend] MediaRecorder (maks 90 detik, format webm/mp4)
   → POST /ai/transcribe (multipart, file kecil)
[Backend] validasi mime + ukuran (maks 2 MB), cek kuota
   → GeminiService::transcribe()
   → hapus file audio sementara
   → (opsional) polish() dalam satu permintaan
   → kembalikan { transcript, polished }
[Frontend] tampilkan hasil → peserta edit → simpan
```

Optimasi: gabungkan transkripsi + perapian dalam **satu prompt** ke Gemini agar hemat panggilan dan latensi.

## Kuota dan Penghematan Biaya

| Teknik | Detail |
|--------|--------|
| Model ringan | Pakai varian Flash/Flash-Lite untuk polish, transkrip, foto |
| Kuota harian | Default 30 panggilan per user (`AI_DAILY_LIMIT_PER_USER`) |
| Throttle | `throttle:10,1` pada route AI |
| Cache | Ringkasan periode lampau disimpan di `reports.ai_summary`, tidak dipanggil ulang |
| Batasi input | Potong teks > 2.000 karakter, foto di-resize 1024px sebelum kirim |
| Batasi output | `maxOutputTokens` sesuai fitur |
| Ringkasan bertahap | Bulanan = ringkas dari ringkasan mingguan, bukan dari semua entri mentah |
| Pantau | Dashboard admin dari tabel `ai_usages` |

## Penanganan Error

| Kondisi | Respons aplikasi |
|---------|------------------|
| Timeout / 5xx | Retry 2x, lalu tampilkan "AI sedang sibuk, simpan manual dulu" |
| 429 (rate limit) | Tampilkan pesan kuota, sarankan coba lagi nanti |
| Safety block / output kosong | Fallback ke teks asli |
| API key salah | Log error, notifikasi admin |
| JSON rusak | Parse ulang, jika gagal pakai teks mentah |

## Privasi dan Etika
- Beri tahu pengguna bahwa teks/audio/foto diproses layanan Google.
- Jangan kirim data sensitif yang tidak perlu (hanya isi kegiatan).
- Audio dihapus segera setelah transkripsi.
- Sediakan opsi admin untuk **menonaktifkan** fitur foto/suara per instansi.
- Cek kebijakan data pada tier Gemini yang dipakai (free tier dan berbayar berbeda soal penggunaan data).
