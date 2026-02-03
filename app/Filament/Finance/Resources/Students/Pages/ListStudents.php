<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\Pages;

use App\Enums\InvoiceTypeEnum;
use App\Filament\Finance\Resources\Students\StudentResource;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentPaymentAccount;
use Carbon\Month;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function getSubheading(): string
    {
        $activeYear = SchoolYear::getActive();

        if (blank($activeYear)) {
            return 'Tahun Ajaran/Semester belum aktif! Mohon setel di pengaturan.';
        }

        $currentMonth = Month::from(now()->month)->name;

        return "Berdasarkan Tahun Ajaran Aktif: {$activeYear->name} — Bulan saat ini: {$currentMonth}";
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('tabler-plus')
                ->color('gray'),
            ActionGroup::make([
                self::createMonthlyInvoiceAction(),
                self::createBookInvoiceAction(),
            ])
                ->label('Buat Tagihan')
                ->color('success')
                ->button()
                ->icon('tabler-invoice'),
        ];
    }

    public static function createMonthlyInvoiceAction(): Action
    {
        return Action::make('createMonthlyInvoice')
            ->label('Uang Sekolah')
            ->requiresConfirmation()
            ->modalIcon('tabler-invoice')
            ->modalHeading('Buat Tagihan SPP')
            ->modalIconColor('success')
            ->modalDescription(function () {
                $schoolYear = SchoolYear::getActive();
                $schoolTerm = SchoolTerm::getActive();

                if (blank($schoolYear) || blank($schoolTerm)) {
                    return 'Tahun Ajaran/Semester belum aktif.';
                }

                return str("Tahun Ajaran: **{$schoolYear->name}** \n" .
                    "Semester: **{$schoolTerm->name->getLabel()}**")
                    ->markdown()
                    ->toHtmlString();
            })
            ->schema([
                Group::make([
                    Select::make('month_id')
                        ->options(
                            collect(Month::cases())
                                ->mapWithKeys(fn ($month) => [$month->value => $month->name])
                                ->toArray()
                        )
                        ->required()
                        ->label('Bulan')
                        ->selectablePlaceholder(false)
                        ->default(now()->addMonth()->month)
                        ->columnSpanFull(),
                    DatePicker::make('issued_at')
                        ->label('Tagihan Dibuka')
                        ->default(now()->setDay(28)),
                    DatePicker::make('due_date')
                        ->label('Tagihan Berakhir')
                        ->default(now()->addMonth()->day(20)),
                ])->columns(2),
            ])
            ->action(function (array $data) {

                /** @var Branch $branch */
                $branch = filament()->getTenant();

                // 1. Ambil data mentah dari request
                $monthId = data_get($data, 'month_id');
                $issuedAt = data_get($data, 'issued_at');
                $dueDate = data_get($data, 'due_date');

                /** @var Builder|Student $query */
                $query = $branch->students();

                $students = $query->active()
                    ->whereHas('currentPaymentAccount', function ($query) {
                        /** @var StudentPaymentAccount $query */
                        // @phpstan-ignore-next-line
                        $query->eligibleForMonthlyFee();
                    })
                    ->whereDoesntHave('invoices', function ($query) use ($monthId) {
                        $query->where('month_id', $monthId)
                            ->where('type', InvoiceTypeEnum::MONTHLY_FEE);
                    })
                    ->with([
                        'school',
                        'currentPaymentAccount',
                        'currentEnrollment.classroom',
                        'currentEnrollment.schoolYear',
                        'currentEnrollment.schoolTerm',
                    ])
                    ->get();

                if (blank($students)) {
                    Notification::make()
                        ->title('Tagihan tidak dibuat!')
                        ->body('Tidak ada siswa yang memenuhi syarat pembuatan tagihan.')
                        ->info()
                        ->send();

                    return;
                }

                $newInvoices = $students->map(function (Student $student) use ($monthId, $issuedAt, $dueDate, $branch) {
                    $enroll = $student->currentEnrollment;
                    $account = $student->currentPaymentAccount;

                    $preparedData = [
                        'branch_id' => $branch->getKey(),
                        'school_id' => $student->school_id,
                        'student_id' => $student->getKey(),
                        'classroom_id' => $enroll->classroom_id,
                        'school_year_id' => $enroll->school_year_id,
                        'school_term_id' => $enroll->school_term_id,

                        'branch_name' => $branch->name,
                        'school_name' => $student->school->name,
                        'classroom_name' => $enroll->classroom->name,
                        'school_year_name' => $enroll->schoolYear->name,
                        'school_term_name' => $enroll->schoolTerm->name,
                        'student_name' => $student->name,

                        'type' => InvoiceTypeEnum::MONTHLY_FEE,
                        'month_id' => $monthId,

                        'amount' => $account->monthly_fee_amount,
                        'total_amount' => $account->monthly_fee_amount,

                        'due_date' => $dueDate,
                        'issued_at' => $issuedAt,
                    ];

                    return $preparedData;
                })->toArray();

                if (blank($newInvoices)) {
                    Notification::make()
                        ->title('Tagihan tidak dibuat!')
                        ->body('Semua siswa terpilih sudah memiliki tagihan untuk bulan yang terpilih.')
                        ->info()
                        ->send();

                    return;
                }

                try {
                    DB::transaction(function () use ($newInvoices, $branch, $issuedAt, $dueDate, $monthId) {
                        $finalData = collect($newInvoices)->map(fn ($item) => Invoice::generateDefaults($item))->toArray();

                        Invoice::fillAndInsert($finalData);

                        Invoice::query()
                            ->whereIn('student_id', $branch->students()->pluck('students.id'))
                            ->unpaidMonthlyFee()
                            ->where('month_id', '!=', $monthId)
                            ->update([
                                'issued_at' => $issuedAt,
                                'due_date' => $dueDate,
                            ]);
                    });

                    Notification::make()
                        ->title('Berhasil membuat tagihan!')
                        ->body(count($newInvoices) . ' tagihan baru dibuat.')
                        ->success()
                        ->send();

                } catch (Throwable $error) {
                    report($error);

                    Notification::make()
                        ->title('Gagal membuat tagihan!')
                        ->body('Terjadi Kesalahan Sistem. Silakan hubungi tim IT.')
                        ->danger()
                        ->persistent()
                        ->send();

                    return;
                }

                // /** @var Branch $branch */
                // $branch = filament()->getTenant()->load('students');

                // $invoice = $branch->students()->with('currentPaymentAccount','currentEnrollment','invoices')->active()->get()->map(function (Student $student) use ($data, $branch) {

                //     if ($student->invoices()->where('month_id', data_get($data, 'month_id'))) {
                //         return null;
                //     }

                //     if (blank($student->currentPaymentAccount)) {
                //         return null;
                //     }

                //     return [
                //         'branch_id' => $branch->getKey(),
                //         'school_id' => $student->school_id,
                //         'classroom_id' => $student->currentEnrollment->classroom_id,
                //         'school_year_id' => $student->currentEnrollment->school_year_id,
                //         'school_term_id' => $student->currentEnrollment->school_term_id,
                //         'student_id' => $student->getKey(),

                //         'branch_name' => $branch->name,
                //         'school_name' => $student->school->name,
                //         'classroom_name' => $student->currentEnrollment->classroom->name,
                //         'school_year_name' => $student->currentEnrollment->schoolYear->name,
                //         'school_term_name' => $student->currentEnrollment->schoolTerm->name,
                //         'student_name' => $student->name,

                //         'type' => InvoiceTypeEnum::MONTHLY_FEE,
                //         'month_id' => data_get($data, 'month_id'),

                //         'amount' => $student->currentPaymentAccount->monthly_fee_amount,
                //         'total_amount' => $student->currentPaymentAccount->monthly_fee_amount,

                //         'due_date' => data_get($data, 'due_date'),
                //         'issued_at' => data_get($data, 'issued_at'),

                //         'description' => 'Tagihan SPP Bulan ' . Month::from(data_get($data, 'month_id'))->name,
                //     ];

                // })->filter()->toArray();

                // if (blank($invoice)) {
                //     Notification::make()
                //         ->title('Tagihan tidak dibuat!')
                //         ->body('Semua siswa terpilih sudah memiliki tagihan untuk bulan yang terpilih.')
                //         ->info()
                //         ->send();

                //     return;
                // }

                // try {

                //     DB::transaction(function () use ($invoice, $data, $branch) {
                //         Invoice::fillAndInsert($invoice);

                //         Invoice::unpaidMonthlyFee()
                //             ->whereIn('student_id', $branch->students()->pluck('id'))
                //             ->where('month_id', '!=', data_get($data, 'month_id'))
                //             ->update([
                //                 'issued_at' => data_get($data, 'issued_at'),
                //                 'due_date' => data_get($data, 'due_date')
                //             ]);
                //     });

                //     Notification::make()
                //         ->title('Berhasil membuat tagihan!')
                //         ->body(count($invoice) . ' tagihan baru telah dibuat dan semua tanggal tagihan yang belum terbayarkan sudah diupdate.')
                //         ->success()
                //         ->send();

                // } catch (Throwable $error) {
                //     report($error);

                //     Notification::make()
                //         ->title('Gagal membuat tagihan!')
                //         ->body('Terjadi Kesalahan Sistem. Silakan hubungi tim IT.')
                //         ->danger()
                //         ->persistent()
                //         ->send();

                //     return;
                // }
            });
    }

    public static function createBookInvoiceAction(): Action
    {
        return Action::make('createBookInvoiceAction')
            ->label('Buku Tahunan')
            ->requiresConfirmation()
            ->modalIcon('tabler-invoice')
            ->modalHeading('Buat Tagihan Buku')
            // ->modalDescription(function () {
            //     /** @var Branch $tenant */
            //     $tenant = filament()->getTenant();

            //     $count = StudentEnrollment::whereIn('classroom_id', $tenant->classrooms()->pluck('classrooms.id'))
            //         ->active()
            //         ->count();

            //     return "Aksi ini akan membuat tagihan buku untuk {$count} siswa aktif di cabang {$tenant->name}.";
            // })
            // ->color('success')
            // ->mountUsing(function (Action $action, Schema $form) {
            //     if (blank(SchoolYear::getActive()) || blank(SchoolTerm::getActive())) {

            //         Notification::make()
            //             ->title('Tidak bisa membuat tagihan!')
            //             ->body('Tahun Ajaran/Semester belum diaktifkan oleh administrator.')
            //             ->warning()
            //             ->send();

            //         $action->cancel();
            //     }

            //     /** @var Branch $tenant */
            //     $tenant = filament()->getTenant();

            //     $activeCount = StudentEnrollment::whereIn('classroom_id', $tenant->classrooms()->pluck('classrooms.id'))
            //         ->active()
            //         ->count();

            //     if ($activeCount === 0) {
            //         Notification::make()
            //             ->title('Tidak bisa membuat tagihan!')
            //             ->body('Tidak ada siswa aktif!')
            //             ->warning()
            //             ->send();
            //         $action->cancel();
            //     }

            //     $form->fill();
            // })
            ->action(function (array $data) {
                // Student::createBookFeeInvoice(filament()->getTenant(), $data);
            });
    }

    public function getTabs(): array
    {
        return [
            'Aktif' => Tab::make()
                ->modifyQueryUsing(fn (Builder | Student $query) => $query->active())
                ->icon('tabler-user-check'),
            'Tidak Aktif' => Tab::make()
                ->modifyQueryUsing(fn (Builder | Student $query) => $query->inActive())
                ->icon('tabler-user-x'),
            // 'Mutasi Internal' => Tab::make()
            //     ->modifyQueryUsing(fn (Student $query) => $query->inActive())
            //     ->icon('tabler-user-down'),
            // 'Calon Siswa' => Tab::make()
            //     ->modifyQueryUsing(fn (Student $query) => $query->inActive())
            //     ->icon('tabler-user-star'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(), // This method returns a component to display the tabs above a table
                $this->setTabInfo(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(), // This is the component that renders the table that is defined in this resource
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }

    private function setTabInfo(): ?Section
    {
        // TODO: jadikan ini enum aja StudentStatus
        $content = [
            'Aktif' => [
                'title' => 'Daftar Siswa Aktif',
                'desc' => 'Menampilkan seluruh siswa yang terdaftar pada tahun ajaran berjalan.',
                'color' => 'success',
            ],
            'Tidak Aktif' => [
                'title' => 'Arsip Siswa',
                'desc' => 'Menampilkan calon siswa atau siswa yang sudah lulus, pindah keluar, atau dikeluarkan.',
                'color' => 'danger',
            ],
            'Mutasi Internal' => [
                'title' => 'Perpindahan Cabang',
                'desc' => 'Menampilkan siswa yang masuk melalui jalur mutasi antar unit sekolah dalam yayasan.',
                'color' => 'info',
            ],
            'Calon Siswa' => [
                'title' => 'Penerimaan Baru',
                'desc' => 'Menampilkan calon siswa yang telah mendaftar untuk tahun ajaran mendatang.',
                'color' => 'warning',
            ],
        ];

        $detail = $content[$this->activeTab] ?? null;

        if (! $detail) {
            return null;
        }

        return Section::make()
            ->columnSpanFull()
            ->description(
                str("**{$detail['title']}** — {$detail['desc']}")
                    ->markdown()
                    ->toHtmlString()
            )
            ->icon($this->getTabs()[$this->activeTab]->getIcon())
            ->iconColor($detail['color']);
    }
}
