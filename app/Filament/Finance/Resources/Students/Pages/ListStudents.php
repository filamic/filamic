<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\Pages;

use Carbon\Month;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Enums\InvoiceTypeEnum;
use App\Models\StudentEnrollment;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\DB;
use Filament\View\PanelsRenderHook;
use App\Models\StudentPaymentAccount;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\RenderHook;
use Illuminate\Auth\Middleware\Authenticate;
use Filament\Schemas\Components\EmbeddedTable;
use App\Filament\Finance\Resources\Students\StudentResource;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('createMonthlyInvoice')
                ->requiresConfirmation()
                ->modalIcon('tabler-invoice')
                ->modalHeading('Buat Tagihan SPP')
                ->modalDescription('Semua siswa aktif tahun ini')
                ->color('success')
                ->mountUsing(function (Action $action, Schema $form) {
                    if(empty(SchoolYear::getActive()) || empty(SchoolTerm::getActive())){

                        Notification::make()
                            ->title('Tahun Ajaran/Semester aktif belum di-set!')
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
                    ])->columns(2)
                ])
                ->action(function(array $data, Action $action,){

                    $classroomIds = filament()
                        ->getTenant()
                        ->classrooms
                        ->pluck('id')->toArray();

                    $studentEnrollments = StudentEnrollment::with('student','classroom','classroom.school','schoolYear')
                        ->active()
                        ->whereIn('classroom_id', $classroomIds)
                        ->get();

                    if($studentEnrollments->isEmpty()){

                        Notification::make()
                            ->title('Gagal Membuat Tagihan!')
                            ->body('Tidak ada siswa aktif.')
                            ->warning()
                            ->send();

                        $action->cancel();
                    }

                    $paymentAccounts = StudentPaymentAccount::whereIn(
                            'school_id', $studentEnrollments->pluck('classroom.school_id')->toArray()
                        )
                        ->with('student','school')
                        ->activeMonthlyFee()
                        ->get();

                    if($paymentAccounts->isEmpty()){
                        Notification::make()
                            ->title('Gagal Membuat Tagihan!')
                            ->body('Tidak ada payment account yang aktif.')
                            ->warning()
                            ->send();

                        $action->cancel();
                    }

                    $existingInvoiceEnrollmentIds = Invoice::query()
                        ->whereIn('student_enrollment_id', $studentEnrollments->pluck('id'))
                        ->where('type', InvoiceTypeEnum::MONTHLY_FEE)
                        ->where('month_id', $data['month_id'])
                        ->pluck('student_enrollment_id')
                        ->toArray();

                    $invoice = $studentEnrollments->map(function(StudentEnrollment $studentEnrollment) use($data, $paymentAccounts, $existingInvoiceEnrollmentIds){

                        if (in_array($studentEnrollment->getKey(), $existingInvoiceEnrollmentIds)) {
                            return null;
                        }

                        $currentPaymentAccount = $paymentAccounts->where('student_id', $studentEnrollment->student_id)->first();

                        if(empty($currentPaymentAccount)){
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
                            'description' => "Tagihan SPP Bulan " . Month::from(data_get($data, 'month_id'))->name,
                            'is_active' => true,
                        ];

                    })->filter()->toArray();

                    if (empty($invoice)) {
                        Notification::make()
                            ->title('Info')
                            ->body('Semua siswa terpilih sudah memiliki tagihan untuk bulan ini.')
                            ->info()
                            ->send();
                        return;
                    }

                    DB::transaction(function () use ($invoice) {
                        // Pake insert atau fillAndInsert sesuai helper lu
                        Invoice::fillAndInsert($invoice);
                    });
                
                    Notification::make()
                        ->title('Berhasil!')
                        ->body(count($invoice) . ' tagihan baru telah dibuat.')
                        ->success()
                        ->send();  
                })
        ];
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
                'desc' => 'Menampilkan siswa yang sudah lulus, pindah keluar, atau keluar.',
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
