<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\StockMovements\Tables;

use App\Enums\StockMovementTypeEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('item.product.name')
                    ->label('Produk')
                    ->searchable(),
                TextColumn::make('item.sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pengguna'),
                TextColumn::make('reference')
                    ->label('Referensi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(StockMovementTypeEnum::class),
            ]);
    }
}
