<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $product_id
 * @property string $sku
 * @property numeric $purchase_price
 * @property numeric $sale_price
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductStockMovement> $movements
 * @property-read int|null $movements_count
 * @property-read Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductStock> $stocks
 * @property-read int|null $stocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductVariationOption> $variationOptions
 * @property-read int|null $variation_options_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem active()
 * @method static \Database\Factories\ProductItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ProductItem extends Model
{
    use HasActiveState;

    /** @use HasFactory<\Database\Factories\ProductItemFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variationOptions(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariationOption::class, 'product_configurations', 'product_item_id', 'product_variation_option_id')
            ->with('variation');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ProductStockMovement::class);
    }
}
