<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, HomeroomClass> $allHomeroomClasses
 * @property-read int|null $all_homeroom_classes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Teaching> $allTeachings
 * @property-read int|null $all_teachings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, HomeroomClass> $homeroomClasses
 * @property-read int|null $homeroom_classes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Teaching> $teachings
 * @property-read int|null $teachings_count
 * @property-read User|null $user
 *
 * @method static \Database\Factories\TeacherFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use BelongsToUser, HasFactory;

    protected $guarded = ['id'];

    public function allHomeroomClasses(): HasMany
    {
        return $this->hasMany(HomeroomClass::class);
    }

    public function allTeachings(): HasMany
    {
        return $this->hasMany(Teaching::class);
    }

    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(HomeroomClass::class)->active();
    }

    public function teachings(): HasMany
    {
        return $this->hasMany(Teaching::class)->active();
    }
}
