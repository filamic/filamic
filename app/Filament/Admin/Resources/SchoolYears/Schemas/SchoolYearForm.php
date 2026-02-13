<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolYears\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class SchoolYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('start_year')
                            ->numeric()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->disabledOn(Operation::Edit)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('end_year', (int) $state + 1);
                                $set('start_date', "{$state}-07-01");
                                $set('end_date', ((int) $state + 1) . '-06-30');
                            }),
                        TextInput::make('end_year')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->hint('Auto generate based on the start year'),
                        DatePicker::make('start_date')
                            ->label('Start Date'),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->after('start_date'),
                        Checkbox::make('is_active')
                            ->label('Active')
                            ->helperText('Only one school year should be active at a time')
                            ->hiddenOn(Operation::Edit),
                    ]),
            ]);
    }
}
