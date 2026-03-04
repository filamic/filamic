<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToSchoolYear;
use App\Models\Traits\BelongsToSupplier;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderItem> $items
 * @property-read int|null $items_count
 * @property-read SchoolYear|null $schoolYear
 * @property-read Supplier|null $supplier
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order activeYear()
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 *
 * @mixin \Eloquent
 */
class Order extends Model
{
    use BelongsToSchoolYear;
    use BelongsToSupplier;

    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'date',
            'grand_total' => 'decimal:2',
            'total_items' => 'integer',
            'discount_percentage' => 'integer',
        ];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
