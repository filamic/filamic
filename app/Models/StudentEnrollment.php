<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StudentEnrollmentStatusEnum;
use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToClassroom;
use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToSchoolTerm;
use App\Models\Traits\BelongsToSchoolYear;
use App\Models\Traits\BelongsToStudent;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $branch_id
 * @property string $school_id
 * @property string $classroom_id
 * @property string $school_year_id
 * @property string $school_term_id
 * @property string $student_id
 * @property StudentEnrollmentStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Branch $branch
 * @property-read Classroom $classroom
 * @property-read School $school
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
 * @method static Builder<static>|StudentEnrollment whereBranchId($value)
 * @method static Builder<static>|StudentEnrollment whereClassroomId($value)
 * @method static Builder<static>|StudentEnrollment whereCreatedAt($value)
 * @method static Builder<static>|StudentEnrollment whereId($value)
 * @method static Builder<static>|StudentEnrollment whereSchoolId($value)
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
    use BelongsToBranch;
    use BelongsToClassroom;
    use BelongsToSchool;
    use BelongsToSchoolTerm;
    use BelongsToSchoolYear;
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
        static::creating(function ($enrollment) {
            if ($enrollment->student?->school) {
                $enrollment->branch_id = $enrollment->student->school->branch_id;
                $enrollment->school_id = $enrollment->student->school_id;
            }
        });

        // Sync student's active status whenever an enrollment is created or updated
        static::saved(function ($enrollment) {
            if ($enrollment->wasRecentlyCreated || $enrollment->wasChanged('status')) {
                $enrollment->student?->syncActiveStatus();
            }
        });
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        $activeYearId = SchoolYear::getActive()?->getKey();
        $activeTermId = SchoolTerm::getActive()?->getKey();

        if ($activeYearId === null || $activeTermId === null) {
            // No active year/term means no active enrollments
            return $query->whereRaw('1 = 0');
        }

        return $query->where($query->qualifyColumn('school_year_id'), $activeYearId)
            ->where($query->qualifyColumn('school_term_id'), $activeTermId)
            ->whereIn($query->qualifyColumn('status'), StudentEnrollmentStatusEnum::getActiveStatuses());
    }

    #[Scope]
    protected function inactive(Builder $query): Builder
    {
        $activeYearId = SchoolYear::getActive()?->getKey();
        $activeTermId = SchoolTerm::getActive()?->getKey();

        return $query->where(function (Builder $q) use ($activeYearId, $activeTermId) {
            $q->whereIn($q->qualifyColumn('status'), StudentEnrollmentStatusEnum::getInactiveStatuses());

            if ($activeYearId !== null) {
                $q->orWhere($q->qualifyColumn('school_year_id'), '!=', $activeYearId);
            }
            if ($activeTermId !== null) {
                $q->orWhere($q->qualifyColumn('school_term_id'), '!=', $activeTermId);
            }
        });
    }
}
