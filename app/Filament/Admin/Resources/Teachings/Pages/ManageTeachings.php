<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachings\Pages;

use App\Filament\Admin\Resources\Teachings\TeachingResource;
use App\Models\Classroom;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Teaching;
use Closure;
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

class ManageTeachings extends ListRecords
{
    protected static string $resource = TeachingResource::class;

    public function getSubheading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'Teaching assignments you have access to are listed below.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new_teaching')
                ->modalHeading('Teaching Assignment')
                ->modalDescription('Assign teachers to subjects and classrooms for the selected school year')
                ->modalIcon(TeachingResource::getNavigationIcon())
                ->schema(self::getForm())
                ->action(function (array $data) {
                    $commonTeachingData = [
                        'school_year_id' => data_get($data, 'school_year_id'),
                        'teacher_id' => data_get($data, 'teacher_id'),
                    ];

                    $teachingAssignments = collect($data['teachings'])
                        ->flatMap(fn ($teachingItem) => collect($teachingItem['classroom_id'])->map(fn ($classroomId) => [
                            ...$commonTeachingData,
                            'subject_id' => $teachingItem['subject_id'],
                            'classroom_id' => $classroomId,
                        ]));

                    if ($teachingAssignments->isEmpty()) {
                        $this->halt();
                    }

                    static::getModel()::fillAndInsert($teachingAssignments->toArray());

                    Notification::make()
                        ->title('Teaching assignments created successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public static function getForm(): array
    {
        return [
            Group::make([
                Select::make('school_year_id')
                    ->relationship('schoolYear')
                    ->getOptionLabelFromRecordUsing(fn (SchoolYear $record) => "{$record->name_with_semester}")
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(self::resetRepeater(...))
                    ->live()
                    ->default(fn ($state) => $state ?? SchoolYear::active()->first()?->getRouteKey())
                    ->required(),

                Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(self::resetRepeater(...))
                    ->live()
                    ->default(fn ($state) => $state)
                    ->required(),

                Select::make('school_id')
                    ->label('School')
                    ->options(School::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(self::resetRepeater(...))
                    ->live()
                    ->default(fn ($state) => $state)
                    ->dehydrated(false)
                    ->required(),

                Repeater::make('teachings')
                    ->hiddenLabel()
                    ->disabled(fn (Get $get) => collect(['school_id', 'teacher_id', 'school_year_id'])
                        ->contains(fn ($key) => blank($get($key))))
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
                                modifyQueryUsing: self::filterBySchool(...))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Set $set, $livewire) {
                                $livewire->resetErrorBag();
                                $set('classroom_id', null);
                            })
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        Select::make('classroom_id')
                            ->disabled(fn (Get $get) => blank($get('subject_id')))
                            ->options(self::getCLassroomOptions(...))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->multiple()
                            ->required()
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                    $existingTeachingAssignments = self::getExistingTeachingAssignmentsQuery($get)
                                        ->whereIn('classroom_id', $value)
                                        ->get()
                                        ->pluck('classroom.name')
                                        ->filter()
                                        ->unique();

                                    if ($existingTeachingAssignments->isNotEmpty()) {
                                        $fail("Teaching assignments already exist for: {$existingTeachingAssignments->join(', ')}");
                                    }

                                },
                            ]),
                    ]),
            ])
                ->columns(3),

        ];
    }

    protected static function resetRepeater(Set $set, $livewire): void
    {
        $livewire->resetErrorBag();
        $set('teachings', [
            ['subject_id' => null, 'classroom_id' => null],
        ]);
    }

    protected static function filterBySchool(Builder $query, Get $get): Builder
    {
        return $query->whereRelation('school', 'schools.id', $get('../../school_id'));
    }

    protected static function getExistingTeachingAssignmentsQuery(Get $get): Builder
    {
        return Teaching::query()
            ->where('subject_id', $get('subject_id'))
            ->where('school_year_id', $get('../../school_year_id'));
    }

    protected static function getCLassroomOptions(Get $get)
    {
        // exclude classrooms that already have a teaching assignment for the subject and school year
        $excludedClassrooms = self::getExistingTeachingAssignmentsQuery($get)
            ->pluck('classroom_id')
            ->toArray();

        return self::filterBySchool(
            query: Classroom::whereNotIn('id', $excludedClassrooms),
            get: $get
        )->pluck('name', 'id');
    }
}
