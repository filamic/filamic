<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\HomeroomClasses\HomeroomClassResource;
use App\Filament\Admin\Resources\HomeroomClasses\Pages\ManageHomeroomClasses;
use App\Models\Classroom;
use App\Models\HomeroomClass;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Teacher;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

// beforeEach(fn () => $this->loginAdmin());

// test('list page is accessible', function () {
//     $this->get(HomeroomClassResource::getUrl())->assertOk();
// });

// test('list page renders columns', function (string $column) {
//     // Arrange
//     HomeroomClass::factory()->create();

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->assertCanRenderTableColumn($column);
// })->with([
//     'teacher.name',
//     'classroom.name',
//     'schoolYear.name_with_semester',
// ]);

// test('list page shows rows', function () {
//     // Arrange
//     $records = HomeroomClass::factory(3)
//         ->forSchoolYear() // we need this to make sure the records are use the same school year
//         ->create();

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->assertCanSeeTableRecords($records);
// });

// test('list page rows have delete action', function () {
//     $record = HomeroomClass::factory()->create();

//     Livewire::test(ManageHomeroomClasses::class)
//         ->assertActionVisible(TestAction::make('delete')->table($record));
// });

// test('delete action is disabled for inactive records', function () {
//     $record = HomeroomClass::factory()
//         ->for(SchoolYear::factory()->inactive())
//         ->create();

//     Livewire::test(ManageHomeroomClasses::class)
//         ->assertActionDisabled(TestAction::make('delete')->table($record));
// });

// test('delete action is enabled for active records', function () {
//     $record = HomeroomClass::factory()
//         ->for(SchoolYear::factory()->active())
//         ->create();

//     Livewire::test(ManageHomeroomClasses::class)
//         ->assertActionEnabled(TestAction::make('delete')->table($record));
// });

// test('can search for records on list page', function (string $attribute) {
//     // Arrange
//     $homeroomClass = HomeroomClass::factory()->create();

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->searchTable(data_get($homeroomClass, $attribute))
//         ->assertCanSeeTableRecords([$homeroomClass]);
// })->with([
//     'teacher.name',
//     'classroom.name',
// ]);

// test('can filter records by teacher', function () {
//     // Arrange
//     $records = HomeroomClass::factory(3)->create();
//     $teacher = $records->first()->teacher_id;

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->filterTable('teacher_id', $teacher)
//         ->assertCanSeeTableRecords($records->where('teacher_id', $teacher))
//         ->assertCanNotSeeTableRecords($records->where('teacher_id', '!=', $teacher));
// });

// test('can filter records by classroom', function () {
//     // Arrange
//     $records = HomeroomClass::factory(3)->create();
//     $classroom = $records->first()->classroom_id;

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->filterTable('classroom_id', $classroom)
//         ->assertCanSeeTableRecords($records->where('classroom_id', $classroom))
//         ->assertCanNotSeeTableRecords($records->where('classroom_id', '!=', $classroom));
// });

// test('can filter records by school year', function () {
//     // Arrange
//     $records = HomeroomClass::factory(3)->create();
//     $schoolYear = $records->first()->school_year_id;

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->filterTable('school_year_id', $schoolYear)
//         ->assertCanSeeTableRecords($records->where('school_year_id', $schoolYear))
//         ->assertCanNotSeeTableRecords($records->where('school_year_id', '!=', $schoolYear));
// });

// test('can filter records by multiple filters', function () {
//     // Arrange
//     $records = HomeroomClass::factory(3)->create();
//     $record = $records->first();

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->filterTable('teacher_id', $record->teacher_id)
//         ->filterTable('classroom_id', $record->classroom_id)
//         ->filterTable('school_year_id', $record->school_year_id)
//         ->assertCanSeeTableRecords([$record])
//         ->assertCanNotSeeTableRecords($records->except($record->getRouteKey()));
// });

// test('create action is accessible', function () {
//     Livewire::test(ManageHomeroomClasses::class)
//         ->assertActionVisible('create');
// });

