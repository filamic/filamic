<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolTerms;

use App\Filament\Admin\Resources\SchoolTerms\Pages\ManageSchoolTerms;
use App\Models\SchoolTerm;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class SchoolTermResource extends Resource
{
    protected static ?string $model = SchoolTerm::class;

    protected static UnitEnum | string | null $navigationGroup = 'Academic Periods';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('name'),
                ToggleColumn::make('is_active')
                    ->beforeStateUpdated(function (SchoolTerm $record, $state) {
                        if ($state) {
                            $record->activateExclusively();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchoolTerms::route('/'),
        ];
    }
}
