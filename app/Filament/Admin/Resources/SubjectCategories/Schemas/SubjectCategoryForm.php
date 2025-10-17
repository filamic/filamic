<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubjectCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class SubjectCategoryForm
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
                            ->placeholder('Example: Group 1')
                            ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('school_id', $get('school_id'))),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->placeholder('Example: 1')
                            ->helperText('The order in which the category should be displayed'),
                    ]),
            ]);
    }
}
