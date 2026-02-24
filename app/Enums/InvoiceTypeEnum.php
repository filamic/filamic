<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InvoiceTypeEnum: int implements HasColor, HasIcon, HasLabel
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

    public function getShortLabel(): string
    {
        return match ($this) {
            self::MONTHLY_FEE => 'SPP',
            self::BOOK_FEE => 'Buku',
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::MONTHLY_FEE => Color::Blue,
            self::BOOK_FEE => Color::Orange,
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::MONTHLY_FEE => 'tabler-credit-card',
            self::BOOK_FEE => 'tabler-book',
        };
    }
}
