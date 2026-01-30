<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToStudent;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $school_id
 * @property string $name
 * @property string $location
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property string|null $image
 * @property string|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School|null $school
 * @property-read Student|null $student
 *
 * @method static \Database\Factories\SchoolEventFactory factory($count = null, $state = [])
 * @method static Builder<static>|SchoolEvent newModelQuery()
 * @method static Builder<static>|SchoolEvent newQuery()
 * @method static Builder<static>|SchoolEvent ongoing()
 * @method static Builder<static>|SchoolEvent past()
 * @method static Builder<static>|SchoolEvent query()
 * @method static Builder<static>|SchoolEvent upcoming()
 * @method static Builder<static>|SchoolEvent whereCreatedAt($value)
 * @method static Builder<static>|SchoolEvent whereDetails($value)
 * @method static Builder<static>|SchoolEvent whereEndDate($value)
 * @method static Builder<static>|SchoolEvent whereId($value)
 * @method static Builder<static>|SchoolEvent whereImage($value)
 * @method static Builder<static>|SchoolEvent whereLocation($value)
 * @method static Builder<static>|SchoolEvent whereName($value)
 * @method static Builder<static>|SchoolEvent whereSchoolId($value)
 * @method static Builder<static>|SchoolEvent whereStartDate($value)
 * @method static Builder<static>|SchoolEvent whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolEvent extends Model
{
    use BelongsToSchool;
    use BelongsToStudent;

    /** @use HasFactory<\Database\Factories\SchoolEventFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    #[Scope]
    protected function upcoming(Builder $query): Builder
    {
        return $query
            ->where('start_date', '>', now()->toDateString())
            ->orderBy('start_date');
    }

    #[Scope]
    protected function ongoing(Builder $query): Builder
    {
        return $query
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString());
    }

    #[Scope]
    protected function past(Builder $query): Builder
    {
        return $query
            ->where('end_date', '<', now()->toDateString())
            ->orderByDesc('end_date');
    }
}
