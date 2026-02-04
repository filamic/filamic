<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InvoiceStatusEnum: int implements HasColor, HasLabel
{
    case UNPAID = 1;
    case PAID = 2;
    case PROCESSING = 3;
    case VOID = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::UNPAID => 'Belum Bayar',
            self::PAID => 'Lunas',
            self::PROCESSING => 'Proses Bank',
            self::VOID => 'Dibatalkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::UNPAID => 'warning',
            self::PAID => 'success',
            self::PROCESSING => 'info',
            self::VOID => 'gray',
        };
    }
}
