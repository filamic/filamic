<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\ProductItems\Pages;

use App\Filament\SupplyHub\Resources\ProductItems\ProductItemResource;
use App\Models\ProductCategory;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProductItems extends ListRecords
{
    protected static string $resource = ProductItemResource::class;

    public bool $priceEditable = false;

    public function getTabs(): array
    {
        $categories = ProductCategory::all();

        $tabs = [];

        foreach ($categories as $category) {
            $tabs[] = Tab::make()
                ->label($category->name)
                ->icon('tabler-category')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('product', 'product_category_id', $category->getKey()));
        }

        return $tabs;
    }
}
