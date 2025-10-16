<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Classrooms\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassroomInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('school.name')
                            ->label('School'),
                        TextEntry::make('grade'),
                        TextEntry::make('phase'),
                        IconEntry::make('is_moving_class')
                            ->label('Is Moving Class')
                            ->boolean(),
                    ]),
            ]);
    }
}
