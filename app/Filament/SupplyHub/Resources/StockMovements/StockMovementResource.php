<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\StockMovements;

use App\Filament\SupplyHub\Resources\StockMovements\Pages\CreateStockMovement;
use App\Filament\SupplyHub\Resources\StockMovements\Pages\ListStockMovements;
use App\Filament\SupplyHub\Resources\StockMovements\Schemas\StockMovementForm;
use App\Filament\SupplyHub\Resources\StockMovements\Tables\StockMovementsTable;
use App\Models\ProductStockMovement;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockMovementResource extends Resource
{
    protected static ?string $model = ProductStockMovement::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'Pergerakan Stok';

    protected static ?string $modelLabel = 'Pergerakan Stok';

    protected static ?string $pluralModelLabel = 'Pergerakan Stok';

    public static function form(Schema $schema): Schema
    {
        return StockMovementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $tenant = Filament::getTenant();

        if ($tenant) {
            $query->whereBelongsTo($tenant, 'branch');
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovement::route('/create'),
        ];
    }
}
