<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\StockMovements\Tables;

use App\Enums\StockMovementTypeEnum;
use App\Models\ProductStockMovement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('transaction_date', 'desc')
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->date()
                    ->sortable(),
                TextColumn::make('item.product.name')
                    ->label('Produk')
                    ->searchable(),
                TextColumn::make('item.sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('item.variationOptions.formatted_name')
                    ->label('Variasi')
                    ->placeholder('-')
                    ->wrapHeader()
                    ->wrap()
                    ->badge(),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->sortable(),
                TextColumn::make('destination_branch')
                    ->label('Cabang Tujuan')
                    ->sortable()
                    ->getStateUsing(fn (ProductStockMovement $record): ?string => $record->type->is(StockMovementTypeEnum::TRANSFER_OUT)
                        ? $record->relatedMovement?->branch?->name
                        : $record->branch->name),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
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
