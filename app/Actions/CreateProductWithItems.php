<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\GradeEnum;
use App\Enums\LevelEnum;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\ProductVariationOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateProductWithItems
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): Product
    {
        Validator::make($data, [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'level' => ['nullable', Rule::enum(LevelEnum::class)],
            'grade' => ['nullable', Rule::enum(GradeEnum::class)],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            // 'variations' => ['nullable', 'array'],
            // 'variations.*.variation_id' => ['required_with:variations', 'string'],
            // 'variations.*.selected_options' => ['required_with:variations', 'array', 'min:1'],
            // 'variations.*.selected_options.*' => ['string'],
        ])->validate();

        return DB::transaction(function () use ($data): Product {
            $product = Product::create([
                'supplier_id' => $data['supplier_id'],
                'product_category_id' => $data['product_category_id'],
                'level' => $data['level'] ?? null,
                'grade' => $data['grade'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $category = ProductCategory::findOrFail($data['product_category_id']);

            $variations = collect($data['variations'] ?? [])
                ->filter(fn (array $row): bool => ! empty($row['selected_options']));

            if ($variations->isNotEmpty()) {
                $this->createVariantItems($product, $category, $variations->all(), $data);
            } else {
                $this->createSingleItem($product, $category, $data);
            }

            return $product;
        });
    }

    /**
     * @param  array<string, array<string, mixed>>  $variations
     * @param  array<string, mixed>  $data
     */
    private function createVariantItems(Product $product, ProductCategory $category, array $variations, array $data): void
    {
        $optionArrays = [];
        foreach ($variations as $row) {
            $optionArrays[] = $row['selected_options'];
        }

        $allOptionIds = collect($optionArrays)->flatten()->unique()->values()->all();
        $optionNameMap = ProductVariationOption::whereIn('id', $allOptionIds)
            ->pluck('name', 'id');

        $combinations = $this->cartesianProduct($optionArrays);

        foreach ($combinations as $optionIds) {
            $optionNames = collect($optionIds)
                ->map(fn (string $id): string => $optionNameMap->get($id, ''))
                ->all();

            $sku = Product::generateSku($category->code, $product->name, $optionNames);

            /** @var ProductItem $productItem */
            $productItem = $product->items()->create([
                'sku' => $sku,
                'purchase_price' => $data['purchase_price'],
                'sale_price' => $data['sale_price'],
                'is_active' => true,
            ]);

            $this->createStock($productItem);

            $productItem->variationOptions()->attach($optionIds);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createSingleItem(Product $product, ProductCategory $category, array $data): void
    {
        $sku = Product::generateSku($category->code, $product->name);

        /** @var ProductItem $productItem */
        $productItem = $product->items()->create([
            'sku' => $sku,
            'purchase_price' => $data['purchase_price'],
            'sale_price' => $data['sale_price'],
            'is_active' => true,
        ]);

        $this->createStock($productItem);
    }

    /**
     * @param  array<int, array<int, string>>  $arrays
     * @return array<int, array<int, string>>
     */
    private function cartesianProduct(array $arrays): array
    {
        $result = [[]];

        foreach ($arrays as $options) {
            $append = [];

            foreach ($result as $combination) {
                foreach ($options as $option) {
                    $append[] = [...$combination, $option];
                }
            }

            $result = $append;
        }

        return $result;
    }

    private function createStock(ProductItem $productItem): void
    {
        $branches = Branch::all();

        foreach ($branches as $branch) {
            $productItem->stocks()->create([
                'branch_id' => $branch->getKey(),
                'quantity' => 0,
            ]);
        }
    }
}
