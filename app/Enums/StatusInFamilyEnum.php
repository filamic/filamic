<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatusInFamilyEnum: int implements HasLabel
{
    case BIOLOGICAL_CHILD = 1;
    case STEP_CHILD = 2;
    case ADOPTED_CHILD = 3;
    case FOSTER_CHILD = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::BIOLOGICAL_CHILD => 'Anak Kandung',
            self::STEP_CHILD => 'Anak Tiri',
            self::ADOPTED_CHILD => 'Anak Angkat',
            self::FOSTER_CHILD => 'Anak Asuh',
        };
    }
}
