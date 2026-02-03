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
use Filament\Support\Enums\Size;
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
            ->modalDescription(function () {
                /** @var Branch $tenant */
                $tenant = filament()->getTenant();

                $count = StudentEnrollment::whereIn('classroom_id', $tenant->classrooms()->pluck('classrooms.id'))
                    ->active()
                    ->count();

                return "Aksi ini akan membuat tagihan SPP untuk {$count} siswa aktif di cabang {$tenant->name}.";
            })
            ->color('success')
            ->mountUsing(function (Action $action, Schema $form) {
                if (blank(SchoolYear::getActive()) || blank(SchoolTerm::getActive())) {

                    Notification::make()
                        ->title('Tidak bisa membuat tagihan!')
                        ->body('Tahun Ajaran/Semester belum diaktifkan oleh administrator.')
                        ->warning()
                        ->send();

                    $action->cancel();
                }

                /** @var Branch $tenant */
                $tenant = filament()->getTenant();

                $activeCount = StudentEnrollment::whereIn('classroom_id', $tenant->classrooms()->pluck('classrooms.id'))
                    ->active()
                    ->count();

                if ($activeCount === 0) {
                    Notification::make()
                        ->title('Tidak bisa membuat tagihan!')
                        ->body('Tidak ada siswa aktif!')
                        ->warning()
                        ->send();
                    $action->cancel();
                }

                $form->fill();
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
                    DatePicker::make('start_date')
                        ->label('Tagihan Dibuka')
                        ->default(now()->setDay(28)),
                    DatePicker::make('end_date')
                        ->label('Tagihan Berakhir')
                        ->default(now()->addMonth()->day(20)),
                ])->columns(2),
            ])
            ->action(function (array $data, Action $action) {

                /** @var Branch $tenant */
                $tenant = filament()->getTenant();

                $classroomIds = $tenant
                    ->classrooms()
                    ->pluck('classrooms.id')
                    ->toArray();

                $studentEnrollments = StudentEnrollment::with([
                    'student',
                    'classroom',
                    'classroom.school',
                    'schoolYear',
                ])
                    ->active()
                    ->whereIn('classroom_id', $classroomIds)
                    ->get();

                if ($studentEnrollments->isEmpty()) {

                    Notification::make()
                        ->title('Tagihan tidak dibuat!')
                        ->body('Tidak ada siswa aktif.')
                        ->warning()
                        ->send();

                    return;
                }

                $paymentAccounts = StudentPaymentAccount::whereIn(
                    'school_id', $studentEnrollments->pluck('classroom.school_id')->toArray()
                )
                    ->with('student', 'school')
                    ->activeMonthlyFee()
                    ->get();

                if ($paymentAccounts->isEmpty()) {
                    Notification::make()
                        ->title('Tagihan tidak dibuat!')
                        ->body('Tidak ada payment account yang aktif.')
                        ->warning()
                        ->send();

                    return;
                }

                $existingInvoiceEnrollmentIds = Invoice::query()
                    ->whereIn('student_enrollment_id', $studentEnrollments->pluck('id'))
                    ->where('type', InvoiceTypeEnum::MONTHLY_FEE)
                    ->where('month_id', $data['month_id'])
                    ->pluck('student_enrollment_id')
                    ->toArray();

                $invoice = $studentEnrollments->map(function (StudentEnrollment $studentEnrollment) use ($data, $paymentAccounts, $existingInvoiceEnrollmentIds) {

                    if (in_array($studentEnrollment->getKey(), $existingInvoiceEnrollmentIds)) {
                        return null;
                    }

                    $currentPaymentAccount = $paymentAccounts
                        ->where('student_id', $studentEnrollment->student_id)
                        ->where('school_id', $studentEnrollment->classroom->school_id)
                        ->first();

                    if (blank($currentPaymentAccount)) {
                        return null;
                    }

                    return [
                        'student_enrollment_id' => $studentEnrollment->getKey(),
                        'student_payment_account_id' => $currentPaymentAccount->getKey(),

                        'school_name' => $currentPaymentAccount->school->name,
                        'classroom_name' => $studentEnrollment->classroom->name,
                        'school_year_name' => $studentEnrollment->schoolYear->name,
                        'student_name' => $studentEnrollment->student->name,
                        'virtual_account_number' => $currentPaymentAccount->monthly_fee_virtual_account,

                        'type' => InvoiceTypeEnum::MONTHLY_FEE,
                        'month_id' => data_get($data, 'month_id'),
                        'amount' => $currentPaymentAccount->monthly_fee_amount,
                        'total_amount' => $currentPaymentAccount->monthly_fee_amount,

                        'is_paid' => false,
                        'start_date' => data_get($data, 'start_date'),
                        'end_date' => data_get($data, 'end_date'),
                        'description' => 'Tagihan SPP Bulan ' . Month::from(data_get($data, 'month_id'))->name,
                        'is_active' => true,
                    ];

                })->filter()->toArray();

                if (blank($invoice)) {
                    Notification::make()
                        ->title('Tagihan tidak dibuat!')
                        ->body('Semua siswa terpilih sudah memiliki tagihan untuk bulan yang terpilih.')
                        ->info()
                        ->send();

                    return;
                }

                try {
                    DB::transaction(function () use ($invoice, $studentEnrollments, $data) {
                        Invoice::fillAndInsert($invoice);

                        $enrollmentIds = $studentEnrollments->pluck('id')->toArray();

                        Invoice::unpaidMonthlyFee()
                            ->whereIn('student_enrollment_id', $enrollmentIds)
                            ->where('month_id', '!=', data_get($data, 'month_id'))
                            ->update([
                                'start_date' => data_get($data, 'start_date'),
                                'end_date' => data_get($data, 'end_date'),
                            ]);
                    });

                    Notification::make()
                        ->title('Berhasil membuat tagihan!')
                        ->body(count($invoice) . ' tagihan baru telah dibuat dan semua tanggal tagihan yang belum terbayarkan sudah diupdate.')
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
            ->modalDescription(function () {
                /** @var Branch $tenant */
                $tenant = filament()->getTenant();

                $count = StudentEnrollment::whereIn('classroom_id', $tenant->classrooms()->pluck('classrooms.id'))
                    ->active()
                    ->count();

                return "Aksi ini akan membuat tagihan buku untuk {$count} siswa aktif di cabang {$tenant->name}.";
            })
            ->color('success')
            ->mountUsing(function (Action $action, Schema $form) {
                if (blank(SchoolYear::getActive()) || blank(SchoolTerm::getActive())) {

                    Notification::make()
                        ->title('Tidak bisa membuat tagihan!')
                        ->body('Tahun Ajaran/Semester belum diaktifkan oleh administrator.')
                        ->warning()
                        ->send();

                    $action->cancel();
                }

                /** @var Branch $tenant */
                $tenant = filament()->getTenant();

                $activeCount = StudentEnrollment::whereIn('classroom_id', $tenant->classrooms()->pluck('classrooms.id'))
                    ->active()
                    ->count();

                if ($activeCount === 0) {
                    Notification::make()
                        ->title('Tidak bisa membuat tagihan!')
                        ->body('Tidak ada siswa aktif!')
                        ->warning()
                        ->send();
                    $action->cancel();
                }

                $form->fill();
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
