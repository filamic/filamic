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

    public function getLabel(): string
    {
        return match ($this) {
            self::KINDERGARTEN => 'Preschool / TK',
            self::ELEMENTARY => 'Elementary School / SD',
            self::JUNIOR_HIGH => 'Junior High School / SMP',
            self::SENIOR_HIGH => 'Senior High School / SMA',
        };
    }
}
