<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachings\Tables;

use App\Models\SchoolYear;
use App\Models\Teaching;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
                TextColumn::make('schoolYear.name_with_semester'),
            ])
            ->filters([
                SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'name'),

                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name'),

                SelectFilter::make('classroom_id')
                    ->label('Classroom')
                    ->relationship('classroom', 'name'),

                SelectFilter::make('school_year_id')
                    ->label('School Year')
                    ->relationship('schoolYear', 'name')
                    ->getOptionLabelFromRecordUsing(fn (SchoolYear $record) => "{$record->name_with_semester}")
                    ->default(SchoolYear::active()->first()?->getRouteKey()),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->disabled(fn (Teaching $record) => ! $record->canDelete()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Teaching $record): bool => $record->canDelete(),
            );
    }
}
