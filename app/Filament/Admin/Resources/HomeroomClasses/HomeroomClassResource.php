<?php

namespace App\Filament\Admin\Resources\HomeroomClasses;

use UnitEnum;
use BackedEnum;
use App\Models\School;
use App\Models\Classroom;
use App\Models\SchoolYear;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\HomeroomClass;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use App\Filament\Admin\Resources\HomeroomClasses\Pages\ManageHomeroomClasses;

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
                    ->options(fn(Get $get) => Classroom::where('school_id',$get('school_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->unique(
                        table: 'homeroom_classes',
                        column: 'school_year_id',
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('school_year_id',$get('school_year_id'))
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
