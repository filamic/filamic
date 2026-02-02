<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Enums\StatusInFamilyEnum;
use App\Models\Traits\BelongsToUser;
use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\StudentEnrollmentStatusEnum;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
 * @property bool $is_active
 * @property string|null $notes
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StudentEnrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read User|null $father
 * @property-read User|null $guardian
 * @property-read User|null $mother
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StudentPaymentAccount> $paymentAccounts
 * @property-read int|null $payment_accounts_count
 * @property-read User|null $user
 *
 * @method static Builder<static>|Student active()
 * @method static \Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Student inactive()
 * @method static Builder<static>|Student newModelQuery()
 * @method static Builder<static>|Student newQuery()
 * @method static Builder<static>|Student query()
 * @method static Builder<static>|Student whereBirthDate($value)
 * @method static Builder<static>|Student whereBirthPlace($value)
 * @method static Builder<static>|Student whereCreatedAt($value)
 * @method static Builder<static>|Student whereFatherId($value)
 * @method static Builder<static>|Student whereGender($value)
 * @method static Builder<static>|Student whereGuardianId($value)
 * @method static Builder<static>|Student whereId($value)
 * @method static Builder<static>|Student whereIsActive($value)
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
 * @method static Builder<static>|Student whereStatusInFamily($value)
 * @method static Builder<static>|Student whereUpdatedAt($value)
 * @method static Builder<static>|Student whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Student extends Model
{
    use BelongsToUser;
    use HasActiveState;

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

    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(StudentPaymentAccount::class);
    }

    // public function currentPaymentAccount(): HasOne
    // {
    //     return $this->hasOne(StudentPaymentAccount::class)


    //         // ->where('school_id', filament()->getTenant()->getKey())
    //         ;
    // }

    // public function currentPaymentAccount(): HasOne
    // {
    //     return $this->hasOne(StudentPaymentAccount::class)->ofMany([
    //         'published_at' => 'max',
    //         'id' => 'max',
    //     ], function (Builder $query) {
    //         $query->where('published_at', '<', now());
    //     });
    // }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function syncActiveStatus(): void
    {
        $activeYear = SchoolYear::getActive();

        if (empty($activeYear)) {
            return;
        }

        $isActive = $this->enrollments()
            ->active()
            ->exists();

        $this->updateQuietly([
            'is_active' => $isActive,
        ]);
    }
}
