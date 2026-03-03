<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\ProductItems\RelationManagers;

use App\Filament\SupplyHub\Resources\ProductItems\ProductItemResource;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Livewire\Attributes\On;

class ProductItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'productItems';

    protected static ?string $relatedResource = ProductItemResource::class;

    protected static string | BackedEnum | null $icon = 'tabler-package';

    protected static ?string $title = 'Daftar Produk';

    public bool $priceEditable = false;

    public function table(Table $table): Table
    {
        return $table
            ->heading(null);
    }

    #[On('refresh-product-item-relation-manager-table')]
    public function refreshProductItemRelationManagerTable(array $data): void
    {
        $this->resetTable();

        $this->tableDeferredFilters['level']['value'] = $data['level'];
        $this->tableDeferredFilters['grade']['value'] = $data['grade'];
        $this->tableDeferredFilters['category']['value'] = $data['product_category_id'];

        $this->applyTableFilters();
    }
}
