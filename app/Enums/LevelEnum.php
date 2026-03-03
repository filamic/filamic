<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LevelEnum: int implements HasColor, HasIcon, HasLabel
{
    case KINDERGARTEN = 1;
    case ELEMENTARY = 2;
    case JUNIOR_HIGH = 3;
    case SENIOR_HIGH = 4;
    // case ALL = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::KINDERGARTEN => 'TK',
            self::ELEMENTARY => 'SD',
            self::JUNIOR_HIGH => 'SMP',
            self::SENIOR_HIGH => 'SMA',
            // self::ALL => 'SEMUA JENJANG',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::KINDERGARTEN => 'tabler-horse-toy',
            self::ELEMENTARY => 'tabler-school',
            self::JUNIOR_HIGH => 'tabler-building-community',
            self::SENIOR_HIGH => 'tabler-building-skyscraper',
            // self::ALL => 'tabler-building',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::KINDERGARTEN => 'info',
            self::ELEMENTARY => 'success',
            self::JUNIOR_HIGH => 'warning',
            self::SENIOR_HIGH => 'danger',
            // self::ALL => 'primary',
        };
    }
}
