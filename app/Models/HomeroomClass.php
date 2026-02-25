<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToSchoolYear;
use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $classroom_id
 * @property string $school_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read SchoolYear $schoolYear
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass activeYear()
 * @method static \Database\Factories\HomeroomClassFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereSchoolYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomClass whereUserId($value)
 *
 * @mixin \Eloquent
 */
class HomeroomClass extends Model
{
    use BelongsToSchoolYear;
    use BelongsToUser;

    /** @use HasFactory<\Database\Factories\HomeroomClassFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
