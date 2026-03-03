<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\ProductItems;

use App\Filament\SupplyHub\Resources\ProductItems\Pages\ListProductItems;
use App\Filament\SupplyHub\Resources\ProductItems\Schemas\ProductItemForm;
use App\Filament\SupplyHub\Resources\ProductItems\Tables\ProductItemsTable;
use App\Models\ProductItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductItemResource extends Resource
{
    protected static ?string $model = ProductItem::class;

    protected static bool $isScopedToTenant = false;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-package';

    public static function form(Schema $schema): Schema
    {
        return ProductItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductItemsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductItems::route('/'),
        ];
    }
}
