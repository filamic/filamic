<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\StockMovements\Pages;

use App\Filament\SupplyHub\Resources\StockMovements\StockMovementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
