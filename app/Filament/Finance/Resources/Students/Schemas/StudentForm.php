<?php

namespace App\Filament\Finance\Resources\Students\Schemas;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use Filament\Support\RawJs;
use Filament\Schemas\Schema;
use App\Enums\StudentStatusEnum;
use App\Enums\StatusInFamilyEnum;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\ToggleButtons;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Student\'s Detail')
                            ->schema([
                                Section::make([
                                    TextInput::make('name')
                                        ->required()
                                        ->placeholder('Example: John Doe'),
                                    ToggleButtons::make('gender')
                                        ->options(GenderEnum::class)
                                        ->required()
                                        ->inline(),
                                    ToggleButtons::make('status')
                                        ->options(StudentStatusEnum::class)
                                        ->required()
                                        ->inline()
                                        ->columnSpanFull(),
                                    Textarea::make('notes')
                                        ->columnSpanFull(),
                                ])
                            ])
                            ->icon('tabler-list-details'),
                        Tab::make('Payment Detail')
                            ->schema([
                                Repeater::make('paymentAccounts')
                                    ->deletable(false)
                                    ->addable(false)
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->relationship('paymentAccounts')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('school_id')
                                            ->relationship('school', 'name')
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('monthly_fee_amount')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->placeholder('0')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->minValue(0)
                                            ->required(),
                                        TextInput::make('book_fee_amount')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->placeholder('0')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->minValue(0)
                                            ->required(),
                                        TextInput::make('monthly_fee_virtual_account')
                                            ->placeholder('Example: 103023001')
                                            ->prefixIcon('tabler-credit-card')
                                            ->numeric()
                                            ->minLength(5)
                                            ->maxLength(20)
                                            ->unique(ignoreRecord: true)
                                            ->different('book_fee_virtual_account'),
                                        TextInput::make('book_fee_virtual_account')
                                            ->placeholder('Example: 103023001')
                                            ->prefixIcon('tabler-book')
                                            ->numeric()
                                            ->minLength(5)
                                            ->maxLength(20)
                                            ->unique(ignoreRecord: true)
                                            ->different('monthly_fee_virtual_account'),
                                    ])
                                
                            ])
                            ->icon('tabler-wallet'),
                        Tab::make('Classroom')
                            ->schema([
                                // ...
                            ])
                            ->icon('tabler-door'),
                    ])
                    ->contained(false)
                
            ]);
    }
}
