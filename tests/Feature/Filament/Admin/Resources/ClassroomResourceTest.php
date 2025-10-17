<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use App\Filament\Admin\Resources\Classrooms\Pages\CreateClassroom;
use App\Filament\Admin\Resources\Classrooms\Pages\EditClassroom;
use App\Filament\Admin\Resources\Classrooms\Pages\ListClassrooms;
use App\Filament\Admin\Resources\Classrooms\Pages\ViewClassroom;
use App\Models\Classroom;
use App\Models\School;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    $this->get(ClassroomResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    Classroom::factory()->forSchool()->create();

    Livewire::test(ListClassrooms::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
    'school.name',
    'grade',
    'phase',
    'is_moving_class',
]);

test('list page shows rows', function () {
    $records = Classroom::factory(3)->forSchool()->create();

    Livewire::test(ListClassrooms::class)
        ->assertCanSeeTableRecords($records);
});

test('list page rows have view action', function () {
    $record = Classroom::factory()->forSchool()->create();

    Livewire::test(ListClassrooms::class)
        ->assertActionVisible(TestAction::make('view')->table($record));
});

test('can search for records on list page', function (string $attribute) {
    $record = Classroom::factory()->forSchool()->create();

    Livewire::test(ListClassrooms::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
]);

test('can filter records by school', function () {
    // Arrange
    $classroom1 = Classroom::factory()->forSchool()->create();
    $classroom2 = Classroom::factory()->forSchool()->create();

    // Act & Assert
    Livewire::test(ListClassrooms::class)
        ->filterTable('school_id', $classroom1->school->getKey())
        ->assertCanSeeTableRecords([$classroom1])
        ->assertCanNotSeeTableRecords([$classroom2]);
});

test('can filter records by grade', function () {
    // Arrange
    [$grade10, $grade11, $grade12] = Classroom::factory(3)
        ->forSchool()
        ->sequence(
            ['grade' => 10],
            ['grade' => 11],
            ['grade' => 12],
        )
        ->create();

    // Act & Assert
    Livewire::test(ListClassrooms::class)
        ->filterTable('grade', '11')
        ->assertCanSeeTableRecords([$grade11])
        ->assertCanNotSeeTableRecords([$grade10, $grade12]);
});

test('can filter records by moving class status', function () {
    // Arrange
    $movingClassroom = Classroom::factory()
        ->forSchool()
        ->setAsMovingClass()
        ->create();

    $regularClassroom = Classroom::factory()
        ->forSchool()
        ->setAsRegularClass()
        ->create();

    // Act & Assert - Filter for moving class
    Livewire::test(ListClassrooms::class)
        ->filterTable('is_moving_class', true)
        ->assertCanSeeTableRecords([$movingClassroom])
        ->assertCanNotSeeTableRecords([$regularClassroom]);

    // Act & Assert - Filter for non-moving class
    Livewire::test(ListClassrooms::class)
        ->filterTable('is_moving_class', false)
        ->assertCanSeeTableRecords([$regularClassroom])
        ->assertCanNotSeeTableRecords([$movingClassroom]);
});

test('can apply multiple filters simultaneously', function () {
    // Arrange
    [$classroom1, $classroom2] = Classroom::factory(2)
        ->forSchool()
        ->sequence(
            ['grade' => 10],
            ['grade' => 11],
        )
        ->sequence(
            ['is_moving_class' => true],
            ['is_moving_class' => false],
        )
        ->create();

    // Act & Assert
    Livewire::test(ListClassrooms::class)
        ->filterTable('school_id', $classroom1->school->getKey())
        ->filterTable('grade', '10')
        ->filterTable('is_moving_class', true)
        ->assertCanSeeTableRecords([$classroom1])
        ->assertCanNotSeeTableRecords([$classroom2]);
});

test('create page is accessible', function () {
    $this->get(ClassroomResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    Livewire::test(CreateClassroom::class)
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'school_id' => 'required',
        ]);
});

test('can create a record', function () {
    $school = School::factory()->create();
    $data = Classroom::factory()->make([
        'school_id' => $school->getKey(),
        'name' => 'New Test Classroom',
        'grade' => 12,
    ]);

    Livewire::test(CreateClassroom::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Classroom::first())
        ->name->toBe('New Test Classroom')
        ->grade->toBe(12)
        ->school_id->toBe($school->getKey());
});

test('view page is accessible', function () {
    $record = Classroom::factory()->forSchool()->create();

    $this->get(ClassroomResource::getUrl('view', ['record' => $record]))->assertOk();
});

test('view page shows all information', function () {
    $record = Classroom::factory()->forSchool()->create();

    Livewire::test(ViewClassroom::class, ['record' => $record->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $record->name,
            'school.name' => $record->school->name,
            'grade' => $record->grade,
            'phase' => $record->phase,
            'is_moving_class' => $record->is_moving_class,
        ]);
});

test('view page has edit action', function () {
    $record = Classroom::factory()->forSchool()->create();

    Livewire::test(ViewClassroom::class, ['record' => $record->getRouteKey()])
        ->assertActionVisible(TestAction::make('edit')->table($record));
});

test('edit page is accessible', function () {
    $record = Classroom::factory()->forSchool()->create();

    $this->get(ClassroomResource::getUrl('edit', ['record' => $record]))->assertOk();
});

test('cannot save a record without required fields', function () {
    $record = Classroom::factory()->forSchool()->create();

    Livewire::test(EditClassroom::class, ['record' => $record->getRouteKey()])
        ->fillForm(['name' => null])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

test('can save a record', function () {
    $record = Classroom::factory()->forSchool()->create();
    $newSchool = School::factory()->create();
    $newData = Classroom::factory()->for($newSchool)->make([
        'name' => 'Updated Classroom',
        'grade' => 11,
    ]);

    Livewire::test(EditClassroom::class, ['record' => $record->getRouteKey()])
        ->fillForm($newData->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->name->toBe('Updated Classroom')
        ->grade->toBe(11)
        ->school_id->toBe($newSchool->getKey());
});

test('can save a record without changes', function () {
    $record = Classroom::factory()->forSchool()->create();

    Livewire::test(EditClassroom::class, ['record' => $record->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();
});
