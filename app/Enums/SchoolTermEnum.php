<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SchoolTermEnum: int implements HasLabel
{
    case ODD = 1;
    case EVEN = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::ODD => 'Ganjil (1)',
            self::EVEN => 'Genap (2)',
        };
    }

    public function getAllowedMonths(): array
    {
        // why odd show 12 month, cuz some parent wanna create and pay invoice for a year, if its in the first semester, but if its in the second semester, it make sense if only from 7-12.
        return match ($this) {
            self::ODD => [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6],
            self::EVEN => [1, 2, 3, 4, 5, 6],
        };
    }
}
