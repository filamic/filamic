<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StudentEnrollmentStatusEnum;
use App\Models\Traits\BelongsToClassroom;
use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToSchoolTerm;
use App\Models\Traits\BelongsToSchoolyear;
use App\Models\Traits\BelongsToStudent;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $student_id
 * @property string $classroom_id
 * @property string $school_year_id
 * @property string $school_term_id
 * @property StudentEnrollmentStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read School|null $school
 * @property-read SchoolTerm $schoolTerm
 * @property-read SchoolYear $schoolYear
 * @property-read Student $student
 *
 * @method static Builder<static>|StudentEnrollment active()
 * @method static Builder<static>|StudentEnrollment activeTerm()
 * @method static Builder<static>|StudentEnrollment activeYear()
 * @method static \Database\Factories\StudentEnrollmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|StudentEnrollment inactive()
 * @method static Builder<static>|StudentEnrollment newModelQuery()
 * @method static Builder<static>|StudentEnrollment newQuery()
 * @method static Builder<static>|StudentEnrollment query()
 * @method static Builder<static>|StudentEnrollment whereClassroomId($value)
 * @method static Builder<static>|StudentEnrollment whereCreatedAt($value)
 * @method static Builder<static>|StudentEnrollment whereId($value)
 * @method static Builder<static>|StudentEnrollment whereSchoolTermId($value)
 * @method static Builder<static>|StudentEnrollment whereSchoolYearId($value)
 * @method static Builder<static>|StudentEnrollment whereStatus($value)
 * @method static Builder<static>|StudentEnrollment whereStudentId($value)
 * @method static Builder<static>|StudentEnrollment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class StudentEnrollment extends Model
{
    use BelongsToClassroom;
    use BelongsToSchool;
    use BelongsToSchoolTerm;
    use BelongsToSchoolyear;
    use BelongsToStudent;

    /** @use HasFactory<\Database\Factories\StudentEnrollmentFactory> */
    use HasFactory;

    use HasUlids;

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
        return $query->where('school_year_id', SchoolYear::getActive()?->getKey())
            ->where('school_term_id', SchoolTerm::getActive()?->getKey())
            ->whereIn('status', StudentEnrollmentStatusEnum::getActiveStatuses());
    }

    #[Scope]
    protected function inactive(Builder $query): Builder
    {
        return $query->whereNot('school_year_id', SchoolYear::getActive()?->getKey())
            ->whereNot('school_term_id', SchoolTerm::getActive()?->getKey())
            ->whereIn('status', StudentEnrollmentStatusEnum::getInactiveStatuses());
    }
}
