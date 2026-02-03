<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToClassroom;
use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToSchoolTerm;
use App\Models\Traits\BelongsToSchoolYear;
use App\Models\Traits\BelongsToStudent;
use Carbon\Month;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $branch_id
 * @property string $school_id
 * @property string $classroom_id
 * @property string $school_year_id
 * @property string $school_term_id
 * @property string $student_id
 * @property string $reference_number
 * @property string $fingerprint
 * @property string $branch_name
 * @property string $school_name
 * @property string $classroom_name
 * @property string $school_year_name
 * @property string $school_term_name
 * @property string $student_name
 * @property string $virtual_account_number
 * @property InvoiceTypeEnum $type
 * @property Month|null $month_id
 * @property numeric $amount
 * @property numeric $discount
 * @property numeric $fine
 * @property numeric $total_amount
 * @property InvoiceStatusEnum $status
 * @property PaymentMethodEnum|null $payment_method
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string $due_date
 * @property string $issued_at
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Branch $branch
 * @property-read Classroom $classroom
 * @property-read School $school
 * @property-read SchoolTerm $schoolTerm
 * @property-read SchoolYear $schoolYear
 * @property-read Student $student
 *
 * @method static Builder<static>|Invoice activeTerm()
 * @method static Builder<static>|Invoice activeYear()
 * @method static Builder<static>|Invoice monthlyFee()
 * @method static Builder<static>|Invoice newModelQuery()
 * @method static Builder<static>|Invoice newQuery()
 * @method static Builder<static>|Invoice paid()
 * @method static Builder<static>|Invoice query()
 * @method static Builder<static>|Invoice unpaid()
 * @method static Builder<static>|Invoice unpaidMonthlyFee()
 * @method static Builder<static>|Invoice whereAmount($value)
 * @method static Builder<static>|Invoice whereBranchId($value)
 * @method static Builder<static>|Invoice whereBranchName($value)
 * @method static Builder<static>|Invoice whereClassroomId($value)
 * @method static Builder<static>|Invoice whereClassroomName($value)
 * @method static Builder<static>|Invoice whereCreatedAt($value)
 * @method static Builder<static>|Invoice whereDescription($value)
 * @method static Builder<static>|Invoice whereDiscount($value)
 * @method static Builder<static>|Invoice whereDueDate($value)
 * @method static Builder<static>|Invoice whereFine($value)
 * @method static Builder<static>|Invoice whereFingerprint($value)
 * @method static Builder<static>|Invoice whereId($value)
 * @method static Builder<static>|Invoice whereIssuedAt($value)
 * @method static Builder<static>|Invoice whereMonthId($value)
 * @method static Builder<static>|Invoice wherePaidAt($value)
 * @method static Builder<static>|Invoice wherePaymentMethod($value)
 * @method static Builder<static>|Invoice whereReferenceNumber($value)
 * @method static Builder<static>|Invoice whereSchoolId($value)
 * @method static Builder<static>|Invoice whereSchoolName($value)
 * @method static Builder<static>|Invoice whereSchoolTermId($value)
 * @method static Builder<static>|Invoice whereSchoolTermName($value)
 * @method static Builder<static>|Invoice whereSchoolYearId($value)
 * @method static Builder<static>|Invoice whereSchoolYearName($value)
 * @method static Builder<static>|Invoice whereStatus($value)
 * @method static Builder<static>|Invoice whereStudentId($value)
 * @method static Builder<static>|Invoice whereStudentName($value)
 * @method static Builder<static>|Invoice whereTotalAmount($value)
 * @method static Builder<static>|Invoice whereType($value)
 * @method static Builder<static>|Invoice whereUpdatedAt($value)
 * @method static Builder<static>|Invoice whereVirtualAccountNumber($value)
 *
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    use BelongsToBranch;
    use BelongsToClassroom;
    use BelongsToSchool;
    use BelongsToSchoolTerm;
    use BelongsToSchoolYear;
    use BelongsToStudent;
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => InvoiceTypeEnum::class,
        'month_id' => Month::class,
        'status' => InvoiceStatusEnum::class,
        'payment_method' => PaymentMethodEnum::class,
        'paid_at' => 'datetime',
    ];


    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (blank($invoice->fingerprint)) {
                $invoice->fingerprint = implode('_', [
                    $invoice->type,
                    $invoice->student_id,
                    $invoice->school_year_id,
                    $invoice->month_id ?? 'annual',
                ]);
            }

            if (blank($invoice->reference_number)) {
                $invoice->reference_number = 'INV/' . now()->format('Ymd') . '/' . str()->random(6)->upper();
            }
        });
    }

    #[Scope]
    protected function paid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatusEnum::PAID)
            ->whereNotNull('paid_at');
    }

    #[Scope]
    protected function unpaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatusEnum::UNPAID)
            ->whereNull('paid_at');
    }

    #[Scope]
    protected function monthlyFee(Builder $query): Builder
    {
        return $query->where('type', InvoiceTypeEnum::MONTHLY_FEE);
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    #[Scope]
    protected function unpaidMonthlyFee(Builder $query): Builder
    {
        return $query->unpaid()->monthlyFee();
    }
}
