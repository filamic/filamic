<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $product_category_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ProductCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductVariationOption> $options
 * @property-read int|null $options_count
 *
 * @method static \Database\Factories\ProductVariationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariation whereProductCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductVariation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ProductVariation extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariationFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductVariationOption::class);
    }
}
