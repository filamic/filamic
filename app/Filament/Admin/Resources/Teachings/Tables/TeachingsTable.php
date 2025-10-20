<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachings\Tables;

use App\Models\SchoolYear;
use App\Models\Teaching;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeachingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.name')
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->searchable(),
                TextColumn::make('classroom.name')
                    ->searchable(),
                TextColumn::make('classroom.school.name')
                    ->searchable(),
                TextColumn::make('schoolYear.name_with_semester')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name'),

                SelectFilter::make('school_year_id')
                    ->label('School Year')
                    ->relationship('schoolYear', 'name')
                    ->getOptionLabelFromRecordUsing(fn (SchoolYear $record) => "{$record->name_with_semester}")
                    ->default(SchoolYear::active()->first()?->getRouteKey()),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->disabled(fn (Teaching $record) => ! $record->canDelete()),
            ]);
    }
}
