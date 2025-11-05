<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SemesterEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property SemesterEnum $semester
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
 * @method static Builder<static>|SchoolYear whereSemester($value)
 * @method static Builder<static>|SchoolYear whereStartDate($value)
 * @method static Builder<static>|SchoolYear whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolYear extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolYearFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'semester' => SemesterEnum::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function inactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function activateExclusively(): void
    {
        static::deactivateOthers();
        $this->update(['is_active' => true]);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isInactive(): bool
    {
        return ! $this->is_active;
    }

    public static function deactivateOthers(): void
    {
        static::query()->active()->update(['is_active' => false]);
    }

    protected function nameWithSemester(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->name} ({$this->semester->getLabel()})",
        );
    }
}
