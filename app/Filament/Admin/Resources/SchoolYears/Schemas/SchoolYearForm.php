<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolYears\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Example: 2024/2025')
                            ->columnSpanFull(),
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
