<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\SubjectCategories\Pages\CreateSubjectCategory;
use App\Filament\Admin\Resources\SubjectCategories\Pages\EditSubjectCategory;
use App\Filament\Admin\Resources\SubjectCategories\Pages\ListSubjectCategories;
use App\Filament\Admin\Resources\SubjectCategories\Pages\ViewSubjectCategory;
use App\Filament\Admin\Resources\SubjectCategories\SubjectCategoryResource;
use App\Models\School;
use App\Models\SubjectCategory;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    $this->get(SubjectCategoryResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    SubjectCategory::factory()->for(School::factory())->create();

    Livewire::test(ListSubjectCategories::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
    'school.name',
    'sort_order',
]);

test('list page shows rows', function () {
    $records = SubjectCategory::factory(3)->for(School::factory())->create();

    Livewire::test(ListSubjectCategories::class)
        ->assertCanSeeTableRecords($records);
});

test('list page rows have view action', function () {
    $record = SubjectCategory::factory()->for(School::factory())->create();

    Livewire::test(ListSubjectCategories::class)
        ->assertActionVisible(TestAction::make('view')->table($record));
});

test('can search for records on list page', function (string $attribute) {
    $record = SubjectCategory::factory()->for(School::factory())->create();

    Livewire::test(ListSubjectCategories::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
]);

test('can filter records by school', function () {
    // Arrange
    $subjectCategory1 = SubjectCategory::factory()->for(School::factory())->create();
    $subjectCategory2 = SubjectCategory::factory()->for(School::factory())->create();

    // Act & Assert
    Livewire::test(ListSubjectCategories::class)
        ->filterTable('school_id', $subjectCategory1->school->getKey())
        ->assertCanSeeTableRecords([$subjectCategory1])
        ->assertCanNotSeeTableRecords([$subjectCategory2]);
});

test('list page sorts by sort_order by default', function () {
    // Arrange
    $school = School::factory()->create();
    $category3 = SubjectCategory::factory()->for($school)->create(['sort_order' => 3, 'name' => 'Third']);
    $category1 = SubjectCategory::factory()->for($school)->create(['sort_order' => 1, 'name' => 'First']);
    $category2 = SubjectCategory::factory()->for($school)->create(['sort_order' => 2, 'name' => 'Second']);

    // Act & Assert
    Livewire::test(ListSubjectCategories::class)
        ->assertCanSeeTableRecords([$category1, $category2, $category3], inOrder: true);
});

test('create page is accessible', function () {
    $this->get(SubjectCategoryResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    Livewire::test(CreateSubjectCategory::class)
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'school_id' => 'required',
        ]);
});

test('cannot create a record with duplicate name in same school', function () {
    // Arrange
    $school = School::factory()->create();
    $existingCategory = SubjectCategory::factory()->for($school)->create([
        'name' => 'Duplicate Name',
    ]);

    // Act & Assert
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => $school->getKey(),
            'name' => 'Duplicate Name',
            'sort_order' => 2,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('can create a record with duplicate name in different school', function () {
    // Arrange
    $school1 = School::factory()->create();
    $school2 = School::factory()->create();
    SubjectCategory::factory()->for($school1)->create([
        'name' => 'Same Name',
    ]);

    // Act & Assert - Should succeed because it's a different school
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => $school2->getKey(),
            'name' => 'Same Name',
            'sort_order' => 1,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(SubjectCategory::where('school_id', $school2->id)->where('name', 'Same Name')->exists())
        ->toBeTrue();
});

test('cannot create a record with sort_order less than 1', function () {
    // Arrange
    $school = School::factory()->create();

    // Act & Assert
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => $school->getKey(),
            'name' => 'Test Category',
            'sort_order' => 0,
        ])
        ->call('create')
        ->assertHasFormErrors(['sort_order' => 'min']);
});

test('cannot create a record with negative sort_order', function () {
    // Arrange
    $school = School::factory()->create();

    // Act & Assert
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => $school->getKey(),
            'name' => 'Test Category',
            'sort_order' => -5,
        ])
        ->call('create')
        ->assertHasFormErrors(['sort_order' => 'min']);
});

test('can create a record', function () {
    $school = School::factory()->create();
    $data = SubjectCategory::factory()->make([
        'school_id' => $school->getKey(),
        'name' => 'New Test Category',
        'sort_order' => 5,
    ]);

    Livewire::test(CreateSubjectCategory::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(SubjectCategory::first())
        ->name->toBe('New Test Category')
        ->sort_order->toBe(5)
        ->school_id->toBe($school->getKey());
});

test('view page is accessible', function () {
    $record = SubjectCategory::factory()->for(School::factory())->create();

    $this->get(SubjectCategoryResource::getUrl('view', ['record' => $record]))->assertOk();
});

test('view page shows all information', function () {
    $record = SubjectCategory::factory()->for(School::factory())->create();

    Livewire::test(ViewSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $record->name,
            'school.name' => $record->school->name,
            'sort_order' => $record->sort_order,
        ]);
});

test('view page has edit action', function () {
    $record = SubjectCategory::factory()->for(School::factory())->create();

    Livewire::test(ViewSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->assertActionVisible(TestAction::make('edit')->table($record));
});

test('edit page is accessible', function () {
    $record = SubjectCategory::factory()->for(School::factory())->create();

    $this->get(SubjectCategoryResource::getUrl('edit', ['record' => $record]))->assertOk();
});

test('cannot save a record without required fields', function () {
    $record = SubjectCategory::factory()->for(School::factory())->create();

    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->fillForm(['name' => null])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

test('cannot save a record with duplicate name in same school', function () {
    // Arrange
    $school = School::factory()->create();
    $category1 = SubjectCategory::factory()->for($school)->create([
        'name' => 'Category One',
    ]);
    $category2 = SubjectCategory::factory()->for($school)->create([
        'name' => 'Category Two',
    ]);

    // Act & Assert - Try to rename category2 to the same name as category1
    Livewire::test(EditSubjectCategory::class, ['record' => $category2->getRouteKey()])
        ->fillForm([
            'name' => 'Category One',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('can save a record with same name as itself', function () {
    // Arrange
    $record = SubjectCategory::factory()->for(School::factory())->create([
        'name' => 'Original Name',
    ]);

    // Act & Assert - Should succeed because it's the same record (ignoreRecord works)
    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => 'Original Name',
            'sort_order' => 5,
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

test('cannot save a record with sort_order less than 1', function () {
    // Arrange
    $record = SubjectCategory::factory()->for(School::factory())->create();

    // Act & Assert
    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->fillForm(['sort_order' => 0])
        ->call('save')
        ->assertHasFormErrors(['sort_order' => 'min']);
});

test('can save a record', function () {
    $record = SubjectCategory::factory()->for(School::factory())->create();
    $newSchool = School::factory()->create();
    $newData = SubjectCategory::factory()->for($newSchool)->make([
        'name' => 'Updated Category',
        'sort_order' => 10,
    ]);

    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->fillForm($newData->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->name->toBe('Updated Category')
        ->sort_order->toBe(10)
        ->school_id->toBe($newSchool->getKey());
});

test('can save a record without changes', function () {
    $record = SubjectCategory::factory()->for(School::factory())->create();

    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();
});
