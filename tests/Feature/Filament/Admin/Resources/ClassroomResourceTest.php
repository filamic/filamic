<?php

declare(strict_types=1);

use App\Enums\GradeEnum;
use App\Enums\LevelEnum;
use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use App\Filament\Admin\Resources\Classrooms\Pages\CreateClassroom;
use App\Filament\Admin\Resources\Classrooms\Pages\EditClassroom;
use App\Filament\Admin\Resources\Classrooms\Pages\ListClassrooms;
use App\Models\Classroom;
use App\Models\School;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    // Act & Assert
    $this->get(ClassroomResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    // Arrange
    Classroom::factory()->create();

    // Act & Assert
    Livewire::test(ListClassrooms::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'school.name',
    'name',
    'grade',
    'phase',
    'is_moving_class',
]);

test('list page shows rows', function () {
    // Arrange
    $records = Classroom::factory(3)->create();

    // Act & Assert
    Livewire::test(ListClassrooms::class)
        ->assertCanSeeTableRecords($records);
});

test('can search for records on list page', function (string $attribute) {
    // Arrange
    $record = Classroom::factory()->create();

    // Act & Assert
    Livewire::test(ListClassrooms::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
    'school.name',
]);

test('can filter records by school', function () {
    // Arrange
    $school1 = School::factory()->create();
    $school2 = School::factory()->create();
    $classroom1 = Classroom::factory()->for($school1)->create();
    $classroom2 = Classroom::factory()->for($school2)->create();

    // Act & Assert
    Livewire::test(ListClassrooms::class)
        ->filterTable('school_id', $school1->getRouteKey())
        ->assertCanSeeTableRecords([$classroom1])
        ->assertCanNotSeeTableRecords([$classroom2]);
});

test('can filter records by moving class status', function () {
    // Arrange
    [$movingClassroom, $regularClassroom] = Classroom::factory(2)
        ->sequence(
            ['is_moving_class' => true],
            ['is_moving_class' => false],
        )
        ->create();

    // Act & Assert
    Livewire::test(ListClassrooms::class)
        ->filterTable('is_moving_class', true)
        ->assertCanSeeTableRecords([$movingClassroom])
        ->assertCanNotSeeTableRecords([$regularClassroom]);
});

test('create page is accessible', function () {
    // Act & Assert
    $this->get(ClassroomResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    // Act & Assert
    Livewire::test(CreateClassroom::class)
        ->call('create')
        ->assertHasFormErrors([
            'school_id' => 'required',
            'name' => 'required',
        ]);
});

test('can create a record', function () {
    // Arrange
    $school = School::factory()->create(['level' => LevelEnum::SENIOR_HIGH->value]);

    // Act
    Livewire::test(CreateClassroom::class)
        ->fillForm([
            'school_id' => $school->getKey(),
            'temp_level' => $school->level->value,
            'name' => 'New Test Classroom',
            'grade' => GradeEnum::GRADE_10->value,
            'phase' => 'A',
            'is_moving_class' => false,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    // Assert
    expect(Classroom::query()->first())
        ->not->toBeNull()
        ->name->toBe('New Test Classroom')
        ->grade->toBe(GradeEnum::GRADE_10);
});

test('view page is accessible', function () {
    // Arrange
    $record = Classroom::factory()->create();

    // Act & Assert
    $this->get(ClassroomResource::getUrl('view', ['record' => $record]))->assertOk();
});

test('edit page is accessible', function () {
    // Arrange
    $record = Classroom::factory()->create();

    // Act & Assert
    $this->get(ClassroomResource::getUrl('edit', ['record' => $record]))->assertOk();
});

test('cannot save a record without required fields', function () {
    // Arrange
    $record = Classroom::factory()->create();

    // Act & Assert
    Livewire::test(EditClassroom::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'school_id' => null,
            'name' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'school_id' => 'required',
            'name' => 'required',
        ]);
});

test('can save a record', function () {
    // Arrange
    $school = School::factory()->create(['level' => LevelEnum::SENIOR_HIGH->value]);
    $record = Classroom::factory()->for($school)->create(['grade' => GradeEnum::GRADE_10->value]);

    // Act
    Livewire::test(EditClassroom::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'temp_level' => $school->level->value,
            'name' => 'Updated Classroom',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    // Assert
    expect($record->refresh())
        ->name->toBe('Updated Classroom');
});

test('can save a record without changes', function () {
    // Arrange
    $school = School::factory()->create(['level' => LevelEnum::SENIOR_HIGH->value]);
    $record = Classroom::factory()->for($school)->create(['grade' => GradeEnum::GRADE_10->value]);

    // Act & Assert
    Livewire::test(EditClassroom::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'temp_level' => $school->level->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});
