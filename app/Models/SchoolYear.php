<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $name_with_semester
 *
 * @method static Builder<static>|SchoolYear active()
 * @method static \Database\Factories\SchoolYearFactory factory($count = null, $state = [])
 * @method static Builder<static>|SchoolYear inactive()
 * @method static Builder<static>|SchoolYear newModelQuery()
 * @method static Builder<static>|SchoolYear newQuery()
 * @method static Builder<static>|SchoolYear query()
 * @method static Builder<static>|SchoolYear whereCreatedAt($value)
 * @method static Builder<static>|SchoolYear whereEndDate($value)
 * @method static Builder<static>|SchoolYear whereId($value)
 * @method static Builder<static>|SchoolYear whereIsActive($value)
 * @method static Builder<static>|SchoolYear whereName($value)
 * @method static Builder<static>|SchoolYear whereStartDate($value)
 * @method static Builder<static>|SchoolYear whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolYear extends Model
{
    use HasActiveState;

    /** @use HasFactory<\Database\Factories\SchoolYearFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected function nameWithSemester(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->name} (Please fix this we moved the semester to a new tabel)",
        );
    }
}
