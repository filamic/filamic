<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolYears\Tables;

use App\Models\SchoolYear;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SchoolYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('semester'),
                ToggleColumn::make('is_active')
                    ->beforeStateUpdated(function (SchoolYear $record, $state) {
                        if ($state) {
                            $record->activateExclusively();
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('name', 'desc');
    }
}
