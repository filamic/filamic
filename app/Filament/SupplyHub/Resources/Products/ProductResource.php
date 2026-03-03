<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Products;

use App\Filament\SupplyHub\Resources\Products\Pages\CreateProduct;
use App\Filament\SupplyHub\Resources\Products\Pages\EditProduct;
use App\Filament\SupplyHub\Resources\Products\Pages\ListProducts;
use App\Filament\SupplyHub\Resources\Products\RelationManagers\ItemsRelationManager;
use App\Filament\SupplyHub\Resources\Products\RelationManagers\StockLevelsRelationManager;
use App\Filament\SupplyHub\Resources\Products\RelationManagers\StockMovementsRelationManager;
use App\Filament\SupplyHub\Resources\Products\Schemas\ProductForm;
use App\Filament\SupplyHub\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isScopedToTenant = false;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-archive';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            StockLevelsRelationManager::class,
            StockMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
