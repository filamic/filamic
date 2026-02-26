<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ProductCategories;

use App\Filament\Admin\Resources\ProductCategories\Pages\ManageProductCategories;
use App\Models\ProductCategory;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static UnitEnum | string | null $navigationGroup = 'Purchase Order Management';

    protected static ?int $navigationSort = 1;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-category';

    protected static ?string $modelLabel = 'Produk Kategori';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Produk Kategori')
                    ->required()
                    ->placeholder('Contoh: Seragam|Buku|Aksesori'),
                TextInput::make('code')
                    ->label('Kode Produk Kategori')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: SRG|BK|AKS'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull()
                    ->hint('Optional'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProductCategories::route('/'),
        ];
    }
}
