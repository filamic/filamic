<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Suppliers\Pages;

use App\Filament\SupplyHub\Resources\Suppliers\SupplierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
