<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum GenderEnum: int implements HasIcon, HasLabel
{
    case MALE = 1;
    case FEMALE = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female'
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::MALE => 'tabler-gender-male',
            self::FEMALE => 'tabler-gender-female'
        };
    }
}
