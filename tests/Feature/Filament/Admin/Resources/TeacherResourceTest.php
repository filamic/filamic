<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Admin\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Admin\Resources\Teachers\Pages\ListTeachers;
use App\Filament\Admin\Resources\Teachers\Pages\ViewTeacher;
use App\Filament\Admin\Resources\Teachers\TeacherResource;
use App\Models\Teacher;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    $this->get(TeacherResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    Teacher::factory()->create();

    Livewire::test(ListTeachers::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
]);

test('list page shows rows', function () {
    $records = Teacher::factory(3)->create();

    Livewire::test(ListTeachers::class)
        ->assertCanSeeTableRecords($records);
});

test('list page rows have view action', function () {
    $record = Teacher::factory()->create();

    Livewire::test(ListTeachers::class)
        ->assertActionVisible(TestAction::make('view')->table($record));
});

test('can search for records on list page', function (string $attribute) {
    $record = Teacher::factory()->create();

    Livewire::test(ListTeachers::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
]);

test('create page is accessible', function () {
    $this->get(TeacherResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    Livewire::test(CreateTeacher::class)
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

test('can create a record', function () {
    $data = Teacher::factory()->make([
        'name' => 'New Test Teacher',
    ]);

    Livewire::test(CreateTeacher::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Teacher::first())
        ->name->toBe('New Test Teacher');
});

test('view page is accessible', function () {
    $record = Teacher::factory()->create();

    $this->get(TeacherResource::getUrl('view', ['record' => $record]))->assertOk();
});

test('view page shows all information', function () {
    $record = Teacher::factory()->create();

    Livewire::test(ViewTeacher::class, ['record' => $record->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $record->name,
        ]);
});

test('view page has edit action', function () {
    $record = Teacher::factory()->create();

    Livewire::test(ViewTeacher::class, ['record' => $record->getRouteKey()])
        ->assertActionVisible(TestAction::make('edit')->table($record));
});

test('edit page is accessible', function () {
    $record = Teacher::factory()->create();

    $this->get(TeacherResource::getUrl('edit', ['record' => $record]))->assertOk();
});

test('cannot save a record without required fields', function () {
    $record = Teacher::factory()->create();

    Livewire::test(EditTeacher::class, ['record' => $record->getRouteKey()])
        ->fillForm(['name' => null])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

test('can save a record', function () {
    $record = Teacher::factory()->create();
    $newData = Teacher::factory()->make([
        'name' => 'Updated Teacher Name',
    ]);

    Livewire::test(EditTeacher::class, ['record' => $record->getRouteKey()])
        ->fillForm($newData->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->name->toBe('Updated Teacher Name');
});

test('can save a record without changes', function () {
    $record = Teacher::factory()->create();

    Livewire::test(EditTeacher::class, ['record' => $record->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();
});
