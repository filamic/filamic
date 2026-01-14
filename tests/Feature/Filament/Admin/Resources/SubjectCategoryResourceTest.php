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
    SubjectCategory::factory()->create();

    Livewire::test(ListSubjectCategories::class)
        ->assertCanRenderTableColumn($column);
})->with([
    // 'school.name',
    'name',
    'sort_order',
]);

test('list page shows rows', function () {
    $records = SubjectCategory::factory(3)->create();

    Livewire::test(ListSubjectCategories::class)
        ->assertCanSeeTableRecords($records);
});

test('list page rows have view action', function () {
    $record = SubjectCategory::factory()->create();

    Livewire::test(ListSubjectCategories::class)
        ->assertActionVisible(TestAction::make('view')->table($record));
});

test('can search for records on list page', function (string $attribute) {
    $record = SubjectCategory::factory()->create();

    Livewire::test(ListSubjectCategories::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
    // 'school.name',
]);

test('can filter records by school', function () {
    // Arrange
    $subjectCategory1 = SubjectCategory::factory()->create();
    $subjectCategory2 = SubjectCategory::factory()
        ->forSchool(School::factory()->create())
        ->create();

    // Act & Assert
    Livewire::test(ListSubjectCategories::class)
        ->filterTable('school_id', $subjectCategory1->school->getRouteKey())
        ->assertCanSeeTableRecords([$subjectCategory1])
        ->assertCanNotSeeTableRecords([$subjectCategory2]);
});

test('list page sorts by sort_order by default', function () {
    // Arrange
    [$category1, $category2, $category3] = SubjectCategory::factory(3)
        ->sequence(
            ['sort_order' => 1, 'name' => 'Maths'],
            ['sort_order' => 2, 'name' => 'Science'],
            ['sort_order' => 3, 'name' => 'English'],
        )
        ->create();

    // Act & Assert
    Livewire::test(ListSubjectCategories::class)
        ->assertCanSeeTableRecords([$category1, $category2, $category3], inOrder: true);
});

test('create page is accessible', function () {
    $this->get(SubjectCategoryResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => null,
            'name' => null,
            'sort_order' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'school_id' => 'required',
            'name' => 'required',
            'sort_order' => 'required',
        ]);
});

test('cannot create a record if sort_order is less than 1', function () {
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => School::factory()->create()->getRouteKey(),
            'name' => 'General Subject',
            'sort_order' => 0,
        ])
        ->call('create')
        ->assertHasFormErrors(['sort_order' => 'min']);
});

test('cannot create a record if sort_order is not a number', function () {
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => School::factory()->create()->getRouteKey(),
            'name' => 'General Subject',
            'sort_order' => 'not a number',
        ])
        ->call('create')
        ->assertHasFormErrors(['sort_order' => 'numeric']);
});

test('cannot create a record with duplicate name in same school', function () {
    // Arrange
    $existingCategory = SubjectCategory::factory()->create([
        'name' => 'Maths',
    ]);

    // Act & Assert
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => $existingCategory->school->getRouteKey(),
            'name' => 'Maths',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('can create a record', function () {
    $school = School::factory()->create();

    $record = SubjectCategory::factory()->make([
        'school_id' => $school->getRouteKey(),
        'name' => 'General Subject',
        'sort_order' => 1,
    ]);

    Livewire::test(CreateSubjectCategory::class)
        ->fillForm($record->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(SubjectCategory::first())
        ->name->toBe('General Subject')
        ->sort_order->toBe(1)
        ->school_id->toBe($school->getRouteKey());
});

test('can create a record with duplicate name in different school', function () {
    // Arrange
    $school1 = School::factory()->create();
    $school2 = School::factory()->create();
    SubjectCategory::factory()->for($school1)->create([
        'name' => 'Maths',
    ]);

    // Act & Assert - Should succeed because it's a different school
    Livewire::test(CreateSubjectCategory::class)
        ->fillForm([
            'school_id' => $school2->getRouteKey(),
            'name' => 'Maths',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $subjectCategories = SubjectCategory::where('name', 'Maths')->get();

    expect($subjectCategories)
        ->toHaveCount(2)
        ->and($subjectCategories->pluck('school_id')->toArray())
        ->toBe([$school1->getRouteKey(), $school2->getRouteKey()])
        ->and($subjectCategories->pluck('school_id')->unique()->count())
        ->toBe(2);
});

test('view page is accessible', function () {
    $record = SubjectCategory::factory()->create();

    $this->get(SubjectCategoryResource::getUrl('view', ['record' => $record]))->assertOk();
});

test('view page shows all information', function () {
    $record = SubjectCategory::factory()->create();

    Livewire::test(ViewSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->assertSchemaStateSet([
            'school.name' => $record->school->name,
            'name' => $record->name,
            'sort_order' => $record->sort_order,
        ]);
});

test('view page has edit action', function () {
    $record = SubjectCategory::factory()->create();

    Livewire::test(ViewSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->assertActionVisible(TestAction::make('edit')->table($record));
});

test('edit page is accessible', function () {
    $record = SubjectCategory::factory()->create();

    $this->get(SubjectCategoryResource::getUrl('edit', ['record' => $record]))->assertOk();
});

test('cannot save a record without required fields', function () {
    $record = SubjectCategory::factory()->create();

    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'school_id' => null,
            'name' => null,
            'sort_order' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'school_id' => 'required',
            'name' => 'required',
            'sort_order' => 'required',
        ]);
});

test('cannot save a record if sort_order is less than 1', function () {
    $record = SubjectCategory::factory()->create();

    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'sort_order' => 0,
        ])
        ->call('save')
        ->assertHasFormErrors(['sort_order' => 'min']);
});

test('cannot save a record if sort_order is not a number', function () {
    $record = SubjectCategory::factory()->create();

    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'sort_order' => 'not a number',
        ])
        ->call('save')
        ->assertHasFormErrors(['sort_order' => 'numeric']);
});

test('cannot save a record with duplicate name in same school', function () {
    // Arrange
    [$category1, $category2] = SubjectCategory::factory(2)
        ->sequence(
            ['name' => 'Maths'],
            ['name' => 'English'],
        )
        ->create();

    // Act & Assert - Try to rename category2 to the same name as category1
    Livewire::test(EditSubjectCategory::class, ['record' => $category2->getRouteKey()])
        ->fillForm([
            'name' => 'Maths',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('can save a record', function () {
    $record = SubjectCategory::factory()->create();

    $newData = SubjectCategory::factory()->make([
        'name' => 'Maths',
        'sort_order' => 1,
    ]);

    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->fillForm($newData->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->name->toBe('Maths')
        ->sort_order->toBe(1);
});

test('can save a record without changes', function () {
    $record = SubjectCategory::factory()->create();

    Livewire::test(EditSubjectCategory::class, ['record' => $record->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();
});
