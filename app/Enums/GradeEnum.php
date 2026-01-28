<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum GradeEnum: int implements HasLabel
{
    // TK
    case PLAYGROUP = 1;
    case KINDERGARTEN_A = 2;
    case KINDERGARTEN_B = 3;

    // SD
    case GRADE_1 = 4;
    case GRADE_2 = 5;
    case GRADE_3 = 6;
    case GRADE_4 = 7;
    case GRADE_5 = 8;
    case GRADE_6 = 9;

    // SMP
    case GRADE_7 = 10;
    case GRADE_8 = 11;
    case GRADE_9 = 12;

    // SMA
    case GRADE_10 = 13;
    case GRADE_11 = 14;
    case GRADE_12 = 15;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PLAYGROUP => 'Playgroup',
            self::KINDERGARTEN_A => 'TK A',
            self::KINDERGARTEN_B => 'TK B',
            self::GRADE_1 => 'Grade 1',
            self::GRADE_2 => 'Grade 2',
            self::GRADE_3 => 'Grade 3',
            self::GRADE_4 => 'Grade 4',
            self::GRADE_5 => 'Grade 5',
            self::GRADE_6 => 'Grade 6',
            self::GRADE_7 => 'Grade 7',
            self::GRADE_8 => 'Grade 8',
            self::GRADE_9 => 'Grade 9',
            self::GRADE_10 => 'Grade 10',
            self::GRADE_11 => 'Grade 11',
            self::GRADE_12 => 'Grade 12',
        };
    }

    public static function forLevel(int $level): array
    {
        $levelValue = $level instanceof LevelEnum ? $level->value : $level;

        return match ($levelValue) {
            1 => [self::PLAYGROUP, self::KINDERGARTEN_A, self::KINDERGARTEN_B],
            2 => [self::GRADE_1, self::GRADE_2, self::GRADE_3, self::GRADE_4, self::GRADE_5, self::GRADE_6],
            3 => [self::GRADE_7, self::GRADE_8, self::GRADE_9],
            4 => [self::GRADE_10, self::GRADE_11, self::GRADE_12],
            default => [],
        };
    }
}
