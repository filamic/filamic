<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $school_id
 * @property string $name
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subject> $subjects
 * @property-read int|null $subjects_count
 *
 * @method static \Database\Factories\SubjectCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SubjectCategory extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<\Database\Factories\SubjectCategoryFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
