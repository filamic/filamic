<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MonthEnum: int implements HasLabel
{
    case January = 1;
    case February = 2;
    case March = 3;
    case April = 4;
    case May = 5;
    case June = 6;
    case July = 7;
    case August = 8;
    case September = 9;
    case October = 10;
    case November = 11;
    case December = 12;

    public function getLabel(): string
    {
        return match ($this) {
            self::January => 'Januari',
            self::February => 'Februari',
            self::March => 'Maret',
            self::April => 'April',
            self::May => 'Mei',
            self::June => 'Juni',
            self::July => 'Juli',
            self::August => 'Agustus',
            self::September => 'September',
            self::October => 'Oktober',
            self::November => 'November',
            self::December => 'Desember',
        };
    }

    public static function filterBySemester(array $allowedMonths): array
    {
        return collect(self::cases())
            ->filter(fn ($month) => in_array($month->value, $allowedMonths))
            ->sortBy(function ($month) use ($allowedMonths) {
                return array_search($month->value, $allowedMonths);
            })
            ->values()
            ->all();
    }
}
