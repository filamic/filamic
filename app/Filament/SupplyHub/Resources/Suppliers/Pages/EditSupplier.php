<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Suppliers\Pages;

use App\Actions\CreateProductWithItems;
use App\Enums\GradeEnum;
use App\Enums\LevelEnum;
use App\Filament\SupplyHub\Resources\Suppliers\SupplierResource;
use App\Models\ProductCategory;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Enums\ContentTabPosition;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Throwable;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;

    public ?string $activeRelationManager = 'productItems';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_product')
                ->label('Tambah Produk')
                ->modalDescription('Silahkan lengkapi form di bawah ini untuk menambahkan produk baru.')
                ->modalIcon('tabler-package')
                ->icon('tabler-plus')
                ->schema($this->getProductForm())
                ->action(function (array $data, $schema) {
                    try {
                        $data['supplier_id'] = $this->getRecord()->getKey();

                        $createProductWithItems = CreateProductWithItems::run($data);

                        if ($createProductWithItems) {
                            Notification::make()
                                ->title('Produk berhasil dibuat!')
                                ->success()
                                ->send();

                            $this->dispatch('refresh-product-item-relation-manager-table', $data);

                            // $schema->fill();

                            return;
                        }

                    } catch (\Illuminate\Database\QueryException $error) {
                        if ($error->getCode() === '23000' && str_contains(mb_strtolower($error->getMessage()), 'fingerprint')) {
                            Notification::make()
                                ->title('Produk sudah dibuat sebelumnya!')
                                ->warning()
                                ->send();

                            return;
                        }

                        throw $error;
                    } catch (Throwable $error) {
                        report($error);

                        Notification::make()
                            ->title('Gagal membuat produk!')
                            ->body('Terjadi Kesalahan Sistem. Silakan hubungi tim IT.')
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }
                }),
            // ->after(function($schema){
            //     $this->halt();
            // }),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabPosition(): ContentTabPosition
    {
        return ContentTabPosition::After; // Form utama akan pindah ke setelah tab relations
    }

    public function getContentTabIcon(): string
    {
        return 'tabler-article';
    }

    public function getContentTabLabel(): ?string
    {
        return 'Informasi Supplier';
    }

    private function getProductForm(): array
    {
        return [
            Group::make([
                Select::make('product_category_id')
                    ->label('Kategori Produk')
                    ->options(fn () => ProductCategory::pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->columnSpanFull()
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

                TextInput::make('purchase_price')
                    ->label('Harga Beli')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->currencyMask()
                    ->minValue(0)
                    ->default(0),

                TextInput::make('sale_price')
                    ->label('Harga Jual')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->currencyMask()
                    ->minValue(0)
                    ->default(0),

            ])->columns(2),
        ];
    }
}
