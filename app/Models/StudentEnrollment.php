<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToClassroom;
use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToSchoolTerm;
use App\Models\Traits\BelongsToStudent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\StudentEnrollmentStatusEnum;
use App\Models\Traits\BelongsToSchoolyear;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $id
 * @property string $student_id
 * @property string $classroom_id
 * @property string $school_year_id
 * @property string $school_term_id
 * @property StudentEnrollmentStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read SchoolTerm $schoolTerm
 * @property-read SchoolYear $schoolYear
 * @property-read Student $student
 *
 * @method static \Database\Factories\StudentEnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereSchoolTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereSchoolYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class StudentEnrollment extends Model
{
    /** @use HasFactory<\Database\Factories\StudentEnrollmentFactory> */
    use HasFactory;
    use HasUlids;
    use BelongsToStudent;
    use BelongsToSchool;
    use BelongsToSchoolyear;
    use BelongsToSchoolTerm;
    use BelongsToClassroom;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => StudentEnrollmentStatusEnum::class,
        ];
    }

    protected static function booted(): void
    {
        // Sync student's active status whenever an enrollment is created or updated
        static::saved(function ($enrollment) {
            $enrollment->student?->syncActiveStatus();
        });
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('school_year_id',SchoolYear::getActive()?->getKey())
            ->where('school_term_id', SchoolTerm::getActive()?->getKey())
            ->whereIn('status', StudentEnrollmentStatusEnum::getActiveStatuses());
    }

    #[Scope]
    protected function inActive(Builder $query): Builder
    {
        return $query->whereNot('school_year_id',SchoolYear::getActive()?->getKey())
            ->whereNot('school_term_id', SchoolTerm::getActive()?->getKey())
            ->whereIn('status', StudentEnrollmentStatusEnum::getInactiveStatuses());
    }
}
