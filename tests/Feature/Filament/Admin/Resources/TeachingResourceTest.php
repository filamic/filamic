<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Teachings\Pages\ManageTeachings;
use App\Filament\Admin\Resources\Teachings\TeachingResource;
use App\Models\SchoolYear;
use App\Models\Teaching;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    $this->get(TeachingResource::getUrl())->assertOk();
});

test('records should have the same school as the subject and classroom', function () {
    $record = Teaching::factory()->create();

    expect($record->subject->school->getRouteKey())->toBe($record->classroom->school->getRouteKey());
});

test('list page renders columns', function (string $column) {
    // Arrange
    $teaching = Teaching::factory()->create();

    // Act & Assert
    Livewire::test(ManageTeachings::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'teacher.name',
    'subject.name',
    'classroom.name',
    'classroom.school.name',
    'schoolYear.name_with_semester',
]);

test('list page shows rows', function () {
    // Arrange
    $records = Teaching::factory(3)
        ->forSchoolYear()
        ->create();

    // Act & Assert
    Livewire::test(ManageTeachings::class)
        ->assertCanSeeTableRecords($records);
});

test('list page rows have delete action', function () {
    $record = Teaching::factory()->create();

    Livewire::test(ManageTeachings::class)
        ->assertActionVisible(TestAction::make('delete')->table($record));
});

test('delete action is disabled for inactive records', function () {
    $record = Teaching::factory()
        ->for(SchoolYear::factory()->inactive())
        ->create();

    Livewire::test(ManageTeachings::class)
        ->assertActionDisabled(TestAction::make('delete')->table($record));
});

test('can search for records on list page', function (string $attribute) {
    // Arrange
    $teaching = Teaching::factory()->create();

    // Act & Assert
    Livewire::test(ManageTeachings::class)
        ->searchTable(data_get($teaching, $attribute))
        ->assertCanSeeTableRecords([$teaching]);
})->with([
    'teacher.name',
    'subject.name',
    'classroom.name',
    'classroom.school.name',
]);

test('can filter records by teacher', function () {
    $records = Teaching::factory(3)
        ->create();

    $teacher = $records->first()->teacher_id;

    Livewire::test(ManageTeachings::class)
        ->filterTable('teacher_id', $teacher)
        ->assertCanSeeTableRecords($records->where('teacher_id', $teacher))
        ->assertCanNotSeeTableRecords($records->where('teacher_id', '!=', $teacher));
});

test('can filter records by subject', function () {
    $records = Teaching::factory(3)
        ->create();

    $subject = $records->first()->subject_id;

    Livewire::test(ManageTeachings::class)
        ->filterTable('subject_id', $subject)
        ->assertCanSeeTableRecords($records->where('subject_id', $subject))
        ->assertCanNotSeeTableRecords($records->where('subject_id', '!=', $subject));
});

test('can filter records by classroom', function () {
    $records = Teaching::factory(3)
        ->create();

    $classroom = $records->first()->classroom_id;

    Livewire::test(ManageTeachings::class)
        ->filterTable('classroom_id', $classroom)
        ->assertCanSeeTableRecords($records->where('classroom_id', $classroom))
        ->assertCanNotSeeTableRecords($records->where('classroom_id', '!=', $classroom));
});

test('can filter records by school year', function () {
    $records = Teaching::factory(3)
        ->create();

    $schoolYear = $records->first()->school_year_id;

    Livewire::test(ManageTeachings::class)
        ->filterTable('school_year_id', $schoolYear)
        ->assertCanSeeTableRecords($records->where('school_year_id', $schoolYear))
        ->assertCanNotSeeTableRecords($records->where('school_year_id', '!=', $schoolYear));
});

test('can filter records by multiple filters', function () {
    $records = Teaching::factory(3)
        ->create();

    $record = $records->first();

    Livewire::test(ManageTeachings::class)
        ->filterTable('teacher_id', $record->teacher_id)
        ->filterTable('subject_id', $record->subject_id)
        ->filterTable('classroom_id', $record->classroom_id)
        ->filterTable('school_year_id', $record->school_year_id)
        ->assertCanSeeTableRecords([$record])
        ->assertCanNotSeeTableRecords($records->except($record->getRouteKey()));
});

test('cannot create a record without required fields', function () {
    $undoRepeaterFake = Repeater::fake();

    Livewire::test(ManageTeachings::class)
        ->callAction('new_teaching')
        ->assertHasFormErrors([
            'school_year_id' => 'required',
            'teacher_id' => 'required',
            'school_id' => 'required',
            'teachings.0.subject_id' => 'required',
            'teachings.0.classroom_id' => 'required',
        ]);

    $undoRepeaterFake();
});

test('classroom options are filtered')->todo();

test('subject options are filtered by selected school')->todo();

test('cannot create a record with duplicate classroom and subject and school year')->todo();

test('can create a record with multiple classrooms and subjects')->todo();

test('can delete a record')->todo();

test('cannot bulk delete a record that is inactive')->todo();

test('can bulk delete a record that is inactive')->todo();