// test('cannot create a record without required fields', function () {
//     Livewire::test(ManageHomeroomClasses::class)
//         ->mountAction('create')
//         ->callMountedAction()
//         ->assertHasFormErrors([
//             'school_year_id' => 'required',
//             'teacher_id' => 'required',
//             'school_id' => 'required',
//             'classroom_id' => 'required',
//         ]);
// });

// test('cannot create a record with duplicate classroom and school year', function () {
//     $record = HomeroomClass::factory()->create();

//     Livewire::test(ManageHomeroomClasses::class)
//         ->mountAction('create')
//         ->fillForm([
//             'school_year_id' => $record->school_year_id,
//             'teacher_id' => Teacher::factory()->create()->getRouteKey(),
//             'school_id' => $record->classroom->school_id,
//             'classroom_id' => $record->classroom_id,
//         ])
//         ->callMountedAction()
//         ->assertHasFormErrors(['classroom_id' => 'unique']);
// });

// test('can create a record', function () {
//     // Arrange
//     $schoolYear = SchoolYear::factory()->active()->create();
//     $teacher = Teacher::factory()->create();
//     $classroom = Classroom::factory()->create();
//     $school = $classroom->school;

//     // Act
//     Livewire::test(ManageHomeroomClasses::class)
//         ->mountAction('create')
//         ->fillForm([
//             'school_year_id' => $schoolYear->getRouteKey(),
//             'teacher_id' => $teacher->getRouteKey(),
//             'school_id' => $school->getRouteKey(),
//             'classroom_id' => $classroom->getRouteKey(),
//         ])
//         ->callMountedAction()
//         ->assertHasNoFormErrors()
//         ->assertNotified();

//     // Assert
//     expect(HomeroomClass::first())
//         ->teacher_id->toBe($teacher->id)
//         ->classroom_id->toBe($classroom->id)
//         ->school_year_id->toBe($schoolYear->id);
// });

// test('classroom field is disabled when teacher is not selected', function () {
//     // Arrange
//     $schoolYear = SchoolYear::factory()->active()->create();

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->mountAction('create')
//         ->fillForm([
//             'school_year_id' => $schoolYear->getRouteKey(),
//         ])
//         ->assertFormFieldIsDisabled('classroom_id');
// });

// test('classroom field is enabled when teacher is selected', function () {
//     // Arrange
//     $schoolYear = SchoolYear::factory()->active()->create();
//     $teacher = Teacher::factory()->create();
//     $school = School::factory()->create();

//     // Act & Assert
//     Livewire::test(ManageHomeroomClasses::class)
//         ->mountAction('create')
//         ->fillForm([
//             'school_year_id' => $schoolYear->getRouteKey(),
//             'teacher_id' => $teacher->getRouteKey(),
//             'school_id' => $school->getRouteKey(),
//         ])
//         ->assertFormFieldIsEnabled('classroom_id');
// });

// test('records are selectable only when can delete', function () {
//     // Arrange
//     $activeSchoolYear = SchoolYear::factory()->active()->create();
//     $inactiveSchoolYear = SchoolYear::factory()->inactive()->create();

//     $activeRecord = HomeroomClass::factory()
//         ->for($activeSchoolYear, 'schoolYear')
//         ->create();
//     $inactiveRecord = HomeroomClass::factory()
//         ->for($inactiveSchoolYear, 'schoolYear')
//         ->create();

//     // Act & Assert
//     // Active record should be selectable (canDelete returns true)
//     expect($activeRecord->canDelete())->toBeTrue();

//     // Inactive record should not be selectable (canDelete returns false)
//     expect($inactiveRecord->canDelete())->toBeFalse();

//     // Verify the table shows the active record (default filter is active school year)
//     Livewire::test(ManageHomeroomClasses::class)
//         ->assertCanSeeTableRecords([$activeRecord]);

//     // Clear filter to see both records
//     Livewire::test(ManageHomeroomClasses::class)
//         ->filterTable('school_year_id', null)
//         ->assertCanSeeTableRecords([$activeRecord, $inactiveRecord]);
// });
