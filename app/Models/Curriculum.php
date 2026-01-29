<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $school_id
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum active()
 * @method static \Database\Factories\CurriculumFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Curriculum extends Model
{
    use BelongsToSchool;
    use HasActiveState;

    /** @use HasFactory<\Database\Factories\CurriculumFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];
}
