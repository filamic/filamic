<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StudentStatusEnum: int implements HasLabel, HasIcon
{
    case ACTIVE = 1;
    case PROSPECTIVE = 2;
    case GRADUATED = 3;
    case MOVED = 4;
    case DROPPED_OUT = 5;
    case NON_ACTIVE = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::GRADUATED => 'Lulus',
            self::MOVED => 'Pindah',
            self::DROPPED_OUT => 'Putus Sekolah',
            self::NON_ACTIVE => 'Tidak Aktif',
            self::PROSPECTIVE => 'Calon Siswa',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ACTIVE => 'tabler-rosette-discount-check',
            self::GRADUATED => 'tabler-briefcase-2',
            self::MOVED => 'tabler-outbound',
            self::DROPPED_OUT => 'tabler-arrow-bear-right',
            self::NON_ACTIVE => 'tabler-rosette-discount-check-off',
            self::PROSPECTIVE => 'tabler-star',
        };
    }
}
