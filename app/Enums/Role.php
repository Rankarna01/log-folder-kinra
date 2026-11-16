<?php

namespace App\Enums;

enum Role: string
{
    case Participant = 'participant';
    case Mentor = 'mentor';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Participant => 'Peserta Magang',
            self::Mentor => 'Mentor Pembimbing',
            self::Admin => 'Administrator',
        };
    }
}
