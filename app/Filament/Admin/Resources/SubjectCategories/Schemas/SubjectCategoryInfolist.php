<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubjectCategories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubjectCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('school.name')
                            ->label('School'),
                        TextEntry::make('name'),
                        TextEntry::make('sort_order'),
                    ]),
            ]);
    }
}
