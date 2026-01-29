<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SchoolTermEnum;
use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property SchoolTermEnum $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm active()
 * @method static \Database\Factories\SchoolTermFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolTerm extends Model
{
    use HasActiveState;

    /** @use HasFactory<\Database\Factories\SchoolTermFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'name' => SchoolTermEnum::class,
        ];
    }
}
