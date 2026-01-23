<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $school_id
 * @property string $name
 * @property string $location
 * @property \Illuminate\Support\Carbon $starts_at
 * @property \Illuminate\Support\Carbon $ends_at
 * @property string|null $image
 * @property string|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School|null $school
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
 * @method static Builder<static>|SchoolEvent whereEndsAt($value)
 * @method static Builder<static>|SchoolEvent whereId($value)
 * @method static Builder<static>|SchoolEvent whereImage($value)
 * @method static Builder<static>|SchoolEvent whereLocation($value)
 * @method static Builder<static>|SchoolEvent whereName($value)
 * @method static Builder<static>|SchoolEvent whereSchoolId($value)
 * @method static Builder<static>|SchoolEvent whereStartsAt($value)
 * @method static Builder<static>|SchoolEvent whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolEvent extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolEventFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    #[Scope]
    protected function upcoming(Builder $query): Builder
    {
        return $query
            ->where('ends_at', '>=', now())
            ->orderBy('starts_at');
    }

    #[Scope]
    protected function ongoing(Builder $query): Builder
    {
        return $query
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    #[Scope]
    protected function past(Builder $query): Builder
    {
        return $query
            ->where('ends_at', '<', now())
            ->orderByDesc('ends_at');
    }
}
