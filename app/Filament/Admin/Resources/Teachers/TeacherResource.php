<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachers;

use App\Filament\Admin\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Admin\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Admin\Resources\Teachers\Pages\ListTeachers;
use App\Filament\Admin\Resources\Teachers\Pages\ViewTeacher;
use App\Filament\Admin\Resources\Teachers\Schemas\TeacherForm;
use App\Filament\Admin\Resources\Teachers\Schemas\TeacherInfolist;
use App\Filament\Admin\Resources\Teachers\Tables\TeachersTable;
use App\Models\Teacher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static UnitEnum | string | null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 6;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-user';

    public static function table(Table $table): Table
    {
        return TeachersTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return TeacherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TeacherInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeachers::route('/'),
            'create' => CreateTeacher::route('/create'),
            'view' => ViewTeacher::route('/{record}'),
            'edit' => EditTeacher::route('/{record}/edit'),
        ];
    }
}
