<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Invoice;
use App\Models\Student;
use App\Enums\InvoiceStatusEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class PayMonthlyFeeInvoice
{
    use AsAction;

    public function handle(Student $student, array $data): bool
    {
        $validated = Validator::make($data, [
            'invoice_ids' => ['required', 'array'],
            'invoice_ids.*' => ['required', 'exists:invoices,id'],
            'paid_at' => ['required', 'date'],
            'payment_method' => ['required', new Enum(PaymentMethodEnum::class)],
            'fine' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ])->validate();

        return DB::transaction(function () use ($student, $validated) {
            $fine = Invoice::calculateAccumulatedFine($student);
            $invoicesToPay = $student->invoices()
                ->whereIn('id', $validated['invoice_ids'])
                ->unpaidMonthlyFee()
                ->orderBy('due_date', 'asc')
                ->lockForUpdate() 
                ->get();

            if ($invoicesToPay->isEmpty()) {
                return false;
            }
            
            if ($invoicesToPay->count() !== count($validated['invoice_ids'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'invoice_ids' => 'Beberapa tagihan sudah diproses atau tidak ditemukan. Silakan refresh halaman.',
                ]);
            }

            $paymentReference = Invoice::generatePaymentReference();

            foreach ($invoicesToPay as $index => $invoice) {
                $isOldest = ($index === 0);

                $updated = $invoice->update([
                    'status' => InvoiceStatusEnum::PAID,
                    'paid_at' => $validated['paid_at'],
                    'payment_method' => $validated['payment_method'],
                    'payment_reference' => $paymentReference,
                    'description' => $validated['description'],
                    
                    // Logic Denda Final kita:
                    'fine' => $isOldest ? $fine : 0, 
                    'discount' => $isOldest ? $validated['discount'] : 0,
                    
                    // Update total_amount karena ada denda/diskon baru
                    'total_amount' => $isOldest 
                        ? ($invoice->amount + $fine - $validated['discount'])
                        : $invoice->amount,
                ]);

                if (! $updated) {
                    throw new \Exception("Gagal mengupdate invoice ID: {$invoice->id}");
                }
            }

            return true;
        });
    }
}
