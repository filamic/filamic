<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\StockMovements\Schemas;

use App\Enums\StockMovementTypeEnum;
use App\Models\Branch;
use App\Models\ProductItem;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading('Data Pergerakan Stok')
                    ->icon('tabler-arrows-right-left')
                    ->components([
                        Select::make('product_item_id')
                            ->label('Item Produk')
                            ->options(fn () => ProductItem::query()
                                ->where('is_active', true)
                                ->with('product')
                                ->get()
                                ->mapWithKeys(fn (ProductItem $item) => [
                                    $item->getKey() => $item->product->name . ' — ' . $item->sku,
                                ])
                            )
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                        Select::make('type')
                            ->label('Tipe')
                            ->options(StockMovementTypeEnum::class)
                            ->required()
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('purchase_price')
                            ->label('Harga Beli')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(0),
                        TextInput::make('sale_price')
                            ->label('Harga Jual')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(0),
                        Select::make('destination_branch_id')
                            ->label('Cabang Tujuan')
                            ->options(function (): array {
                                $currentBranchId = Filament::getTenant()?->getKey();

                                return Branch::query()
                                    ->when($currentBranchId, fn ($query) => $query->where('id', '!=', $currentBranchId))
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->visible(fn (Get $get): bool => static::typeEquals($get('type'), StockMovementTypeEnum::TRANSFER_OUT)),
                        Select::make('student_id')
                            ->label('Siswa')
                            ->relationship('student', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => static::typeEquals($get('type'), StockMovementTypeEnum::DISTRIBUTION)),
                        TextInput::make('reference')
                            ->label('Referensi')
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function typeEquals(mixed $value, StockMovementTypeEnum $expected): bool
    {
        if (blank($value)) {
            return false;
        }

        if ($value instanceof StockMovementTypeEnum) {
            return $value === $expected;
        }

        return (int) $value === $expected->value;
    }
}
