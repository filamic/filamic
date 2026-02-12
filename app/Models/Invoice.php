<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\MonthEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToClassroom;
use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToSchoolTerm;
use App\Models\Traits\BelongsToSchoolYear;
use App\Models\Traits\BelongsToStudent;
use BackedEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use InvalidArgumentException;

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
 * @property InvoiceTypeEnum $type
 * @property MonthEnum|null $month
 * @property int $amount
 * @property int $fine
 * @property int $discount
 * @property int $total_amount
 * @property Carbon $issued_at
 * @property Carbon $due_date
 * @property InvoiceStatusEnum $status
 * @property PaymentMethodEnum|null $payment_method
 * @property Carbon|null $paid_at
 * @property string|null $payment_reference
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Branch $branch
 * @property-read Classroom $classroom
 * @property-read mixed $formatted_amount
 * @property-read mixed $formatted_discount
 * @property-read mixed $formatted_fine
 * @property-read mixed $formatted_total_amount
 * @property-read School $school
 * @property-read SchoolTerm $schoolTerm
 * @property-read SchoolYear $schoolYear
 * @property-read Student $student
 *
 * @method static Builder<static>|Invoice activeTerm()
 * @method static Builder<static>|Invoice activeYear()
 * @method static Builder<static>|Invoice bookFee()
 * @method static Builder<static>|Invoice monthlyFee()
 * @method static Builder<static>|Invoice monthlyFeeForThisSchoolYear(?int $month = null, ?int $schoolYearId = null)
 * @method static Builder<static>|Invoice newModelQuery()
 * @method static Builder<static>|Invoice newQuery()
 * @method static Builder<static>|Invoice paid()
 * @method static Builder<static>|Invoice paidMonthlyFee()
 * @method static Builder<static>|Invoice query()
 * @method static Builder<static>|Invoice unpaid()
 * @method static Builder<static>|Invoice unpaidMonthlyFee()
 * @method static Builder<static>|Invoice unpaidMonthlyFeeForThisSchoolYear(?int $month = null, ?int $schoolYearId = null)
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
 * @method static Builder<static>|Invoice whereMonth($value)
 * @method static Builder<static>|Invoice wherePaidAt($value)
 * @method static Builder<static>|Invoice wherePaymentMethod($value)
 * @method static Builder<static>|Invoice wherePaymentReference($value)
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
        'month' => MonthEnum::class,
        'status' => InvoiceStatusEnum::class,
        'payment_method' => PaymentMethodEnum::class,
        'paid_at' => 'datetime',
        'issued_at' => 'datetime',
        'due_date' => 'datetime',
        'amount' => 'integer',
        'fine' => 'integer',
        'discount' => 'integer',
        'total_amount' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (Invoice $invoice) {
            $invoice->fingerprint = static::generateFingerprint([
                'type' => $invoice->type,
                'student_id' => $invoice->student_id,
                'school_year_id' => $invoice->school_year_id,
                'month' => $invoice->month,
            ]);

            $invoice->reference_number = static::generateReferenceNumber();
        });
    }

    protected function formatCurrency(int | float $value): string
    {
        return Number::currency((float) $value, in: 'IDR', locale: config('app.locale'), precision: 0);
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatCurrency($this->amount),
        );
    }

    protected function formattedFine(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatCurrency($this->fine),
        );
    }

    protected function formattedDiscount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatCurrency($this->discount),
        );
    }

    protected function formattedTotalAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatCurrency($this->total_amount),
        );
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
    protected function bookFee(Builder $query): Builder
    {
        return $query->where('type', InvoiceTypeEnum::BOOK_FEE);
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

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    #[Scope]
    protected function monthlyFeeForThisSchoolYear(Builder $query, ?int $month = null, ?int $schoolYearId = null): Builder
    {
        $schoolYearId ??= SchoolYear::getActive()?->getKey();

        if ($schoolYearId === null) {
            // Return empty result set when no school year is active
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->monthlyFee()
            ->where('school_year_id', $schoolYearId)
            ->when($month, fn ($query) => $query->where('month', $month));
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    #[Scope]
    protected function unpaidMonthlyFeeForThisSchoolYear(Builder $query, ?int $month = null, ?int $schoolYearId = null): Builder
    {
        return $query
            ->monthlyFeeForThisSchoolYear($month, $schoolYearId)
            ->unpaid();
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    #[Scope]
    protected function paidMonthlyFee(Builder $query): Builder
    {
        return $query->paid()->monthlyFee();
    }

    public static function generateFingerprint(array $data): string
    {
        $components = [
            'type' => data_get($data, 'type'),
            'student_id' => data_get($data, 'student_id'),
            'school_year_id' => data_get($data, 'school_year_id'),
            'month' => data_get($data, 'month', 'annual'),
        ];

        foreach ($components as $key => $value) {
            if (blank($value)) {
                throw new InvalidArgumentException("Component [{$key}] is required for fingerprint.");
            }
        }

        return collect($components)
            ->map(fn ($val) => $val instanceof BackedEnum ? $val->value : $val)
            ->join(':');
    }

    public static function generateReferenceNumber(): string
    {
        return sprintf('INV/%s/%s',
            now()->format('Ymd'),
            Str::ulid()
        );
    }

    public static function generatePaymentReference(): string
    {
        return sprintf('PAY/%s/%s',
            now()->format('Ymd'),
            Str::ulid()
        );
    }

    public static function calculateFineFromOldestUnpaidInvoice(Student $student): int
    {
        $ratePerDay = (int) config('app.fine', 0);

        $oldestBill = $student->invoices()
            ->unpaidMonthlyFee()
            ->orderBy('due_date', 'asc')
            ->first();

        if (blank($oldestBill)) {
            return 0;
        }

        $dueDate = Carbon::parse($oldestBill->due_date)->startOfDay();
        $today = now()->startOfDay();

        if ($today->lessThanOrEqualTo($dueDate)) {
            return 0;
        }

        $daysLate = $dueDate->diffInDays($today);

        return (int) ($daysLate * $ratePerDay);
    }
}
