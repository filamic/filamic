<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Students\Schemas;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Enums\StatusInFamilyEnum;
use App\Enums\StudentStatusEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->required()
                        ->placeholder('Example: John Doe'),
                    // Select::make('user_id')
                    //     ->relationship('user', 'name'),
                    // Select::make('father_id')
                    //     ->relationship('father', 'name'),
                    // Select::make('mother_id')
                    //     ->relationship('mother', 'name'),
                    // Select::make('guardian_id')
                    //     ->relationship('guardian', 'name'),
                    // TextInput::make('nisn'),
                    // TextInput::make('nis'),
                    ToggleButtons::make('gender')
                        ->options(GenderEnum::class)
                        ->required()
                        ->inline(),
                    // TextInput::make('birth_place'),
                    // DatePicker::make('birth_date'),
                    // TextInput::make('previous_education')
                    //     ->placeholder('Example: SDS Kasih Sayang'),
                    // TextInput::make('joined_at_class')
                    //     ->placeholder('Example: VII (Joshua 1)'),
                    // TextInput::make('sibling_order_in_family')
                    //     ->numeric(),
                    // Select::make('status_in_family')
                    //     ->options(StatusInFamilyEnum::class),
                    // Select::make('religion')
                    //     ->options(ReligionEnum::class),
                    // ToggleButtons::make('status')
                    //     ->options(StudentStatusEnum::class)
                    //     ->required()
                    //     ->inline()
                    //     ->columnSpanFull(),
                    Textarea::make('notes')
                        ->columnSpanFull(),
                    // TextInput::make('metadata'),
                ]),
            ]);
    }
}
