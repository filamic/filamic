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
            self::ODD => 'Odd',
            self::EVEN => 'Even',
        };
    }
}
