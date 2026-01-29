<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $school_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 *
 * @method static \Database\Factories\LearningGroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningGroup whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningGroup whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LearningGroup extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<\Database\Factories\LearningGroupFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];
}
