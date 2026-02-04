<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\RelationManagers;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\Invoice;
use Carbon\Month;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MonthlyFeeInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    // public function form(Schema $schema): Schema
    // {
    //     return $schema
    //         ->components([
    //             Select::make('branch_id')
    //                 ->relationship('branch', 'name')
    //                 ->required(),
    //             Select::make('school_id')
    //                 ->relationship('school', 'name')
    //                 ->required(),
    //             Select::make('classroom_id')
    //                 ->relationship('classroom', 'name')
    //                 ->required(),
    //             Select::make('school_year_id')
    //                 ->relationship('schoolYear', 'name')
    //                 ->required(),
    //             Select::make('school_term_id')
    //                 ->relationship('schoolTerm', 'name')
    //                 ->required(),
    //             TextInput::make('reference_number')
    //                 ->required(),
    //             TextInput::make('fingerprint')
    //                 ->required(),
    //             TextInput::make('branch_name')
    //                 ->required(),
    //             TextInput::make('school_name')
    //                 ->required(),
    //             TextInput::make('classroom_name')
    //                 ->required(),
    //             TextInput::make('school_year_name')
    //                 ->required(),
    //             TextInput::make('school_term_name')
    //                 ->required(),
    //             TextInput::make('student_name')
    //                 ->required(),
    //             Select::make('type')
    //                 ->options(InvoiceTypeEnum::class)
    //                 ->required(),
    //             Select::make('month_id')
    //                 ->options(Month::class),
    //             TextInput::make('amount')
    //                 ->required()
    //                 ->numeric(),
    //             TextInput::make('discount')
    //                 ->required()
    //                 ->numeric()
    //                 ->default(0.0),
    //             TextInput::make('fine')
    //                 ->required()
    //                 ->numeric()
    //                 ->default(0.0),
    //             TextInput::make('total_amount')
    //                 ->required()
    //                 ->numeric(),
    //             Select::make('status')
    //                 ->options(InvoiceStatusEnum::class)
    //                 ->default(1)
    //                 ->required(),
    //             Select::make('payment_method')
    //                 ->options(PaymentMethodEnum::class),
    //             DateTimePicker::make('paid_at'),
    //             DatePicker::make('due_date')
    //                 ->required(),
    //             DatePicker::make('issued_at')
    //                 ->required(),
    //             Textarea::make('description')
    //                 ->columnSpanFull(),
    //         ]);
    // }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                /** @var Builder|Invoice $query */
                $query->monthlyFee();
            })
            ->heading('Uang Sekolah')
            ->recordTitleAttribute('id')
            ->columns([
                // TextColumn::make('branch.name')
                //     ->searchable(),
                // TextColumn::make('school.name')
                //     ->searchable(),
                // TextColumn::make('classroom.name')
                //     ->searchable(),
                // TextColumn::make('schoolYear.name')
                //     ->searchable(),
                // TextColumn::make('schoolTerm.name')
                //     ->searchable(),
                TextColumn::make('reference_number'),
                // TextColumn::make('fingerprint')
                //     ->searchable(),
                // TextColumn::make('branch_name')
                //     ->searchable(),
                // TextColumn::make('school_name')
                //     ->searchable(),
                // TextColumn::make('classroom_name')
                //     ->searchable(),
                // TextColumn::make('school_year_name')
                //     ->searchable(),
                // TextColumn::make('schoolTerm.name')
                //     ->searchable(),
                // TextColumn::make('student_name')
                //     ->searchable(),
                // TextColumn::make('type')
                //     ->badge()
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('month_id.name')
                    ->label('Bulan'),
                // TextColumn::make('amount')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('discount')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('fine')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('total_amount')
                    ->numeric(),
                TextColumn::make('issued_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('payment_method'),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ]);
    }
}
