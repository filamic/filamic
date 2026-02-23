<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StudentEnrollmentStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case ENROLLED = 1;
    case GRADUATED = 4;
    case INACTIVE = 3;
    // case PROMOTED = 2;
    // case STAYED = 3;
    // case MOVED_INTERNAL = 5;
    // case MOVED_EXTERNAL = 6;
    // case DROPPED_OUT = 7;

    public function getLabel(): string
    {
        return match ($this) {
            self::ENROLLED => 'Terdaftar',
            self::GRADUATED => 'Lulus',
            self::INACTIVE => 'Tidak Aktif',
            // self::PROMOTED => 'Naik Kelas',
            // self::STAYED => 'Tinggal Kelas',
            // self::MOVED_INTERNAL => 'Mutasi Internal',
            // self::MOVED_EXTERNAL => 'Mutasi Keluar',
            // self::DROPPED_OUT => 'Putus Sekolah',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ENROLLED => 'success',
            self::GRADUATED => 'info',
            self::INACTIVE => 'danger',
            // self::PROMOTED, self::GRADUATED => 'success',
            // self::STAYED, self::DROPPED_OUT => 'danger',
            // self::MOVED_INTERNAL, self::MOVED_EXTERNAL => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ENROLLED => 'tabler-circle-dashed-check',
            self::GRADUATED => 'tabler-circle-check',
            self::INACTIVE => 'tabler-circle-x',
            // self::PROMOTED => 'heroicon-m-arrow-trending-up',
            // self::STAYED => 'heroicon-m-arrow-path',
            // self::MOVED_INTERNAL => 'heroicon-m-arrows-right-left',
            // self::MOVED_EXTERNAL => 'heroicon-m-arrow-right-on-rectangle',
            // self::DROPPED_OUT => 'heroicon-m-x-circle',
        };
    }

    /**
     * @return self[]
     */
    public static function getActiveStatuses(): array
    {
        return [
            self::ENROLLED,
        ];
    }

    /**
     * @return self[]
     */
    public static function getInactiveStatuses(): array
    {
        return collect(self::cases())
            ->reject(fn (self $case) => in_array($case, self::getActiveStatuses(), true))
            ->all();
    }
}
