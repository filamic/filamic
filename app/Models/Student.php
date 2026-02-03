<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Enums\StatusInFamilyEnum;
use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToUser;
use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property string $name
 * @property string $school_id
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
 * @property-read StudentEnrollment|null $currentEnrollment
 * @property-read StudentPaymentAccount|null $currentPaymentAccount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StudentEnrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read User|null $father
 * @property-read User|null $guardian
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read User|null $mother
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StudentPaymentAccount> $paymentAccounts
 * @property-read int|null $payment_accounts_count
 * @property-read School $school
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
 * @method static Builder<static>|Student whereSchoolId($value)
 * @method static Builder<static>|Student whereSiblingOrderInFamily($value)
 * @method static Builder<static>|Student whereStatusInFamily($value)
 * @method static Builder<static>|Student whereUpdatedAt($value)
 * @method static Builder<static>|Student whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Student extends Model
{
    use BelongsToSchool;
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

    public function currentPaymentAccount(): HasOne
    {
        return $this->hasOne(StudentPaymentAccount::class)
            ->latestOfMany()
            ->where('student_payment_accounts.school_id', function ($sub) {
                $sub->select('school_id')
                    ->from('students')
                    ->whereColumn('students.id', 'student_payment_accounts.student_id')
                    ->limit(1);
            });
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return HasMany<StudentEnrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function currentEnrollment(): HasOne
    {
        return $this->hasOne(StudentEnrollment::class)
            ->latestOfMany()
            ->where(function ($query) {
                $query->active();
            });
    }

    public function syncActiveStatus(): void
    {
        $activeYear = SchoolYear::getActive();
        $activeTerm = SchoolTerm::getActive();

        if (blank($activeYear) || blank($activeTerm)) {
            return;
        }

        $isActive = $this->enrollments()
            ->active()
            ->exists();

        $this->updateQuietly([
            'is_active' => $isActive,
        ]);
    }

    public static function createMonthlyFeeInvoice(Branch $branch, array $data)
    {
        // implement it here get all the actice student using the branch
        // create the invoice for all of the student and skip and report the skipped student that doesnt have payment account
        // dont forget to check if the student invoice for the sleected month is exist then skip it
        // inser to invoice, update due_date, isseud_at, and virtual_account_number for unpaid invoice
    }

    public static function createBookFeeInvoice(Branch $branch, array $data)
    {

        // 1. get all active student based on the givin branch
        // $branch->students()->active()

        // [
        //     'student_enrollment_id' => $studentEnrollment->getKey(),
        //     'student_payment_account_id' => $currentPaymentAccount->getKey(),

        //     'school_name' => $currentPaymentAccount->school->name,
        //     'classroom_name' => $studentEnrollment->classroom->name,
        //     'school_year_name' => $studentEnrollment->schoolYear->name,
        //     'student_name' => $studentEnrollment->student->name,
        //     'virtual_account_number' => $currentPaymentAccount->monthly_fee_virtual_account,

        //     'type' => InvoiceTypeEnum::BOOK_FEE,
        //     'amount' => $currentPaymentAccount->monthly_fee_amount,
        //     'total_amount' => $currentPaymentAccount->monthly_fee_amount,

        //     'is_paid' => false,
        //     'start_date' => data_get($data, 'start_date'),
        //     'end_date' => data_get($data, 'end_date'),
        //     'description' => 'Tagihan uang buku',
        //     'is_active' => true,
        // ]
    }
}
