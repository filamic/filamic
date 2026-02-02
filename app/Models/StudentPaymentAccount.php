<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToStudent;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $school_id
 * @property string $student_id
 * @property string|null $monthly_fee_virtual_account
 * @property string|null $book_fee_virtual_account
 * @property numeric $monthly_fee_amount
 * @property numeric $book_fee_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read Student $student
 *
 * @method static Builder<static>|StudentPaymentAccount activeBookFee()
 * @method static Builder<static>|StudentPaymentAccount activeMonthlyFee()
 * @method static Builder<static>|StudentPaymentAccount newModelQuery()
 * @method static Builder<static>|StudentPaymentAccount newQuery()
 * @method static Builder<static>|StudentPaymentAccount query()
 * @method static Builder<static>|StudentPaymentAccount whereBookFeeAmount($value)
 * @method static Builder<static>|StudentPaymentAccount whereBookFeeVirtualAccount($value)
 * @method static Builder<static>|StudentPaymentAccount whereCreatedAt($value)
 * @method static Builder<static>|StudentPaymentAccount whereId($value)
 * @method static Builder<static>|StudentPaymentAccount whereMonthlyFeeAmount($value)
 * @method static Builder<static>|StudentPaymentAccount whereMonthlyFeeVirtualAccount($value)
 * @method static Builder<static>|StudentPaymentAccount whereSchoolId($value)
 * @method static Builder<static>|StudentPaymentAccount whereStudentId($value)
 * @method static Builder<static>|StudentPaymentAccount whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class StudentPaymentAccount extends Model
{
    use BelongsToSchool;
    use BelongsToStudent;
    use HasUlids;

    protected $guarded = ['id'];

    #[Scope]
    protected function activeMonthlyFee(Builder $query): Builder
    {
        return $query->whereNotNull('monthly_fee_virtual_account')
            ->where('monthly_fee_amount', '>=', 0);
    }

    #[Scope]
    protected function activeBookFee(Builder $query): Builder
    {
        return $query->whereNotNull('book_fee_virtual_account')
            ->where('book_fee_amount', '>=', 0);
    }
}
