<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolYears;

use App\Filament\Admin\Resources\SchoolYears\Pages\CreateSchoolYear;
use App\Filament\Admin\Resources\SchoolYears\Pages\EditSchoolYear;
use App\Filament\Admin\Resources\SchoolYears\Pages\ListSchoolYears;
use App\Filament\Admin\Resources\SchoolYears\Pages\ViewSchoolYear;
use App\Filament\Admin\Resources\SchoolYears\Schemas\SchoolYearForm;
use App\Filament\Admin\Resources\SchoolYears\Schemas\SchoolYearInfolist;
use App\Filament\Admin\Resources\SchoolYears\Tables\SchoolYearsTable;
use App\Models\SchoolYear;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SchoolYearResource extends Resource
{
    protected static ?string $model = SchoolYear::class;

    protected static UnitEnum | string | null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 3;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-calendar-event';

    public static function table(Table $table): Table
    {
        return SchoolYearsTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return SchoolYearForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SchoolYearInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchoolYears::route('/'),
            'create' => CreateSchoolYear::route('/create'),
            'view' => ViewSchoolYear::route('/{record}'),
            'edit' => EditSchoolYear::route('/{record}/edit'),
        ];
    }
}
