<?php

namespace App\Filament\Finance\Resources\Students;

use BackedEnum;
use App\Models\Student;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;

use Filament\Support\Icons\Heroicon;
use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use App\Filament\Finance\Resources\Students\Pages\EditStudent;
use App\Filament\Finance\Resources\Students\Pages\ListStudents;
use App\Filament\Finance\Resources\Students\Pages\CreateStudent;
use App\Filament\Finance\Resources\Students\Schemas\StudentForm;
use App\Filament\Finance\Resources\Students\Tables\StudentsTable;
use App\Filament\Admin\Resources\Students\StudentResource as StudentsStudentResource;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-friends';

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
            //
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
