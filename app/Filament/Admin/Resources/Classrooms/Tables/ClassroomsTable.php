<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Classrooms\Tables;

use App\Models\Classroom;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClassroomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('grade')
                    ->sortable(),
                TextColumn::make('phase'),
                IconColumn::make('is_moving_class')
                    ->label('Moving Class')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name'),

                SelectFilter::make('grade')
                    ->label('Grade')
                    ->options(Classroom::getGrades()),

                SelectFilter::make('is_moving_class')
                    ->label('Moving Class')
                    ->options([
                        true => 'Yes',
                        false => 'No',
                    ]),
            ])
            ->filtersLayout(FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
