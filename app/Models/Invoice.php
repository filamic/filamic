<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceTypeEnum;
use App\Enums\MonthEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\Traits\BelongsToClassroom;
use App\Models\Traits\BelongsToSchoolyear;
use App\Models\Traits\BelongsToStudent;
use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $classroom_id
 * @property string $school_year_id
 * @property string $student_id
 * @property string $student_payment_account_id
 * @property string $school_name
 * @property string $classroom_name
 * @property string $school_year_name
 * @property string $student_name
 * @property string $virtual_account_number
 * @property InvoiceTypeEnum $type
 * @property numeric $amount
 * @property numeric $discount
 * @property numeric $fine
 * @property numeric $total_amount
 * @property MonthEnum|null $month_id
 * @property PaymentMethodEnum|null $payment_method
 * @property bool $is_paid
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string $start_date
 * @property string $end_date
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read SchoolYear $schoolYear
 * @property-read Student $student
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice activeYear()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClassroomName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereFine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereMonthId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSchoolYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSchoolYearName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStudentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStudentPaymentAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereVirtualAccountNumber($value)
 *
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    use BelongsToClassroom;
    use BelongsToSchoolyear;
    use BelongsToStudent;
    use HasActiveState;
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => InvoiceTypeEnum::class,
        'month_id' => MonthEnum::class,
        'payment_method' => PaymentMethodEnum::class,
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];
}
