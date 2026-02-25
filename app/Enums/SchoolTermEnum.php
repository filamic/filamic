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
        // ODD term allows all 12 months to support parents creating full-year invoices.
        // EVEN term only allows months 1-6 since the academic year ends in this semester.
        return match ($this) {
            self::ODD => [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6],
            self::EVEN => [1, 2, 3, 4, 5, 6],
        };
    }
}
