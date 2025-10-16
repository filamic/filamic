<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Classrooms\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
