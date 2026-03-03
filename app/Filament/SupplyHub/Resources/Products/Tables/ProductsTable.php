<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultGroup('supplier.name')
            ->groups(['supplier.name', 'category.name'])
            ->columns([
                // TextColumn::make('supplier.name')
                //     ->label('Supplier')
                //     ->searchable(),
                // TextColumn::make('category.name')
                //     ->label('Kategori')
                //     ->badge()
                //     ->searchable(),
                TextColumn::make('name')
                    ->description(fn (Product $record) => $record->getkey())
                    ->searchable(),
                TextColumn::make('items.variationOptions.name')
                    ->label('Variasi')
                    ->description('Klik untuk lihat detail variasi produk')
                    ->badge(),
                // TextColumn::make('item.purchase_price')
                //     ->label('Harga Beli'),
                // TextColumn::make('item.sale_price')
                //     ->label('Harga Jual'),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
