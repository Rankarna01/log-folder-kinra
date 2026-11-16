<?php

namespace App\Enums;

enum LogStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Revision = 'revision';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::Approved => 'Approved',
            self::Revision => 'Revisi',
        };
    }

    /**
     * Maps logbook status to one of the 4 Pastel Bento card styles from style-design.png.
     */
    public function cardStyle(): array
    {
        return match ($this) {
            self::Approved => [
                'bg' => 'bg-pastel-mint',
                'bar' => 'bg-pastel-mint-deep',
                'badge_bg' => 'bg-white',
                'badge_text' => 'text-emerald-800',
                'progress' => 100,
            ],
            self::Submitted => [
                'bg' => 'bg-pastel-periwinkle',
                'bar' => 'bg-pastel-periwinkle-deep',
                'badge_bg' => 'bg-white',
                'badge_text' => 'text-indigo-900',
                'progress' => 75,
            ],
            self::Revision => [
                'bg' => 'bg-pastel-butter',
                'bar' => 'bg-pastel-butter-deep',
                'badge_bg' => 'bg-white',
                'badge_text' => 'text-amber-900',
                'progress' => 45,
            ],
            self::Draft => [
                'bg' => 'bg-pastel-lavender',
                'bar' => 'bg-pastel-lavender-deep',
                'badge_bg' => 'bg-white',
                'badge_text' => 'text-purple-900',
                'progress' => 35,
            ],
        };
    }
}
