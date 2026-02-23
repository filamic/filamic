<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReligionEnum: int implements HasLabel
{
    case CHRISTIAN = 1;
    case CATHOLIC = 2;
    case HINDU = 3;
    case BUDDHA = 4;
    case KHONGHUCU = 5;
    case ISLAM = 6;
    case OTHERS = 7;

    public function getLabel(): string
    {
        return match ($this) {
            self::CHRISTIAN => 'Kristen',
            self::CATHOLIC => 'Katolik',
            self::HINDU => 'Hindu',
            self::BUDDHA => 'Buddha',
            self::KHONGHUCU => 'Khonghucu',
            self::ISLAM => 'Islam',
            self::OTHERS => 'Lainnya',
        };
    }
}
