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
    $data = Classroom::factory()->make(['school_id' => $school->id]);

    Livewire::test(CreateClassroom::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Classroom::first())->toMatchArray($data->toArray());
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
    $newData = Classroom::factory()->for($newSchool)->make();

    Livewire::test(EditClassroom::class, ['record' => $record->getRouteKey()])
        ->fillForm($newData->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh()->toArray())->toMatchArray($newData->toArray());
});

test('can save a record without changes', function () {
    $record = Classroom::factory()->forSchool()->create();

    Livewire::test(EditClassroom::class, ['record' => $record->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();
});
