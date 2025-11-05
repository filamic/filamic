<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasSchoolyear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $teacher_id
 * @property int $classroom_id
 * @property int $school_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read SchoolYear $schoolYear
 * @property-read Teacher $teacher
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass active()
 * @method static \Database\Factories\HomeroomClassFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereSchoolYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class HomeroomClass extends Model
{
    /** @use HasFactory<\Database\Factories\HomeroomClassFactory> */
    use HasFactory, HasSchoolyear;

    protected $guarded = ['id'];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
