<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Subjects\Tables;

use App\Models\Subject;
use App\Models\SubjectCategory;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (Subject $record): string => $record->category->name),
                TextColumn::make('school.name')
                    ->label('School')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereRelation('school', 'schools.name', 'like', "%{$search}%")),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('subject_category_id')
                    ->label('Category')
                    ->columnSpanFull()
                    ->options(fn () => SubjectCategory::with('school')
                        ->orderBy('sort_order')
                        ->get()
                        ->groupBy('school.name')
                        ->map(fn ($categories) => $categories->pluck('name', 'id'))
                    )
                    ->searchable(),
            ])
            ->filtersLayout(FiltersLayout::Modal)
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
