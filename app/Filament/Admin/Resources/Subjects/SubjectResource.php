<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Subjects;

use App\Filament\Admin\Resources\Subjects\Pages\CreateSubject;
use App\Filament\Admin\Resources\Subjects\Pages\EditSubject;
use App\Filament\Admin\Resources\Subjects\Pages\ListSubjects;
use App\Filament\Admin\Resources\Subjects\Pages\ViewSubject;
use App\Filament\Admin\Resources\Subjects\Schemas\SubjectForm;
use App\Filament\Admin\Resources\Subjects\Schemas\SubjectInfolist;
use App\Filament\Admin\Resources\Subjects\Tables\SubjectsTable;
use App\Models\Subject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static UnitEnum | string | null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 4;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-book';

    public static function table(Table $table): Table
    {
        return SubjectsTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return SubjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubjectInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubjects::route('/'),
            'create' => CreateSubject::route('/create'),
            'view' => ViewSubject::route('/{record}'),
            'edit' => EditSubject::route('/{record}/edit'),
        ];
    }
}
