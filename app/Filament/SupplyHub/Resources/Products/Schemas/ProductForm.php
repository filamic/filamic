<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Products\Schemas;

use App\Enums\GradeEnum;
use App\Enums\LevelEnum;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->heading('Informasi Produk')
                    ->description('Silahkan isi informasi produk')
                    ->icon('tabler-archive')
                    ->components([
                        Select::make('supplier_id')
                            ->default(fn () => request('supplier'))
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('product_category_id')
                            ->label('Kategori Produk')
                            ->relationship('category', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if (blank($state)) {
                                    $set('variations', []);

                                    return;
                                }
                                $variations = ProductVariation::where('product_category_id', $state)->get();
                                $items = [];
                                foreach ($variations as $variation) {
                                    $items[str()->uuid()->toString()] = [
                                        'variation_id' => $variation->getKey(),
                                        'variation_name' => $variation->name,
                                        'selected_options' => [],
                                    ];
                                }
                                $set('variations', $items);
                            }),

                        Group::make()
                            ->visible(fn (Get $get) => filled($get('product_category_id')) && filled($get('variations')))
                            ->columns(2)
                            ->columnSpanFull()
                            ->components(function (Get $get) {
                                $components = [];

                                if (filled($get('variations'))) {
                                    $components[] = Repeater::make('variations')
                                        ->hiddenLabel()
                                        ->addable(false)
                                        ->deletable(false)
                                        ->reorderable(false)
                                        ->columnSpanFull()
                                        ->components([
                                            Hidden::make('variation_id'),
                                            Hidden::make('variation_name'),
                                            Select::make('selected_options')
                                                ->label(fn (Get $get) => 'Variasi ' . ($get('variation_name') ?? '') . ' Yang Tersedia')
                                                ->multiple()
                                                ->options(function (Get $get) {
                                                    $variationId = $get('variation_id');
                                                    if (blank($variationId)) {
                                                        return [];
                                                    }

                                                    return ProductVariationOption::where('product_variation_id', $variationId)
                                                        ->pluck('name', 'id');
                                                })
                                                ->columnSpanFull(),
                                        ])
                                        ->extraAttributes([
                                            'class' => '[&_.fi-fo-repeater-item-content]:!p-0 [&_.fi-fo-repeater-item]:!ring-0',
                                        ]);
                                }

                                return $components;
                            }),

                        Select::make('level')
                            ->label('Jenjang')
                            ->options(LevelEnum::class)
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('grade', null))
                            ->placeholder('Semua Jenjang'),
                        Select::make('grade')
                            ->label('Kelas')
                            ->options(function (Get $get) {
                                $level = $get('level');
                                if (blank($level)) {
                                    return [];
                                }

                                return collect(GradeEnum::forLevel($level))
                                    ->mapWithKeys(fn (GradeEnum $grade) => [
                                        $grade->value => $grade->getLabel(),
                                    ]);
                            })
                            ->placeholder('Semua Kelas'),
                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),

            ]);
    }
}
