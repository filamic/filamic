<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvoiceStatusEnum: int implements HasLabel
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
}
