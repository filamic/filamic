<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\InvoiceStatusEnum;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class PayMonthlyFeeInvoice
{
    use AsAction;

    public function handle(Student $student, array $data): bool
    {
        $invoicesIds = data_get($data, 'invoice_ids');
        $paidAt = data_get($data, 'paid_at');
        $paymentMethod = data_get($data, 'payment_method');
        $description = data_get($data, 'description');

        if (
            blank($invoicesIds) ||
            blank($paidAt) ||
            blank($paymentMethod)
        ) {
            return false;
        }

        return DB::transaction(function () use ($student, $invoicesIds, $paidAt, $paymentMethod, $description) {
            /** @var Builder|Invoice $query */
            // @phpstan-ignore-next-line
            $query = $student->invoices();

            $updatedCount = $query
                ->whereIn('id', $invoicesIds)
                ->unpaidMonthlyFee()
                ->update([
                    'status' => InvoiceStatusEnum::PAID,
                    'paid_at' => $paidAt,
                    'payment_method' => $paymentMethod,
                    'description' => $description,
                ]);

            return $updatedCount > 0;
        });
    }
}
