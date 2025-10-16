<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Classrooms\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('school_id')
                            ->relationship('school', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Example: Matthew 1'),
                        Select::make('grade')
                            ->options(array_map('strval', range(0, 12))),
                        TextInput::make('phase')
                            ->placeholder('Example: A|B|C|D'),
                        Checkbox::make('is_moving_class')
                            ->label('Is Moving Class'),
                    ]),
            ]);
    }
}
