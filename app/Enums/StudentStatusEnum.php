<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StudentStatusEnum: int implements HasLabel
{
    case ACTIVE = 1;
    case GRADUATED = 2;
    case MOVED = 3;
    case DROPPED_OUT = 4;
    case NON_ACTIVE = 5;
    case PROSPECTIVE = 6;

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
}
