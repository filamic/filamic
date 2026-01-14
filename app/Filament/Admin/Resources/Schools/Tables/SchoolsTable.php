<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Schools\Tables;

use App\Models\School;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (School $school) => $school->formatted_npsn),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
