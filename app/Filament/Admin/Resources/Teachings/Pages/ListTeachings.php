<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachings\Pages;

use App\Filament\Admin\Resources\Teachings\TeachingResource;
use App\Models\Classroom;
use App\Models\School;
use App\Models\SchoolYear;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;

class ListTeachings extends ListRecords
{
    protected static string $resource = TeachingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_teaching')
                ->modalHeading('Teaching Assignment')
                ->modalDescription('Assign teachers to subjects and classrooms for the selected school year')
                ->modalIcon(TeachingResource::getNavigationIcon())
                ->schema(self::make())
                ->action(function (array $data) {
                    $baseData = [
                        'school_year_id' => data_get($data, 'school_year_id'),
                        'teacher_id' => data_get($data, 'teacher_id'),
                    ];

                    $teachings = collect($data['teachings'])
                        ->flatMap(fn ($teaching) => collect($teaching['classroom_id'])
                            ->map(fn ($classroomId) => [
                                ...$baseData,
                                'subject_id' => $teaching['subject_id'],
                                'classroom_id' => $classroomId,
                            ])
                        )
                        ->toArray();

                    static::getModel()::fillAndInsert($teachings);

                    Notification::make()
                        ->title('Teaching assignments created successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getSubheading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'Teaching assignments you have access to are listed below.';
    }

    public static function make(): array
    {
        return [
            Group::make([
                Select::make('school_year_id')
                    ->relationship('schoolYear')
                    ->getOptionLabelFromRecordUsing(fn (SchoolYear $record) => "{$record->name_with_semester}")
                    ->searchable()
                    ->preload()
                    ->default(fn ($state) => $state ?? SchoolYear::active()->first()?->getRouteKey())
                    ->required(),
                Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn ($state) => $state)
                    ->required(),
                Select::make('school_id')
                    ->label('School')
                    ->options(School::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(function (Set $set) {
                        $set('teachings', [
                            ['subject_id' => null, 'classroom_id' => null],
                        ]);
                    })
                    ->live()
                    ->default(fn ($state) => $state)
                    ->dehydrated(false)
                    ->required(),
                Repeater::make('teachings')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->reorderable(false)
                    ->table([
                        TableColumn::make('Subject')->markAsRequired(),
                        TableColumn::make('Classroom')->markAsRequired(),
                    ])
                    ->compact()
                    ->schema([
                        Select::make('subject_id')
                            ->relationship(
                                name: 'subject',
                                titleAttribute: 'name',
                                modifyQueryUsing: self::modifyQueryUsing(...))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        Select::make('classroom_id')
                            ->options(fn (Get $get) => self::modifyQueryUsing(Classroom::query(), $get)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->multiple()
                            ->required(),
                    ]),
            ])
                ->columns(3),

        ];
    }

    private static function modifyQueryUsing(Builder $query, Get $get): Builder
    {
        return $query->whereRelation('school', 'schools.id', $get('../../school_id'));
    }
}
