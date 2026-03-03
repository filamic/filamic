<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StockMovementTypeEnum: int implements HasColor, HasIcon, HasLabel
{
    case STOCK_IN = 1;
    case DISTRIBUTION = 2;
    case DIRECT_SALE = 3;
    case TRANSFER_OUT = 4;
    case TRANSFER_IN = 5;
    case ADJUSTMENT = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::STOCK_IN => 'Barang Masuk',
            self::DISTRIBUTION => 'Pembagian',
            self::DIRECT_SALE => 'Penjualan Langsung',
            self::TRANSFER_OUT => 'Transfer Keluar',
            self::TRANSFER_IN => 'Transfer Masuk',
            self::ADJUSTMENT => 'Penyesuaian',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::STOCK_IN => 'success',
            self::DISTRIBUTION => 'info',
            self::DIRECT_SALE => 'success',
            self::TRANSFER_OUT => 'danger',
            self::TRANSFER_IN => 'success',
            self::ADJUSTMENT => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::STOCK_IN => 'heroicon-o-inbox',
            self::DISTRIBUTION => 'heroicon-o-truck',
            self::DIRECT_SALE => 'heroicon-o-shopping-cart',
            self::TRANSFER_OUT => 'heroicon-o-arrow-right',
            self::TRANSFER_IN => 'heroicon-o-arrow-left',
            self::ADJUSTMENT => 'heroicon-o-pencil',
        };
    }
}
