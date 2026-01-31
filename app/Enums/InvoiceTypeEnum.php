<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvoiceTypeEnum: int implements HasLabel
{
    case MONTHLY_FEE = 1;
    case BOOK_FEE = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::MONTHLY_FEE => 'Tagihan SPP',
            self::BOOK_FEE => 'Tagihan Buku',
        };
    }
}
