<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Products\Pages;

use App\Actions\CreateProductWithItems;
use App\Filament\SupplyHub\Resources\Products\ProductResource;
use App\Filament\SupplyHub\Resources\Suppliers\SupplierResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return CreateProductWithItems::run($data);
    }

    protected function getRedirectUrl(): string
    {
        /** @var Product $product */
        $product = $this->getRecord();

        return SupplierResource::getUrl('edit', [
            'record' => $product->supplier_id,
        ]);
    }
}
