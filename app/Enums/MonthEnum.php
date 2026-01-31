<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MonthEnum: int implements HasLabel
{
    case JANUARY = 1;
    case FEBRUARY = 2;
    case MARCH = 3;
    case APRIL = 4;
    case MAY = 5;
    case JUNE = 6;
    case JULY = 7;
    case AUGUST = 8;
    case SEPTEMBER = 9;
    case OCTOBER = 10;
    case NOVEMBER = 11;
    case DECEMBER = 12;

    public function getLabel(): string
    {
        return match ($this) {
            self::JANUARY => 'Januari',
            self::FEBRUARY => 'Februari',
            self::MARCH => 'Maret',
            self::APRIL => 'April',
            self::MAY => 'Mei',
            self::JUNE => 'Juni',
            self::JULY => 'Juli',
            self::AUGUST => 'Agustus',
            self::SEPTEMBER => 'September',
            self::OCTOBER => 'Oktober',
            self::NOVEMBER => 'November',
            self::DECEMBER => 'Desember',
        };
    }
}
