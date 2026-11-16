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
            self::Tugas => 'Tugas & Proyek',
            self::Meeting => 'Meeting & Diskusi',
            self::Belajar => 'Riset & Belajar',
            self::Pelatihan => 'Pelatihan / Workshop',
            self::Lapangan => 'Kunjungan Lapangan',
            self::Administrasi => 'Administrasi',
            self::Lainnya => 'Lainnya',
        };
    }
}
