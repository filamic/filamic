<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\InvoiceTypeEnum;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\StudentPaymentAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateMonthlyFeeInvoice
{
    use AsAction;

    public function handle(Branch $branch, array $data): int
    {
        $monthId = data_get($data, 'month_id');
        $issuedAt = data_get($data, 'issued_at');
        $dueDate = data_get($data, 'due_date');

        if (blank($monthId) || blank($issuedAt) || blank($dueDate)) {
            return 0;
        }

        /** @var Builder|Student $getStudentsQuery */
        // @phpstan-ignore-next-line
        $getStudentsQuery = $branch->students();

        $students = $getStudentsQuery->active()
            ->whereHas('currentPaymentAccount', function ($query) {
                /** @var StudentPaymentAccount $query */
                // @phpstan-ignore-next-line
                $query->eligibleForMonthlyFee();
            })
            ->whereDoesntHave('invoices', function ($query) use ($monthId) {
                /** @var Invoice $query */
                // @phpstan-ignore-next-line
                $query->where('month_id', $monthId)->monthlyFee();
            })
            ->with([
                'school',
                'currentPaymentAccount',
                'currentEnrollment.classroom',
                'currentEnrollment.schoolYear',
                'currentEnrollment.schoolTerm',
            ])
            ->get();

        if ($students->isEmpty()) {
            return 0;
        }

        $newInvoices = $students->map(function (Student $student) use ($monthId, $issuedAt, $dueDate, $branch) {
            $enrollment = $student->currentEnrollment;
            $paymentAccount = $student->currentPaymentAccount;

            $prepareFingerprint = [
                'type' => InvoiceTypeEnum::MONTHLY_FEE->value,
                'student_id' => $student->getKey(),
                'school_year_id' => $enrollment->school_year_id,
                'month_id' => $monthId,
            ];

            $preparedData = [
                'fingerprint' => Invoice::generateFingerprint($prepareFingerprint),
                'reference_number' => Invoice::generateReferenceNumber(),

                'branch_id' => $branch->getKey(),
                'school_id' => $student->school_id,
                'student_id' => $student->getKey(),
                'classroom_id' => $enrollment->classroom_id,
                'school_year_id' => $enrollment->school_year_id,
                'school_term_id' => $enrollment->school_term_id,

                'branch_name' => $branch->name,
                'school_name' => $student->school->name,
                'classroom_name' => $enrollment->classroom->name,
                'school_year_name' => $enrollment->schoolYear->name,
                'school_term_name' => $enrollment->schoolTerm->name,
                'student_name' => $student->name,

                'type' => InvoiceTypeEnum::MONTHLY_FEE,
                'month_id' => $monthId,

                'amount' => $paymentAccount->monthly_fee_amount,
                'total_amount' => $paymentAccount->monthly_fee_amount,

                'due_date' => $dueDate,
                'issued_at' => $issuedAt,
            ];

            return $preparedData;
        })->toArray();

        return DB::transaction(function () use ($newInvoices, $branch, $issuedAt, $dueDate, $monthId) {
            foreach (array_chunk($newInvoices, 500) as $chunk) {
                Invoice::fillAndInsert($chunk);
            }

            Invoice::query()
                ->whereIn('student_id', $branch->students()->select('students.id'))
                ->unpaidMonthlyFee()
                ->where('month_id', '!=', $monthId)
                ->update([
                    'issued_at' => $issuedAt,
                    'due_date' => $dueDate,
                ]);

            return count($newInvoices);
        });
    }
}
