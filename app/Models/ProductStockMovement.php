<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StockMovementTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $branch_id
 * @property string $product_item_id
 * @property string|null $related_movement_id
 * @property string|null $student_id
 * @property numeric $purchase_price
 * @property numeric $sale_price
 * @property StockMovementTypeEnum $type
 * @property int $quantity
 * @property string|null $reference
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Branch $branch
 * @property-read ProductItem $item
 * @property-read ProductStockMovement|null $relatedMovement
 * @property-read Student|null $student
 * @property-read User $user
 *
 * @method static \Database\Factories\ProductStockMovementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereProductItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereRelatedMovementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductStockMovement whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ProductStockMovement extends Model
{
    /** @use HasFactory<\Database\Factories\ProductStockMovementFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => StockMovementTypeEnum::class,
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function relatedMovement(): BelongsTo
    {
        return $this->belongsTo(ProductStockMovement::class, 'related_movement_id');
    }
}
