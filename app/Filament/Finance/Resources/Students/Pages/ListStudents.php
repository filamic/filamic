<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\Pages;

use App\Actions\GenerateMonthlyFeeInvoice;
use App\Enums\MonthEnum;
use App\Filament\Finance\Resources\Students\StudentResource;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
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

        $currentMonth = MonthEnum::from(now()->month)->getLabel();

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
                    Select::make('month')
                        ->options(function () {
                            $currentTerm = SchoolTerm::getActive();
                            $allowedMonths = $currentTerm->getAllowedMonths();

                            return collect(MonthEnum::filterBySemester($allowedMonths))
                                ->mapWithKeys(fn ($month) => [$month->value => $month->getLabel()])
                                ->toArray();
                        })
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
                        ->after('issued_at')
                        ->default(now()->addMonth()->day(20)),
                ])->columns(2),
            ])
            ->action(function (array $data) {
                try {
                    $generateMonthlyFeeInvoice = GenerateMonthlyFeeInvoice::run(
                        filament()->getTenant(),
                        $data
                    );

                    if ($generateMonthlyFeeInvoice === 0) {
                        Notification::make()
                            ->title('Tagihan tidak dibuat!')
                            ->body('Tidak ada siswa yang memenuhi syarat pembuatan tagihan.')
                            ->info()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Berhasil membuat tagihan!')
                        ->body("{$generateMonthlyFeeInvoice} tagihan baru dibuat.")
                        ->success()
                        ->send();

                } catch (\Illuminate\Database\QueryException $error) {
                    if ($error->getCode() === '23000' && str_contains($error->getMessage(), 'fingerprint')) {
                        Notification::make()
                            ->title('Invoice sudah dibuat sebelumnya!')
                            ->warning()
                            ->send();

                        return;
                    }

                    throw $error;
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

    public function getTabs(): array
    {
        return [
            'Aktif' => Tab::make()
                ->modifyQueryUsing(fn (Builder | Student $query) => $query->active())
                ->icon('tabler-user-check'),
            'Tidak Aktif' => Tab::make()
                ->modifyQueryUsing(fn (Builder | Student $query) => $query->inActive())
                ->icon('tabler-user-x'),
        ];
    }
}
