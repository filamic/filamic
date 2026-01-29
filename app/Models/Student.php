<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Enums\StatusInFamilyEnum;
use App\Enums\StudentStatusEnum;
use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $name
 * @property string|null $user_id
 * @property string|null $father_id
 * @property string|null $mother_id
 * @property string|null $guardian_id
 * @property string|null $nisn
 * @property string|null $nis
 * @property GenderEnum $gender
 * @property string|null $birth_place
 * @property string|null $birth_date
 * @property string|null $previous_education
 * @property string|null $joined_at_class
 * @property int|null $sibling_order_in_family
 * @property StatusInFamilyEnum|null $status_in_family
 * @property ReligionEnum|null $religion
 * @property StudentStatusEnum $status
 * @property string|null $notes
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $father
 * @property-read User|null $guardian
 * @property-read User|null $mother
 * @property-read User|null $user
 *
 * @method static Builder<static>|Student active()
 * @method static Builder<static>|Student droppedOut()
 * @method static \Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Student graduated()
 * @method static Builder<static>|Student moved()
 * @method static Builder<static>|Student newModelQuery()
 * @method static Builder<static>|Student newQuery()
 * @method static Builder<static>|Student nonActive()
 * @method static Builder<static>|Student prospective()
 * @method static Builder<static>|Student query()
 * @method static Builder<static>|Student whereBirthDate($value)
 * @method static Builder<static>|Student whereBirthPlace($value)
 * @method static Builder<static>|Student whereCreatedAt($value)
 * @method static Builder<static>|Student whereFatherId($value)
 * @method static Builder<static>|Student whereGender($value)
 * @method static Builder<static>|Student whereGuardianId($value)
 * @method static Builder<static>|Student whereId($value)
 * @method static Builder<static>|Student whereJoinedAtClass($value)
 * @method static Builder<static>|Student whereMetadata($value)
 * @method static Builder<static>|Student whereMotherId($value)
 * @method static Builder<static>|Student whereName($value)
 * @method static Builder<static>|Student whereNis($value)
 * @method static Builder<static>|Student whereNisn($value)
 * @method static Builder<static>|Student whereNotes($value)
 * @method static Builder<static>|Student wherePreviousEducation($value)
 * @method static Builder<static>|Student whereReligion($value)
 * @method static Builder<static>|Student whereSiblingOrderInFamily($value)
 * @method static Builder<static>|Student whereStatus($value)
 * @method static Builder<static>|Student whereStatusInFamily($value)
 * @method static Builder<static>|Student whereUpdatedAt($value)
 * @method static Builder<static>|Student whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Student extends Model
{
    use BelongsToUser;

    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    use HasUlids;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'gender' => GenderEnum::class,
            'status_in_family' => StatusInFamilyEnum::class,
            'religion' => ReligionEnum::class,
            'status' => StudentStatusEnum::class,
            'metadata' => 'array',
        ];
    }

    public function father(): BelongsTo
    {
        return $this->belongsTo(User::class, 'father_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mother_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::ACTIVE);
    }

    #[Scope]
    protected function graduated(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::GRADUATED);
    }

    #[Scope]
    protected function moved(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::MOVED);
    }

    #[Scope]
    protected function droppedOut(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::DROPPED_OUT);
    }

    #[Scope]
    protected function nonActive(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::NON_ACTIVE);
    }

    #[Scope]
    protected function prospective(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::PROSPECTIVE);
    }
}
