<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GradeEnum;
use App\Enums\LevelEnum;
use BackedEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use InvalidArgumentException;

/**
 * @property string $id
 * @property string $supplier_id
 * @property string $product_category_id
 * @property LevelEnum|null $level
 * @property GradeEnum|null $grade
 * @property string $name
 * @property string|null $description
 * @property string $fingerprint
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ProductCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductStockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductStock> $stocks
 * @property-read int|null $stocks_count
 * @property-read Supplier $supplier
 *
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereFingerprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProductCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'level' => LevelEnum::class,
            'grade' => GradeEnum::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->fingerprint = static::generateFingerprint([
                'supplier_id' => $product->supplier_id,
                'product_category_id' => $product->product_category_id,
                'name' => $product->name,
                'level' => $product->level,
                'grade' => $product->grade,
            ]);
        });

        static::updating(function (Product $product) {
            if ($product->isDirty(['supplier_id', 'product_category_id', 'name', 'level', 'grade'])) {
                $product->fingerprint = static::generateFingerprint([
                    'supplier_id' => $product->supplier_id,
                    'product_category_id' => $product->product_category_id,
                    'name' => $product->name,
                    'level' => $product->level,
                    'grade' => $product->grade,
                ]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function generateFingerprint(array $data): string
    {
        $components = [
            'supplier_id' => data_get($data, 'supplier_id'),
            'product_category_id' => data_get($data, 'product_category_id'),
            'name' => data_get($data, 'name'),
        ];

        foreach ($components as $key => $value) {
            if (blank($value)) {
                throw new InvalidArgumentException("Component [{$key}] is required for fingerprint.");
            }
        }

        $components['name'] = str($components['name'])->lower()->toString();

        $level = data_get($data, 'level');
        if (! blank($level)) {
            $components['level'] = $level instanceof BackedEnum ? $level->value : $level;
        }

        $grade = data_get($data, 'grade');
        if (! blank($grade)) {
            $components['grade'] = $grade instanceof BackedEnum ? $grade->value : $grade;
        }

        return collect($components)
            ->map(fn ($val) => $val instanceof BackedEnum ? $val->value : $val)
            ->join(':');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class);
    }

    public function stocks(): HasManyThrough
    {
        return $this->hasManyThrough(ProductStock::class, ProductItem::class, 'product_id', 'product_item_id', 'id', 'id');
    }

    public function stockMovements(): HasManyThrough
    {
        return $this->hasManyThrough(ProductStockMovement::class, ProductItem::class, 'product_id', 'product_item_id', 'id', 'id');
    }

    /**
     * @param  array<int, string>  $optionNames
     */
    public static function generateSku(string $categoryCode, string $productName, array $optionNames = []): string
    {
        $parts = [
            str($categoryCode)->upper()->toString(),
            str($productName)->slug('-')->upper()->limit(20, '')->toString(),
        ];

        foreach ($optionNames as $optionName) {
            $parts[] = str($optionName)->slug('-')->upper()->limit(10, '')->toString();
        }

        return implode('-', $parts);
    }
}
