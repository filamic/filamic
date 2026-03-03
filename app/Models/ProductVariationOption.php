<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property string $product_variation_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductItem> $productItems
 * @property-read int|null $product_items_count
 * @property-read ProductVariation $variation
 *
 * @method static \Database\Factories\ProductVariationOptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariationOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariationOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariationOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariationOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariationOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariationOption whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariationOption whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariationOption whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ProductVariationOption extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariationOptionFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function formattedName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->variation->name . ': ' . $this->name
        );
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function productItems(): BelongsToMany
    {
        return $this->belongsToMany(ProductItem::class, 'product_configurations', 'product_variation_option_id', 'product_item_id');
    }
}
