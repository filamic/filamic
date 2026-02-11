<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Lorisleiva\Actions\Concerns\AsAction;

class PrintMonthlyFeeInvoice
{
    use AsAction;

    public function handle(Student $student, array $data): ?string
    {
        $invoicesIds = data_get($data, 'invoice_ids');

        if (
            blank($invoicesIds)
        ) {
            return null;
        }

        $invoices = $student->invoices()
            ->whereIn('id', $invoicesIds)
            ->paidMonthlyFee()
            ->get();

        if ($invoices->isEmpty()) {
            return null;
        }

        $view = 'filament.finance.pdf.monthly-invoice';

        $pdf = Pdf::loadView(
            $view,
            compact('invoices', 'student')
        )->setPaper([0, 0, 609.449, 935.433], 'portrait');

        $filaneme = 'pdf/invoice-spp_' . $student->getKey() . '.pdf';

        $pdf->save($filaneme, 'public');

        return $filaneme;
    }
}
