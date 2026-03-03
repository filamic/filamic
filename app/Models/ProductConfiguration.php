<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $product_item_id
 * @property string $product_variation_option_id
 * @property-read ProductItem $item
 * @property-read ProductVariationOption $variationOption
 *
 * @method static \Database\Factories\ProductConfigurationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductConfiguration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductConfiguration whereProductItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductConfiguration whereProductVariationOptionId($value)
 *
 * @mixin \Eloquent
 */
class ProductConfiguration extends Pivot
{
    /** @use HasFactory<\Database\Factories\ProductConfigurationFactory> */
    use HasFactory;

    protected $table = 'product_configurations';

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    public function variationOption(): BelongsTo
    {
        return $this->belongsTo(ProductVariationOption::class, 'product_variation_option_id');
    }
}
