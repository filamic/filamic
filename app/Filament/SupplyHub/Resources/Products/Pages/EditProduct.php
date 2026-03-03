<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Products\Pages;

use App\Actions\UpdateProductWithItems;
use App\Filament\SupplyHub\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Product $product */
        $product = $this->record;
        $product->load(['items.variationOptions', 'category.variations']);

        $item = $product->items->first();

        if ($item) {
            $data['purchase_price'] = $item->purchase_price;
            $data['sale_price'] = $item->sale_price;
        }

        if ($product->category->variations->isNotEmpty()) {
            $variations = [];

            foreach ($product->category->variations as $variation) {
                $selectedOptionIds = $product->items
                    ->flatMap(fn ($i) => $i->variationOptions
                        ->where('product_variation_id', $variation->getKey())
                        ->pluck('id'))
                    ->unique()
                    ->values()
                    ->all();

                $variations[str()->uuid()->toString()] = [
                    'variation_id' => $variation->getKey(),
                    'variation_name' => $variation->name,
                    'selected_options' => $selectedOptionIds,
                ];
            }

            $data['variations'] = $variations;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Product $record */
        return UpdateProductWithItems::run($record, $data);
    }
}
