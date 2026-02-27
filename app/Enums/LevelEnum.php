<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LevelEnum: int implements HasLabel
{
    case KINDERGARTEN = 1;
    case ELEMENTARY = 2;
    case JUNIOR_HIGH = 3;
    case SENIOR_HIGH = 4;
    case ALL = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::KINDERGARTEN => 'TK',
            self::ELEMENTARY => 'SD',
            self::JUNIOR_HIGH => 'SMP',
            self::SENIOR_HIGH => 'SMA',
            self::ALL => 'SEMUA JENJANG',
        };
    }
}
