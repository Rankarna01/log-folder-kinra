<?php

namespace Database\Seeders;

use App\Enums\LogCategory;
use App\Enums\LogStatus;
use App\Enums\Role;
use App\Models\ActivityTemplate;
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
            'name' => 'Randy Karna',
            'email' => 'peserta@kinra.id',
            'password' => Hash::make('password'),
            'role' => Role::Participant,
            'phone' => '0812-3456-7890',
            'nim_nik' => '2201020048',
            'campus' => 'IT & Sistem Informasi',
            'major' => 'Full Stack Developer',
            'preferences' => [
                'ai_style' => 'formal',
                'language' => 'id',
                'mentor_name' => 'Rizky Pratama',
                'division' => 'IT & Sistem Informasi',
                'position' => 'Full Stack Developer',
            ],
            'is_active' => true,
        ]);

        $mentor = User::create([
            'name' => 'Rizky Pratama',
            'email' => 'mentor@kinra.id',
            'password' => Hash::make('password'),
            'role' => Role::Mentor,
            'phone' => '0811-9988-7766',
            'nim_nik' => 'NIP-19890412',
            'campus' => 'IT & Sistem Informasi',
            'major' => 'Supervisor Sistem Informasi',
            'is_active' => true,
        ]);

        $institution = Institution::create([
            'name' => 'PT Kawasan Industri Nusantara (KINRA Sei Mangkei)',
            'address' => 'KEK Sei Mangkei, Kec. Bosar Maligas, Kab. Simalungun, Sumatera Utara',
            'letterhead' => [
                'subtitle' => 'Rekapitulasi Logbook Kegiatan Harian Peserta Magang',
                'phone' => '(0622) 7430-111',
                'website' => 'https://kinra.co.id',
            ],
        ]);

        $internship = Internship::create([
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->id,
            'institution_id' => $institution->id,
            'division' => 'IT & Sistem Informasi',
            'position' => 'Full Stack Developer',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-31',
            'work_days' => [1, 2, 3, 4, 5],
            'status' => 'active',
        ]);

        $entries = [
            [
                'date' => '2026-11-10',
                'start_time' => '08:00',
                'end_time' => '12:00',
                'duration_minutes' => 240,
                'title' => 'Pencatatan & Rekapitulasi Data Operasional Harian',
                'category' => LogCategory::Tugas,
                'raw_description' => 'mencatat data operasional harian bagian it dan menyusun rekapitulasi log sistem internal kinra sei mangkei',
                'ai_description' => 'Melaksanakan pencatatan data operasional harian pada bagian IT serta menyusun dokumen rekapitulasi pemeliharaan sistem internal di PT Kawasan Industri Nusantara (KINRA Sei Mangkei).',
                'final_description' => 'Melaksanakan pencatatan data operasional harian pada bagian IT serta menyusun dokumen rekapitulasi pemeliharaan sistem internal di PT Kawasan Industri Nusantara (KINRA Sei Mangkei).',
                'output' => 'Rekap Data Operasional Harian • Dokumentasi Sistem • Arsip Bagian IT',
                'obstacles' => 'Tidak ada kendala signifikan.',
                'work_location' => 'wfo',
                'status' => LogStatus::Approved,
            ],
            [
                'date' => '2026-11-12',
                'start_time' => '08:30',
                'end_time' => '12:30',
                'duration_minutes' => 240,
                'title' => 'Pengembangan Modul Pencatatan Logbook Harian',
                'category' => LogCategory::Tugas,
                'raw_description' => 'membuat halaman form input logbook harian dan kalender riwayat kegiatan peserta magang',
                'ai_description' => 'Mengembangkan antarmuka formulir pencatatan logbook kegiatan harian peserta magang beserta fitur kalender riwayat pengisian bulanan.',
                'final_description' => 'Mengembangkan antarmuka formulir pencatatan logbook kegiatan harian peserta magang beserta fitur kalender riwayat pengisian bulanan.',
                'output' => 'Modul Form Logbook Harian • Kalender Riwayat Kegiatan',
                'obstacles' => 'Penyesuaian tampilan tabel cetak pada ukuran kertas A4.',
                'work_location' => 'wfo',
                'status' => LogStatus::Approved,
            ],
            [
                'date' => '2026-11-14',
                'start_time' => '13:00',
                'end_time' => '16:30',
                'duration_minutes' => 210,
                'title' => 'Pengecekan Infrastruktur & Inventaris Perangkat',
                'category' => LogCategory::Lapangan,
                'raw_description' => 'melakukan pengecekan kondisi perangkat jaringan dan pencatatan inventaris kantor sei mangkei',
                'ai_description' => 'Melaksanakan pemeriksaan berkala terhadap perangkat keras dan jaringan kerja di lingkungan kantor operasional KINRA Sei Mangkei serta memperbarui daftar inventaris bagian.',
                'final_description' => 'Melaksanakan pemeriksaan berkala terhadap perangkat keras dan jaringan kerja di lingkungan kantor operasional KINRA Sei Mangkei serta memperbarui daftar inventaris bagian.',
                'output' => 'Checklist Pemeriksaan Perangkat • Data Inventaris Terbaru',
                'work_location' => 'field',
                'status' => LogStatus::Approved,
            ],
            [
                'date' => '2026-11-16',
                'start_time' => '08:30',
                'end_time' => '12:00',
                'duration_minutes' => 210,
                'title' => 'Penyusunan Format Cetak Laporan PDF Bulanan',
                'category' => LogCategory::Administrasi,
                'raw_description' => 'menyusun template cetak laporan pdf bulanan lengkap dengan kop surat kinra sei mangkei',
                'ai_description' => 'Merancang tata letak dokumen cetak laporan logbook bulanan berukuran A4 yang dilengkapi kop resmi PT Kawasan Industri Nusantara (KINRA Sei Mangkei) dan kolom pengesahan.',
                'final_description' => 'Merancang tata letak dokumen cetak laporan logbook bulanan berukuran A4 yang dilengkapi kop resmi PT Kawasan Industri Nusantara (KINRA Sei Mangkei) dan kolom pengesahan.',
                'output' => 'Format Laporan PDF A4 • Kop Resmi KINRA Sei Mangkei',
                'work_location' => 'wfo',
                'status' => LogStatus::Approved,
            ],
        ];

        foreach ($entries as $idx => $item) {
            $logbook = Logbook::create(array_merge($item, [
                'internship_id' => $internship->id,
                'source' => 'manual',
                'client_uuid' => (string) Str::uuid(),
                'submitted_at' => now(),
                'reviewed_at' => now(),
                'reviewed_by' => $mentor->id,
            ]));

            if ($idx === 0) {
                Comment::create([
                    'logbook_id' => $logbook->id,
                    'user_id' => $participant->id,
                    'message' => 'Seluruh lampiran berkas rekapitulasi harian sudah disimpan di folder arsip divisi.',
                    'type' => 'comment',
                ]);
            }
        }

        ActivityTemplate::create([
            'user_id' => $participant->id,
            'title' => 'Pencatatan & Rekapitulasi Data Harian Bagian',
            'category' => 'tugas',
            'description' => 'Melaksanakan pencatatan data kegiatan harian bagian serta menyusun rekapitulasi dokumen kerja sesuai arahan pembimbing lapangan.',
        ]);

        ActivityTemplate::create([
            'user_id' => $participant->id,
            'title' => 'Pemeliharaan Sistem & Dokumentasi Teknis',
            'category' => 'tugas',
            'description' => 'Melakukan pemeriksaan dan pemeliharaan fitur aplikasi internal serta memperbarui dokumentasi teknis pekerjaan harian.',
        ]);

        Report::create([
            'internship_id' => $internship->id,
            'type' => 'monthly',
            'period_start' => '2026-11-01',
            'period_end' => '2026-11-30',
            'options' => [
                'include_photos' => true,
                'include_ai_summary' => true,
            ],
            'ai_summary' => json_encode([
                'overview' => 'Selama periode November 2026, peserta magang telah melaksanakan pencatatan kegiatan harian secara konsisten pada bagian IT & Sistem Informasi di PT Kawasan Industri Nusantara (KINRA Sei Mangkei).',
                'main_activities' => [
                    'Pencatatan & Rekapitulasi Data Operasional Harian',
                    'Pengembangan Modul Pencatatan Logbook Harian',
                    'Pengecekan Infrastruktur & Inventaris Perangkat',
                    'Penyusunan Format Cetak Laporan PDF Bulanan',
                ],
                'outputs' => [
                    'Rekapitulasi Data Harian Terdokumentasi',
                    'Modul Aplikasi Logbook Harian KINRA',
                    'Dokumen Laporan Bulanan PDF A4',
                ],
                'obstacles' => [
                    'Penyesuaian format tabel cetak pada ukuran kertas A4',
                ],
                'skills' => [
                    'Administrasi & Dokumentasi Teknis',
                    'Pengembangan Aplikasi Web (Laravel)',
                    'Manajemen Data Operasional Harian',
                ],
            ], JSON_UNESCAPED_UNICODE),
            'status' => 'ready',
            'verify_token' => 'KINRA-2026-NOV-VERIFIED',
            'generated_at' => now(),
        ]);

        Setting::create([
            'key' => 'system_config',
            'value' => [
                'max_backdate_days' => 7,
                'ai_daily_limit' => 30,
                'institution_name' => 'PT Kawasan Industri Nusantara (KINRA Sei Mangkei)',
            ],
        ]);

        Holiday::create([
            'institution_id' => $institution->id,
            'date' => '2026-11-10',
            'name' => 'Hari Pahlawan Nasional',
        ]);
    }
}
