<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SchoolTermEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $id
 * @property int|null $legacy_old_id
 * @property SchoolTermEnum $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm active()
 * @method static \Database\Factories\SchoolTermFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTerm inactive()
 * @method static Builder<static>|SchoolTerm newModelQuery()
 * @method static Builder<static>|SchoolTerm newQuery()
 * @method static Builder<static>|SchoolTerm query()
 * @method static Builder<static>|SchoolTerm whereCreatedAt($value)
 * @method static Builder<static>|SchoolTerm whereId($value)
 * @method static Builder<static>|SchoolTerm whereIsActive($value)
 * @method static Builder<static>|SchoolTerm whereLegacyOldId($value)
 * @method static Builder<static>|SchoolTerm whereName($value)
 * @method static Builder<static>|SchoolTerm whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolTerm extends AcademicPeriod
{
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

    public function getAllowedMonths(): array
    {
        return $this->name->getAllowedMonths();
    }
}
