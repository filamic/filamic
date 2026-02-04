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

                $monthId = data_get($data, 'month_id');
                $issuedAt = data_get($data, 'issued_at');
                $dueDate = data_get($data, 'due_date');

                if(blank($monthId) || blank($issuedAt) || blank($dueDate)){
                    Notification::make()
                        ->title('Tagihan tidak dibuat!')
                        ->body('Data tidak lengkap')
                        ->info()
                        ->send();

                    return;
                }

                /** @var Builder|Student $getStudentsQuery */
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

                if (blank($students)) {
                    Notification::make()
                        ->title('Tagihan tidak dibuat!')
                        ->body('Tidak ada siswa yang memenuhi syarat pembuatan tagihan.')
                        ->info()
                        ->send();

                    return;
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

                try {
                    DB::transaction(function () use ($newInvoices, $branch, $issuedAt, $dueDate, $monthId) {
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
