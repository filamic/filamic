<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Suppliers;

use App\Filament\SupplyHub\Resources\ProductItems\RelationManagers\ProductItemsRelationManager;
use App\Filament\SupplyHub\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\SupplyHub\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\SupplyHub\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\SupplyHub\Resources\Suppliers\Schemas\SupplierForm;
use App\Filament\SupplyHub\Resources\Suppliers\Tables\SuppliersTable;
use App\Models\Supplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-truck';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return SupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuppliersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'productItems' => ProductItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
