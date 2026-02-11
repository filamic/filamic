<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\Tables;

use App\Actions\PayMonthlyFeeInvoice;
use App\Actions\PrintMonthlyFeeInvoice;
use App\Enums\PaymentMethodEnum;
use App\Models\Invoice;
use App\Models\SchoolYear;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Size;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Throwable;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['currentPaymentAccount', 'unpaidMonthlyFee']);
            })
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('currentPaymentAccount')
                    ->label('Nomor VA')
                    ->markdown()
                    ->formatStateUsing(function ($record) {
                        $account = $record->currentPaymentAccount;

                        return "**SPP:** {$account->monthly_fee_virtual_account}  \n**Buku:** {$account->book_fee_virtual_account}";
                    }),
                TextColumn::make('unpaidMonthlyFee.school_year_name')
                    ->label('TA')
                    ->listWithLineBreaks(),
                TextColumn::make('unpaidMonthlyFee.month')
                    ->label('Bulan')
                    ->listWithLineBreaks(),
                TextColumn::make('unpaidMonthlyFee.total_amount')
                    ->label('Tagihan')
                    ->listWithLineBreaks()
                    ->money('IDR'),
                TextColumn::make('unpaid_monthly_fee_sum_total_amount')
                    ->sum('unpaidMonthlyFee', 'total_amount')
                    ->money('IDR')
                    ->label('Total')
                    ->sortable(),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->size(Size::ExtraSmall)
                    ->tooltip('Ubah'),
                ActionGroup::make([
                    Action::make('payMonthlyFeeAction')
                        ->label('Uang Sekolah')
                        ->visible(fn (Student $record) => $record->hasUnpaidMonthlyFee())
                        ->modalHeading('Bayar Tagihan Uang Sekolah')
                        ->schema([
                            TextInput::make('total_invoice')
                                ->label('Total Tagihan')
                                ->disabled()
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->prefix('Rp'),
                            DateTimePicker::make('paid_at')
                                ->required()
                                ->maxDate(now())
                                ->label('Tanggal Bayar')
                                ->hint('Tanggal Sesuai Rekening Koran/Tanggal Saat Bayar')
                                ->default(date('Y-m-d H:i:s')),
                            CheckboxList::make('invoice_ids')
                                ->label('Tagihan Berdasarkan Bulan')
                                ->required()
                                ->bulkToggleable()
                                ->options(function (Student $record) {
                                    /** @var Builder|Invoice $query */
                                    // @phpstan-ignore-next-line
                                    $query = $record->invoices();

                                    return $query
                                        ->unpaidMonthlyFee()
                                        ->orderBy('due_date')
                                        ->get()
                                        ->mapWithKeys(fn (Invoice $invoice) => [
                                            $invoice->getKey() => $invoice->month->getLabel(),
                                        ]);
                                })
                                ->columns(6)
                                ->descriptions(function (Student $record) {
                                    /** @var Builder|Invoice $query */
                                    // @phpstan-ignore-next-line
                                    $query = $record->invoices();

                                    return $query
                                        ->unpaidMonthlyFee()
                                        ->orderBy('month')
                                        ->get()
                                        ->pluck('formatted_amount', 'id');
                                })
                                ->gridDirection('row')
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?array $state, Student $record) {
                                    $totalAmount = Number::format($record->invoices
                                        ->whereIn('id', $state)
                                        ->sum('amount'), locale: config('app.locale'));

                                    $set('total_invoice', $totalAmount);
                                }),
                            Group::make([
                                TextInput::make('fine')
                                    ->label('Denda')
                                    ->default(fn (Student $record) => Invoice::calculateAccumulatedFine($record))
                                    ->numeric()
                                    ->readOnly()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->prefix('Rp')
                                    ->minValue(0),
                                TextInput::make('discount')
                                    ->label('Diskon')
                                    ->minValue(0)
                                    ->numeric()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->prefix('Rp')
                                    ->default(0),
                            ])->columns(2),
                            Select::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->required()
                                ->options(PaymentMethodEnum::class),
                            Textarea::make('description')
                                ->label('Keterangan')
                                ->hint('Deskripsi Ini Akan Di Buat Untuk Semua Tagihan Yang Terpilih.'),
                        ])
                        ->action(function (Student $record, array $data) {
                            try {
                                $payMonthlyFeeInvoice = PayMonthlyFeeInvoice::run($record, $data);

                                if ($payMonthlyFeeInvoice === false) {
                                    Notification::make()
                                        ->title('Tagihan tidak dibayarkan!')
                                        ->info()
                                        ->send();

                                    return;
                                }

                                Notification::make()
                                    ->title('Berhasil membayar tagihan!')
                                    ->success()
                                    ->send();

                            } catch (Throwable $error) {
                                report($error);

                                Notification::make()
                                    ->title('Gagal membayar tagihan!')
                                    ->body('Terjadi Kesalahan Sistem. Silakan hubungi tim IT.')
                                    ->danger()
                                    ->persistent()
                                    ->send();

                                return;
                            }
                        }),
                ])
                    ->iconButton()
                    ->size(Size::ExtraSmall)
                    ->tooltip('Bayar')
                    ->color('success')
                    ->icon('tabler-invoice'),
                ActionGroup::make([
                    Action::make('printInvoice')
                        ->visible(fn (Student $record) => $record->hasPaidMonthlyFee())
                        ->requiresConfirmation()
                        ->label('Uang Sekolah')
                        ->schema([
                            Select::make('school_year_id')
                                ->label('Tahun Ajaran')
                                ->live()
                                ->options(fn () => SchoolYear::pluck('name', 'id'))
                                ->default(fn () => SchoolYear::getActive()->getKey()),
                            CheckboxList::make('invoice_ids')
                                ->label('Tagihan')
                                ->live()
                                ->bulkToggleable()
                                ->required()
                                ->options(fn (Student $record, Get $get): array => $record
                                    ->paidMonthlyFee()
                                    ->where('school_year_id', $get('school_year_id'))
                                    ->orderBy('month')
                                    ->get()
                                    ->mapWithKeys(fn ($invoice): array => [
                                        (string) $invoice->getKey() => $invoice->month->getLabel(),
                                    ])
                                    ->all()
                                )
                                ->columns(3)
                                ->gridDirection('row'),
                        ])
                        ->action(function (Student $record, array $data) {
                            try {
                                $printMonthlyFeeInvoice = PrintMonthlyFeeInvoice::run(
                                    $record,
                                    $data
                                );

                                if (blank($printMonthlyFeeInvoice)) {
                                    Notification::make()
                                        ->title('Gagal membuat pdf tagihan!')
                                        ->info()
                                        ->send();

                                    return;
                                }

                                Notification::make()
                                    ->title('Berhasil membuat pdf tagihan!')
                                    ->success()
                                    ->actions([
                                        Action::make('view')
                                            ->label('Klik disini untuk lihat')
                                            ->url(asset('storage/' . $printMonthlyFeeInvoice))
                                            ->link()
                                            ->openUrlInNewTab(),
                                    ])
                                    ->send();

                            } catch (Throwable $error) {
                                report($error);

                                Notification::make()
                                    ->title('Gagal membuat pdf invoice, tidak ada data!')
                                    ->body('Terjadi Kesalahan Sistem. Silakan hubungi tim IT.')
                                    ->danger()
                                    ->persistent()
                                    ->send();

                                return;
                            }
                        }),
                ])
                    ->iconButton()
                    ->size(Size::ExtraSmall)
                    ->tooltip('Print')
                    ->icon('tabler-printer'),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
