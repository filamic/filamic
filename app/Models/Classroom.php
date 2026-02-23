<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GradeEnum;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property int|null $legacy_old_id
 * @property string $school_id
 * @property string $name
 * @property GradeEnum $grade
 * @property string|null $phase
 * @property bool $is_moving_class
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 *
 * @method static \Database\Factories\ClassroomFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereIsMovingClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereLegacyOldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom wherePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classroom whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Classroom extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<\Database\Factories\ClassroomFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'grade' => GradeEnum::class,
            'is_moving_class' => 'boolean',
        ];
    }
}
