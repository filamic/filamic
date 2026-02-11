<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StudentEnrollmentStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case ENROLLED = 1;
    case PROMOTED = 2;
    case STAYED = 3;
    case GRADUATED = 4;
    case MOVED_INTERNAL = 5;
    case MOVED_EXTERNAL = 6;
    case DROPPED_OUT = 7;

    public function getLabel(): string
    {
        return match ($this) {
            self::ENROLLED => 'Terdaftar',
            self::PROMOTED => 'Naik Kelas',
            self::STAYED => 'Tinggal Kelas',
            self::GRADUATED => 'Lulus',
            self::MOVED_INTERNAL => 'Mutasi Internal',
            self::MOVED_EXTERNAL => 'Mutasi Keluar',
            self::DROPPED_OUT => 'Putus Sekolah',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ENROLLED => 'info',
            self::PROMOTED, self::GRADUATED => 'success',
            self::STAYED, self::DROPPED_OUT => 'danger',
            self::MOVED_INTERNAL, self::MOVED_EXTERNAL => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ENROLLED => 'heroicon-m-user-group',
            self::PROMOTED => 'heroicon-m-arrow-trending-up',
            self::STAYED => 'heroicon-m-arrow-path',
            self::GRADUATED => 'heroicon-m-academic-cap',
            self::MOVED_INTERNAL => 'heroicon-m-arrows-right-left',
            self::MOVED_EXTERNAL => 'heroicon-m-arrow-right-on-rectangle',
            self::DROPPED_OUT => 'heroicon-m-x-circle',
        };
    }

    public static function getActiveStatuses(): array
    {
        return [
            self::ENROLLED,
        ];
    }

    public static function getInactiveStatuses(): array
    {
        return [
            self::PROMOTED,
            self::STAYED,
            self::GRADUATED,
            self::MOVED_INTERNAL,
            self::MOVED_EXTERNAL,
            self::DROPPED_OUT,
        ];
    }
}
