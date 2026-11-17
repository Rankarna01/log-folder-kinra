<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Institution::firstOrCreate(
            ['name' => 'PT Kawasan Industri Nusantara (KINRA Sei Mangkei)'],
            [
                'address' => 'KEK Sei Mangkei, Kec. Bosar Maligas, Kab. Simalungun, Sumatera Utara',
                'letterhead' => [
                    'subtitle' => 'Rekapitulasi Logbook Kegiatan Harian Peserta Magang',
                    'phone' => '(0622) 7430-111',
                    'website' => 'https://kinra.co.id',
                ],
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'system_config'],
            [
                'value' => [
                    'max_backdate_days' => 7,
                    'ai_daily_limit' => 30,
                    'institution_name' => 'PT Kawasan Industri Nusantara (KINRA Sei Mangkei)',
                ],
            ]
        );
    }
}
