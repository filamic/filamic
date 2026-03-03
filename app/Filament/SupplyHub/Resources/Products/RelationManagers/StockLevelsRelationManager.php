<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Products\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockLevelsRelationManager extends RelationManager
{
    protected static string $relationship = 'stocks';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Stok per Cabang')
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('item.sku')
                    ->label('SKU'),
                TextColumn::make('branch.name')
                    ->label('Cabang'),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->sortable(),
            ]);
    }
}
