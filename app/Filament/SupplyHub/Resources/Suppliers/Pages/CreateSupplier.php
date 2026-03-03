<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Suppliers\Pages;

use App\Filament\SupplyHub\Resources\Suppliers\SupplierResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
}
