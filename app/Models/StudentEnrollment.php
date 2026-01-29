<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StudentEnrollmentStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $student_id
 * @property string $classroom_id
 * @property string $school_year_id
 * @property string $school_term_id
 * @property string $curriculum_id
 * @property StudentEnrollmentStatusEnum $status
 * @property string $enrolled_at
 * @property string|null $left_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Student $student
 *
 * @method static \Database\Factories\StudentEnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereCurriculumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereEnrolledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEnrollment whereLeftAt($value)
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

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => StudentEnrollmentStatusEnum::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
