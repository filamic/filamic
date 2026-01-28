<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReligionEnum: int implements HasLabel
{
    case ISLAM = 1;
    case PROTESTANT = 2;
    case CATHOLIC = 3;
    case HINDU = 4;
    case BUDDHA = 5;
    case KHONGHUCU = 6;
    case OTHERS = 7;

    public function getLabel(): string
    {
        return match ($this) {
            self::ISLAM => 'Islam',
            self::PROTESTANT => 'Protestan',
            self::CATHOLIC => 'Katolik',
            self::HINDU => 'Hindu',
            self::BUDDHA => 'Buddha',
            self::KHONGHUCU => 'Konghucu',
            self::OTHERS => 'Lainnya',
        };
    }
}
