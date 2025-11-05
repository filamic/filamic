<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HomeroomClasses;

use App\Filament\Admin\Resources\HomeroomClasses\Pages\ManageHomeroomClasses;
use App\Models\Classroom;
use App\Models\HomeroomClass;
use App\Models\School;
use App\Models\SchoolYear;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use UnitEnum;

class HomeroomClassResource extends Resource
{
    protected static ?string $model = HomeroomClass::class;

    protected static UnitEnum | string | null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 8;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-star';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('school_year_id')
                    ->relationship('schoolYear')
                    ->getOptionLabelFromRecordUsing(fn (SchoolYear $record) => "{$record->name_with_semester}")
                    ->searchable()
                    ->preload()
                    ->live()
                    ->default(fn ($state) => $state ?? SchoolYear::active()->first()?->getRouteKey())
                    ->required(),

                Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->required(),

                Select::make('school_id')
                    ->label('School')
                    ->options(School::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->default(fn ($state) => $state)
                    ->dehydrated(false)
                    ->required(),

                Select::make('classroom_id')
                    ->label('Classroom')
                    ->disabled(fn (Get $get) => blank($get('teacher_id')))
                    ->options(fn (Get $get) => Classroom::where('school_id', $get('school_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->unique(
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('school_year_id', $get('school_year_id'))
                    ),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.name')
                    ->searchable(),
                TextColumn::make('classroom.name')
                    ->searchable(),
                TextColumn::make('schoolYear.name_with_semester'),
            ])
            ->filters([
                SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'name'),

                SelectFilter::make('classroom_id')
                    ->label('Classroom')
                    ->relationship('classroom', 'name'),

                SelectFilter::make('school_year_id')
                    ->label('School Year')
                    ->relationship('schoolYear', 'name')
                    ->getOptionLabelFromRecordUsing(fn (SchoolYear $record) => "{$record->name_with_semester}")
                    ->default(SchoolYear::active()->first()?->getRouteKey()),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->disabled(fn (HomeroomClass $record) => ! $record->canDelete()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (HomeroomClass $record): bool => $record->canDelete(),
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHomeroomClasses::route('/'),
        ];
    }
}
