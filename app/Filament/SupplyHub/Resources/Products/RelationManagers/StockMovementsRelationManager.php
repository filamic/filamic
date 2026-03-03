<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Products\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Riwayat Pergerakan Stok')
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('item.sku')
                    ->label('SKU'),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->sortable(),
                TextColumn::make('branch.name')
                    ->label('Cabang'),
                TextColumn::make('user.name')
                    ->label('Pengguna'),
            ]);
    }
}
