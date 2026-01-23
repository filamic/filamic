<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $npsn
 * @property string|null $nis_nss_nds
 * @property string|null $telp
 * @property string|null $postal_code
 * @property string|null $village
 * @property string|null $subdistrict
 * @property string|null $city
 * @property string|null $province
 * @property string|null $website
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Classroom> $classrooms
 * @property-read int|null $classrooms_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SchoolEvent> $events
 * @property-read int|null $events_count
 * @property-read mixed $formatted_npsn
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SubjectCategory> $subjectCategories
 * @property-read int|null $subject_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subject> $subjects
 * @property-read int|null $subjects_count
 *
 * @method static \Database\Factories\SchoolFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereNisNssNds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereNpsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSubdistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereTelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereVillage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereWebsite($value)
 *
 * @mixin \Eloquent
 */
class School extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function subjectCategories(): HasMany
    {
        return $this->hasMany(SubjectCategory::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(SchoolEvent::class);
    }

    public function subjects(): HasManyThrough
    {
        return $this->hasManyThrough(Subject::class, SubjectCategory::class);
    }

    protected function formattedNpsn(): Attribute
    {
        return Attribute::make(
            get: fn () => filled($this->npsn) ? "NPSN: {$this->npsn}" : '-',
        );
    }
}
