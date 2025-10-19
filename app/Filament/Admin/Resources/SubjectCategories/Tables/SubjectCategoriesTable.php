<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubjectCategories\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubjectCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('School')
                    ->columnSpanFull()
                    ->relationship('school', 'name'),
            ])
            ->filtersLayout(FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
