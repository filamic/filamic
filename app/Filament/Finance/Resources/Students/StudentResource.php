<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students;

use App\Filament\Finance\Resources\Students\Pages\CreateStudent;
use App\Filament\Finance\Resources\Students\Pages\EditStudent;
use App\Filament\Finance\Resources\Students\Pages\ListStudents;
use App\Filament\Finance\Resources\Students\Schemas\StudentForm;
use App\Filament\Finance\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-friends';

    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return 'Peserta Didik';
    }

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
