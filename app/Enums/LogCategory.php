<?php

namespace App\Enums;

enum LogCategory: string
{
    case Tugas = 'tugas';
    case Meeting = 'meeting';
    case Belajar = 'belajar';
    case Pelatihan = 'pelatihan';
    case Lapangan = 'lapangan';
    case Administrasi = 'administrasi';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Tugas => 'Tugas Harian',
            self::Meeting => 'Operasional Bagian',
            self::Belajar => 'Riset & Analisis',
            self::Pelatihan => 'Pelatihan Teknis',
            self::Lapangan => 'Kegiatan Lapangan',
            self::Administrasi => 'Administrasi & Dokumen',
            self::Lainnya => 'Kegiatan Lainnya',
        };
    }
}
