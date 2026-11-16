<?php

namespace Database\Seeders;

use App\Enums\LogCategory;
use App\Enums\LogStatus;
use App\Enums\Role;
use App\Models\ActivityTemplate;
use App\Models\AiUsage;
use App\Models\Comment;
use App\Models\Holiday;
use App\Models\Institution;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $participant = User::create([
            'name' => 'Esther Howard',
            'email' => 'peserta@maganghub.id',
            'password' => Hash::make('password'),
            'role' => Role::Participant,
            'phone' => '0812-3456-7890',
            'nim_nik' => '2201020048',
            'campus' => 'Universitas Indonesia',
            'major' => 'Sistem Informasi & Desain Produk',
            'preferences' => [
                'ai_style' => 'formal',
                'language' => 'id',
                'reminder_time' => '16:00',
            ],
            'is_active' => true,
        ]);

        $mentor = User::create([
            'name' => 'Agnes Nielsen',
            'email' => 'mentor@maganghub.id',
            'password' => Hash::make('password'),
            'role' => Role::Mentor,
            'phone' => '0811-9988-7766',
            'nim_nik' => 'NIP-19890412',
            'campus' => 'Lead Product & Engineering Mentor',
            'major' => 'Digital Transformation Division',
            'preferences' => [
                'ai_style' => 'formal',
                'language' => 'id',
            ],
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Muhammad Randy Karna',
            'email' => 'admin@maganghub.id',
            'password' => Hash::make('password'),
            'role' => Role::Admin,
            'phone' => '0813-0000-1111',
            'nim_nik' => 'ADM-001',
            'campus' => 'Koordinator MagangHub Nasional',
            'major' => 'Manajemen Talenta Digital',
            'is_active' => true,
        ]);

        $institution = Institution::create([
            'name' => 'PT Kinra Digital Nusantara (MagangHub)',
            'address' => 'Jl. Jenderal Sudirman Kav. 52-53, SCBD, Jakarta Selatan',
            'letterhead' => [
                'subtitle' => 'Program Magang Terpadu & Logbook Digital Berbasis AI',
                'phone' => '(021) 5099-8877',
                'website' => 'https://maganghub.kinra.id',
            ],
        ]);

        $internship = Internship::create([
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->id,
            'institution_id' => $institution->id,
            'division' => 'Product Design & Frontend Engineering',
            'position' => 'UI/UX & Web Developer Intern',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-31',
            'work_days' => [1, 2, 3, 4, 5],
            'status' => 'active',
        ]);

        $entries = [
            [
                'date' => '2026-11-10',
                'start_time' => '08:30',
                'end_time' => '12:30',
                'duration_minutes' => 240,
                'title' => 'Website Design & Design System',
                'category' => LogCategory::Tugas,
                'raw_description' => 'bikin design system untuk charty app, landing page, pricing page, dan copywriting bersama tim',
                'ai_description' => 'Menyusun arsitektur Design System untuk proyek Charty App yang mencakup komponen Landing Page, Pricing Page, serta penyelarasan tata bahasa antarmuka (UX Copywriting) sesuai panduan identitas visual perusahaan.',
                'final_description' => 'Menyusun arsitektur Design System untuk proyek Charty App yang mencakup komponen Landing Page, Pricing Page, serta penyelarasan tata bahasa antarmuka (UX Copywriting) sesuai panduan identitas visual perusahaan.',
                'output' => 'Design system • Landing Page • Pricing Page • Copywriting (Charty App)',
                'obstacles' => 'Penyesuaian kontras warna pastel pada layar dengan kecerahan rendah.',
                'work_location' => 'wfo',
                'status' => LogStatus::Approved,
                'submitted_at' => now()->subDays(5),
                'reviewed_at' => now()->subDays(4),
                'reviewed_by' => $mentor->id,
            ],
            [
                'date' => '2026-11-10',
                'start_time' => '13:30',
                'end_time' => '16:40',
                'duration_minutes' => 190,
                'title' => 'Delivery App UI Kit (Foodnow)',
                'category' => LogCategory::Meeting,
                'raw_description' => 'meeting review delivery app ui kit foodnow sama mentor agnes dan rapihin komponen',
                'ai_description' => 'Mengikuti rapat koordinasi dan evaluasi progres pembuatan Delivery App UI Kit (Foodnow) bersama pembimbing lapangan, serta menyempurnakan komponen kartu pesanan dan navigasi mobile.',
                'final_description' => 'Mengikuti rapat koordinasi dan evaluasi progres pembuatan Delivery App UI Kit (Foodnow) bersama pembimbing lapangan, serta menyempurnakan komponen kartu pesanan dan navigasi mobile.',
                'output' => 'Modul UI Kit Foodnow (65% selesai) & Catatan Evaluasi Mentor',
                'obstacles' => 'Belum ada kendala signifikan.',
                'work_location' => 'wfo',
                'status' => LogStatus::Submitted,
                'submitted_at' => now()->subDays(2),
            ],
            [
                'date' => '2026-11-12',
                'start_time' => '09:00',
                'end_time' => '13:00',
                'duration_minutes' => 240,
                'title' => 'Integrasi PWA Offline & IndexedDB',
                'category' => LogCategory::Tugas,
                'raw_description' => 'implementasi service worker dan penyimpanan lokal dexie indexeddb buat logbook saat sinyal lemah',
                'ai_description' => 'Mengembangkan fitur Progressive Web App (PWA) menggunakan Service Worker dan IndexedDB (Dexie.js) agar peserta tetap dapat menyimpan draf logbook secara offline dan melakukan sinkronisasi otomatis.',
                'final_description' => 'Mengembangkan fitur Progressive Web App (PWA) menggunakan Service Worker dan IndexedDB (Dexie.js) agar peserta tetap dapat menyimpan draf logbook secara offline dan melakukan sinkronisasi otomatis.',
                'output' => 'Modul Offline Sync & Service Worker v1',
                'obstacles' => 'Perlu pengujian tambahan untuk Safari iOS.',
                'work_location' => 'wfh',
                'status' => LogStatus::Revision,
                'submitted_at' => now()->subDay(),
                'reviewed_at' => now()->subHours(6),
                'reviewed_by' => $mentor->id,
            ],
            [
                'date' => '2026-11-14',
                'start_time' => '08:30',
                'end_time' => '12:00',
                'duration_minutes' => 210,
                'title' => 'Optimasi Prompt AI Gemini Flash',
                'category' => LogCategory::Belajar,
                'raw_description' => 'eksperimen prompt gemini flash untuk rapikan teks logbook 3 gaya formal singkat detail',
                'ai_description' => 'Melakukan pengujian dan kalibrasi sistem instruksi (System Prompt) pada Google Gemini Flash API untuk menghasilkan 3 variasi gaya penulisan logbook (Formal, Singkat, dan Detail) tanpa mengubah fakta lapangan.',
                'final_description' => 'Melakukan pengujian dan kalibrasi sistem instruksi (System Prompt) pada Google Gemini Flash API untuk menghasilkan 3 variasi gaya penulisan logbook (Formal, Singkat, dan Detail) tanpa mengubah fakta lapangan.',
                'output' => 'Dokumen Standar PromptBuilder & Kuota Harian',
                'work_location' => 'wfo',
                'status' => LogStatus::Approved,
                'submitted_at' => now()->subDays(2),
                'reviewed_at' => now()->subDay(),
                'reviewed_by' => $mentor->id,
            ],
            [
                'date' => '2026-11-18',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'duration_minutes' => 180,
                'title' => 'Penyusunan Template Ekspor PDF Bulanan',
                'category' => LogCategory::Administrasi,
                'raw_description' => 'buat layout laporan pdf a4 lengkap dengan kop instansi qr verifikasi dan tanda tangan',
                'ai_description' => 'Merancang tata letak cetak laporan PDF ukuran A4 yang dilengkapi kop resmi instansi, tabel rekapitulasi jam kerja, kolom tanda tangan pembimbing, serta kode QR verifikasi keaslian dokumen.',
                'final_description' => 'Merancang tata letak cetak laporan PDF ukuran A4 yang dilengkapi kop resmi instansi, tabel rekapitulasi jam kerja, kolom tanda tangan pembimbing, serta kode QR verifikasi keaslian dokumen.',
                'output' => 'Template Blade PDF Harian, Mingguan, & Bulanan',
                'work_location' => 'wfo',
                'status' => LogStatus::Approved,
                'submitted_at' => now()->subDay(),
                'reviewed_at' => now(),
                'reviewed_by' => $mentor->id,
            ],
            [
                'date' => '2026-11-20',
                'start_time' => '13:00',
                'end_time' => '16:00',
                'duration_minutes' => 180,
                'title' => 'Workshop Aksesibilitas & Audit Lighthouse',
                'category' => LogCategory::Pelatihan,
                'raw_description' => 'ikut workshop internal performa web dan audit pwa lighthouse skor 98',
                'ai_description' => 'Menghadiri pelatihan internal terkait standar aksesibilitas antarmuka (WCAG) serta melaksanakan audit performa PWA menggunakan Google Lighthouse dengan capaian skor 98/100.',
                'final_description' => 'Menghadiri pelatihan internal terkait standar aksesibilitas antarmuka (WCAG) serta melaksanakan audit performa PWA menggunakan Google Lighthouse dengan capaian skor 98/100.',
                'output' => 'Laporan Hasil Audit Performa Mobile PWA',
                'work_location' => 'wfo',
                'status' => LogStatus::Approved,
                'submitted_at' => now(),
                'reviewed_at' => now(),
                'reviewed_by' => $mentor->id,
            ],
            [
                'date' => '2026-11-25',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'duration_minutes' => 180,
                'title' => 'Observasi Penggunaan Logbook oleh Peserta Pilot',
                'category' => LogCategory::Lapangan,
                'raw_description' => 'uji coba aplikasi langsung ke 15 peserta magang batch november',
                'ai_description' => 'Melaksanakan sesi uji coba lapangan (Usability Testing) bersama 15 peserta magang untuk mengukur rata-rata waktu pengisian logbook harian (tercapai < 48 detik per entri).',
                'final_description' => 'Melaksanakan sesi uji coba lapangan (Usability Testing) bersama 15 peserta magang untuk mengukur rata-rata waktu pengisian logbook harian (tercapai < 48 detik per entri).',
                'output' => 'Rekap Umpan Balik Pengguna & Metrik Kecepatan Input',
                'work_location' => 'field',
                'status' => LogStatus::Draft,
            ],
            [
                'date' => '2026-11-28',
                'start_time' => '10:00',
                'end_time' => '15:00',
                'duration_minutes' => 300,
                'title' => 'Rekapitulasi Laporan Akhir Bulan November',
                'category' => LogCategory::Tugas,
                'raw_description' => 'menyusun ringkasan capaian bulan november dan ekstraksi kompetensi',
                'ai_description' => 'Menyusun laporan rekapitulasi kegiatan bulanan periode November 2026 beserta pemetaan kompetensi teknis yang telah diperoleh selama masa penempatan.',
                'final_description' => 'Menyusun laporan rekapitulasi kegiatan bulanan periode November 2026 beserta pemetaan kompetensi teknis yang telah diperoleh selama masa penempatan.',
                'output' => 'Draf Laporan Bulanan Terverifikasi QR',
                'work_location' => 'wfo',
                'status' => LogStatus::Approved,
                'submitted_at' => now(),
                'reviewed_at' => now(),
                'reviewed_by' => $mentor->id,
            ],
        ];

        foreach ($entries as $idx => $item) {
            $logbook = Logbook::create(array_merge($item, [
                'internship_id' => $internship->id,
                'source' => $idx === 1 ? 'voice' : 'manual',
                'client_uuid' => (string) Str::uuid(),
            ]));

            if ($idx === 0) {
                Comment::create([
                    'logbook_id' => $logbook->id,
                    'user_id' => $mentor->id,
                    'message' => 'Struktur komponen Design System sudah sangat rapi dan sesuai standar tim produk. Disetujui!',
                    'type' => 'approval',
                ]);
                Comment::create([
                    'logbook_id' => $logbook->id,
                    'user_id' => $participant->id,
                    'message' => 'Terima kasih Kak Agnes! Dokumentasi token warna Pastel Bento juga sudah saya lampirkan.',
                    'type' => 'comment',
                ]);
            }

            if ($idx === 2) {
                Comment::create([
                    'logbook_id' => $logbook->id,
                    'user_id' => $mentor->id,
                    'message' => 'Tolong tambahkan tangkapan layar pengujian mode offline saat koneksi diputus ya.',
                    'type' => 'revision_request',
                ]);
            }
        }

        ActivityTemplate::create([
            'user_id' => $participant->id,
            'title' => 'Daily Standup & Sinkronisasi Tim Produk',
            'category' => 'meeting',
            'description' => 'Mengikuti rapat koordinasi harian (Daily Standup) untuk melaporkan progres tugas kemarin, rencana hari ini, serta kendala teknis.',
        ]);

        ActivityTemplate::create([
            'user_id' => $participant->id,
            'title' => 'Pengembangan & Pengujian Komponen UI/UX',
            'category' => 'tugas',
            'description' => 'Melaksanakan implementasi komponen antarmuka pengguna sesuai spesifikasi desain serta melakukan pengujian responsivitas pada perangkat mobile.',
        ]);

        Report::create([
            'internship_id' => $internship->id,
            'type' => 'monthly',
            'period_start' => '2026-11-01',
            'period_end' => '2026-11-30',
            'options' => [
                'include_photos' => true,
                'include_ai_summary' => true,
                'only_approved' => false,
            ],
            'ai_summary' => json_encode([
                'overview' => 'Selama bulan November 2026, Esther Howard telah menyelesaikan rangkaian pengembangan Design System, UI Kit Foodnow, integrasi PWA Offline, serta kalibrasi asisten AI Gemini dengan tingkat kepatuhan pengisian logbook yang sangat tinggi.',
                'main_activities' => [
                    'Penyusunan Design System & Komponen UI Charty App',
                    'Evaluasi Delivery App UI Kit (Foodnow) bersama Mentor',
                    'Implementasi PWA Offline Sync berbasis IndexedDB',
                    'Kalibrasi Prompt AI Gemini Flash (Formal, Singkat, Detail)',
                ],
                'outputs' => [
                    'Design System & UI Kit Terdokumentasi',
                    'Modul Offline Sync Service Worker',
                    'Template Laporan PDF Berverifikasi QR',
                ],
                'obstacles' => [
                    'Penyesuaian kontras warna pastel pada mode hemat daya',
                    'Penanganan cache Safari iOS pada mode PWA',
                ],
                'skills' => [
                    'UI/UX Design System',
                    'Laravel & Tailwind CSS',
                    'Progressive Web App (PWA)',
                    'Prompt Engineering (Google Gemini)',
                ],
            ], JSON_UNESCAPED_UNICODE),
            'status' => 'ready',
            'verify_token' => 'MAGANGHUB-2026-NOV-VERIFIED',
            'generated_at' => now(),
        ]);

        AiUsage::create([
            'user_id' => $participant->id,
            'feature' => 'polish',
            'model' => 'gemini-flash-latest',
            'input_tokens' => 84,
            'output_tokens' => 142,
            'status' => 'success',
            'created_at' => now()->subHours(3),
        ]);

        AiUsage::create([
            'user_id' => $participant->id,
            'feature' => 'transcribe',
            'model' => 'gemini-flash-latest',
            'input_tokens' => 210,
            'output_tokens' => 165,
            'status' => 'success',
            'created_at' => now()->subHours(1),
        ]);

        Setting::create([
            'key' => 'system_config',
            'value' => [
                'max_backdate_days' => 7,
                'ai_daily_limit' => 30,
                'institution_name' => 'PT Kinra Digital Nusantara (MagangHub)',
            ],
        ]);

        Holiday::create([
            'institution_id' => $institution->id,
            'date' => '2026-11-10',
            'name' => 'Hari Pahlawan Nasional (Kegiatan Khusus)',
        ]);
    }
}
