<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubjectCategories;

use App\Filament\Admin\Resources\SubjectCategories\Pages\CreateSubjectCategory;
use App\Filament\Admin\Resources\SubjectCategories\Pages\EditSubjectCategory;
use App\Filament\Admin\Resources\SubjectCategories\Pages\ListSubjectCategories;
use App\Filament\Admin\Resources\SubjectCategories\Pages\ViewSubjectCategory;
use App\Filament\Admin\Resources\SubjectCategories\Schemas\SubjectCategoryForm;
use App\Filament\Admin\Resources\SubjectCategories\Schemas\SubjectCategoryInfolist;
use App\Filament\Admin\Resources\SubjectCategories\Tables\SubjectCategoriesTable;
use App\Models\SubjectCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SubjectCategoryResource extends Resource
{
    protected static ?string $model = SubjectCategory::class;

    protected static UnitEnum | string | null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 3;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-category';

    public static function table(Table $table): Table
    {
        return SubjectCategoriesTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return SubjectCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubjectCategoryInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubjectCategories::route('/'),
            'create' => CreateSubjectCategory::route('/create'),
            'view' => ViewSubjectCategory::route('/{record}'),
            'edit' => EditSubjectCategory::route('/{record}/edit'),
        ];
    }
}
