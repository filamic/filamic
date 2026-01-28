<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToUser;
use App\Models\Traits\HasSchoolyear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $classroom_id
 * @property string $user_id
 * @property string $subject_id
 * @property string $school_year_id
 * @property string $school_term_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read SchoolYear $schoolYear
 * @property-read Subject $subject
 * @property-read User $user
 *
 * @method static Builder<static>|Teaching active()
 * @method static \Database\Factories\TeachingFactory factory($count = null, $state = [])
 * @method static Builder<static>|Teaching newModelQuery()
 * @method static Builder<static>|Teaching newQuery()
 * @method static Builder<static>|Teaching query()
 * @method static Builder<static>|Teaching whereClassroomId($value)
 * @method static Builder<static>|Teaching whereCreatedAt($value)
 * @method static Builder<static>|Teaching whereId($value)
 * @method static Builder<static>|Teaching whereSchoolTermId($value)
 * @method static Builder<static>|Teaching whereSchoolYearId($value)
 * @method static Builder<static>|Teaching whereSubjectId($value)
 * @method static Builder<static>|Teaching whereUpdatedAt($value)
 * @method static Builder<static>|Teaching whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Teaching extends Model
{
    use BelongsToUser;

    /** @use HasFactory<\Database\Factories\TeachingFactory> */
    use HasFactory;

    use HasSchoolyear;
    use HasUlids;

    protected $guarded = ['id'];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
