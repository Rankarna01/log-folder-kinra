<?php

namespace App\Services\Ai;

class PromptBuilder
{
    public static function polishSystem(string $style = 'formal'): string
    {
        $lengthGuide = match ($style) {
            'singkat' => '1 kalimat padat dan langsung pada inti kegiatan',
            'detail' => '4-6 kalimat terstruktur yang menjelaskan proses, metode, dan hasil kegiatan',
            default => '2-3 kalimat formal standar laporan magang',
        };

        return <<<PROMPT
Kamu asisten penulisan logbook magang berbahasa Indonesia.
Tugas: ubah catatan kasar menjadi deskripsi kegiatan yang rapi dan jelas.

Aturan:
- Gunakan bahasa Indonesia baku sesuai gaya: {$style}.
- JANGAN menambah kegiatan, angka, nama, atau hasil yang tidak ada di catatan.
- Perbaiki typo, singkatan, dan istilah teknis.
- Gunakan kalimat aktif, diawali kata kerja (Menyusun, Menganalisis, Menghadiri, Mengembangkan, Melakukan, dst.).
- Panjang: {$lengthGuide}.
- Keluarkan hanya teksnya, tanpa pembuka atau penutup.
PROMPT;
    }

    public static function transcribeInstruction(): string
    {
        return <<<PROMPT
Transkripsikan audio ini ke teks bahasa Indonesia apa adanya.
Pertahankan istilah teknis berbahasa Inggris.
Hapus kata pengisi (eh, emm, anu). Jangan menambah isi.
Keluarkan hanya transkrip.
PROMPT;
    }

    public static function photoInstruction(): string
    {
        return <<<PROMPT
Lihat foto kegiatan magang ini. Buat 1 kalimat caption netral dan
1 saran deskripsi kegiatan. Jangan menebak identitas orang.
Jika foto tidak jelas, katakan "Tidak cukup informasi".
Keluarkan JSON: {"caption": "...", "suggestion": "..."}
PROMPT;
    }

    public static function summaryInstruction(string $periodLabel): string
    {
        return <<<PROMPT
Berikut entri logbook magang periode {$periodLabel}.
Buat ringkasan dalam bahasa Indonesia formal:
1. Ringkasan umum (1 paragraf)
2. Kegiatan utama (poin-poin, dikelompokkan per kategori)
3. Capaian/output
4. Kendala dan solusi (jika ada)
5. Skill yang dipelajari
Hanya gunakan informasi dari entri. Jangan mengarang.
Keluarkan JSON dengan kunci: overview, main_activities, outputs, obstacles, skills.
PROMPT;
    }
}
