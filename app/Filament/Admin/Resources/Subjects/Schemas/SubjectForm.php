<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Subjects\Schemas;

use App\Models\SubjectCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('subject_category_id')
                            ->label('Category')
                            ->options(fn () => SubjectCategory::with('school')
                                ->orderBy('sort_order')
                                ->get()
                                ->groupBy('school.name')
                                ->map(fn ($categories) => $categories->pluck('name', 'id'))
                            )
                            ->searchable()
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Example: Mathematics')
                            ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('subject_category_id', $get('subject_category_id'))),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->placeholder('Example: 1')
                            ->helperText('The order in which the subject should be displayed'),
                    ]),
            ]);
    }
}
