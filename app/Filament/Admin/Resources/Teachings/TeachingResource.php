<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachings;

use App\Filament\Admin\Resources\Teachings\Pages\ListTeachings;
use App\Filament\Admin\Resources\Teachings\Tables\TeachingsTable;
use App\Models\Teaching;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

class TeachingResource extends Resource
{
    protected static ?string $model = Teaching::class;

    protected static UnitEnum | string | null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 7;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-chalkboard';

    public static function table(Table $table): Table
    {
        return TeachingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeachings::route('/'),
        ];
    }
}
