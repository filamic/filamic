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
 * @property int $teacher_id
 * @property int $subject_id
 * @property int $classroom_id
 * @property int $school_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read SchoolYear $schoolYear
 * @property-read Subject $subject
 * @property-read Teacher $teacher
 *
 * @method static Builder<static>|Teaching active()
 * @method static \Database\Factories\TeachingFactory factory($count = null, $state = [])
 * @method static Builder<static>|Teaching newModelQuery()
 * @method static Builder<static>|Teaching newQuery()
 * @method static Builder<static>|Teaching query()
 * @method static Builder<static>|Teaching whereClassroomId($value)
 * @method static Builder<static>|Teaching whereCreatedAt($value)
 * @method static Builder<static>|Teaching whereId($value)
 * @method static Builder<static>|Teaching whereSchoolYearId($value)
 * @method static Builder<static>|Teaching whereSubjectId($value)
 * @method static Builder<static>|Teaching whereTeacherId($value)
 * @method static Builder<static>|Teaching whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Teaching extends Model
{
    /** @use HasFactory<\Database\Factories\TeachingFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->whereRelation('schoolYear', 'is_active', true);
    }

    public function canDelete(): bool
    {
        return $this->isActive();
    }

    public function isActive(): bool
    {
        return $this->schoolYear->isActive();
    }

    public function isInActive(): bool
    {
        return $this->schoolYear->isInactive();
    }
}
