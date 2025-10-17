<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolYears\Schemas;

use Closure;
use App\Models\SchoolYear;
use App\Enums\SemesterEnum;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Operation;
use Filament\Forms\Components\Checkbox;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;

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
                            ->placeholder('Example: 2024/2025'),
                        Select::make('semester')
                            ->required()
                            ->options(SemesterEnum::class)
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                                return $rule->where('name', $get('name'));
                            }),
                        DatePicker::make('start_date')
                            ->label('Start Date'),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->after('start_date'),
                        Checkbox::make('is_active')
                            ->label('Active')
                            ->helperText('Only one school year should be active at a time')
                            ->hiddenOn(Operation::Edit)
                    ]),
            ]);
    }
}
