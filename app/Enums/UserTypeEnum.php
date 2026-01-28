<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserTypeEnum: int implements HasColor, HasLabel
{
    case NOTYPE = 0;
    case EMPLOYEE = 1;
    case STUDENT = 2;
    case PARENT = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::EMPLOYEE => 'Employee',
            self::STUDENT => 'Student',
            self::PARENT => 'Parent',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::EMPLOYEE => 'success',
            self::STUDENT => 'info',
            self::PARENT => 'warning',
        };
    }
}
