<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Subjects\Pages\CreateSubject;
use App\Filament\Admin\Resources\Subjects\Pages\EditSubject;
use App\Filament\Admin\Resources\Subjects\Pages\ListSubjects;
use App\Filament\Admin\Resources\Subjects\Pages\ViewSubject;
use App\Filament\Admin\Resources\Subjects\SubjectResource;
use App\Models\School;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    // Act & Assert
    $this->get(SubjectResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    // Arrange
    $school = School::factory()->create();
    $category = SubjectCategory::factory()->for($school)->create();
    Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    Livewire::test(ListSubjects::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
    'school.name',
    'sort_order',
]);

test('list page shows rows', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $records = Subject::factory(3)->create(['subject_category_id' => $category->id]);

    // Act & Assert
    Livewire::test(ListSubjects::class)
        ->assertCanSeeTableRecords($records);
});

test('list page rows have view action', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    Livewire::test(ListSubjects::class)
        ->assertActionVisible(TestAction::make('view')->table($record));
});

test('can search for records on list page', function (string $attribute) {
    // Arrange
    $school = School::factory()->create();
    $category = SubjectCategory::factory()->for($school)->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    Livewire::test(ListSubjects::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
    'school.name',
]);

test('can filter records by category', function () {
    // Arrange
    $school = School::factory()->create();
    [$category1, $category2] = SubjectCategory::factory(2)->for($school)->create();
    $subject1 = Subject::factory()->create(['subject_category_id' => $category1->id]);
    $subject2 = Subject::factory()->create(['subject_category_id' => $category2->id]);

    // Act & Assert
    Livewire::test(ListSubjects::class)
        ->filterTable('subject_category_id', $category1->getRouteKey())
        ->assertCanSeeTableRecords([$subject1])
        ->assertCanNotSeeTableRecords([$subject2]);
});

test('list page sorts by sort_order by default', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $subject3 = Subject::factory()->create(['subject_category_id' => $category->id, 'sort_order' => 3, 'name' => 'Third']);
    $subject1 = Subject::factory()->create(['subject_category_id' => $category->id, 'sort_order' => 1, 'name' => 'First']);
    $subject2 = Subject::factory()->create(['subject_category_id' => $category->id, 'sort_order' => 2, 'name' => 'Second']);

    // Act & Assert
    Livewire::test(ListSubjects::class)
        ->assertCanSeeTableRecords([$subject1, $subject2, $subject3], inOrder: true);
});

test('create page is accessible', function () {
    $this->get(SubjectResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    Livewire::test(CreateSubject::class)
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'subject_category_id' => 'required',
        ]);
});

test('cannot create a record with duplicate name in same category', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    Subject::factory()->create([
        'subject_category_id' => $category->id,
        'name' => 'Duplicate Name',
    ]);

    // Act & Assert
    Livewire::test(CreateSubject::class)
        ->fillForm([
            'name' => 'Duplicate Name',
            'subject_category_id' => $category->getRouteKey(),
            'sort_order' => 2,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('can create a record with duplicate name in different category', function () {
    // Arrange
    $school = School::factory()->create();
    [$category1, $category2] = SubjectCategory::factory(2)->for($school)->create();
    Subject::factory()->create([
        'subject_category_id' => $category1->id,
        'name' => 'Same Name',
    ]);

    // Act & Assert
    Livewire::test(CreateSubject::class)
        ->fillForm([
            'subject_category_id' => $category2->getRouteKey(),
            'name' => 'Same Name',
            'sort_order' => 1,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Subject::where('subject_category_id', $category2->id)->where('name', 'Same Name')->exists())
        ->toBeTrue();
});

test('cannot create a record with invalid sort_order', function (int $sortOrder) {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();

    // Act & Assert
    Livewire::test(CreateSubject::class)
        ->fillForm([
            'subject_category_id' => $category->getRouteKey(),
            'name' => 'Test Subject',
            'sort_order' => $sortOrder,
        ])
        ->call('create')
        ->assertHasFormErrors(['sort_order' => 'min']);
})->with([
    'zero' => 0,
    'negative' => -5,
]);

test('can create a record', function () {
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $data = Subject::factory()->make([
        'subject_category_id' => $category->getRouteKey(),
        'name' => 'New Test Subject',
        'sort_order' => 5,
    ]);

    Livewire::test(CreateSubject::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Subject::first())
        ->name->toBe('New Test Subject')
        ->sort_order->toBe(5)
        ->subject_category_id->toBe($category->getRouteKey());
});

test('view page is accessible', function () {
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    $this->get(SubjectResource::getUrl('view', ['record' => $record]))->assertOk();
});

test('view page shows all information', function () {
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    Livewire::test(ViewSubject::class, ['record' => $record->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $record->name,
            'category.name' => $record->category->name,
            'sort_order' => $record->sort_order,
        ]);
});

test('view page has edit action', function () {
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    Livewire::test(ViewSubject::class, ['record' => $record->getRouteKey()])
        ->assertActionVisible(TestAction::make('edit')->table($record));
});

test('edit page is accessible', function () {
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    $this->get(SubjectResource::getUrl('edit', ['record' => $record]))->assertOk();
});

test('cannot save a record without required fields', function () {
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    Livewire::test(EditSubject::class, ['record' => $record->getRouteKey()])
        ->fillForm(['name' => null])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

test('cannot save a record with duplicate name in same category', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    Subject::factory()->create([
        'subject_category_id' => $category->id,
        'name' => 'Subject One',
    ]);
    $subject2 = Subject::factory()->create([
        'subject_category_id' => $category->id,
        'name' => 'Subject Two',
    ]);

    // Act & Assert
    Livewire::test(EditSubject::class, ['record' => $subject2->getRouteKey()])
        ->fillForm(['name' => 'Subject One'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('can save a record with same name as itself', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create([
        'subject_category_id' => $category->id,
        'name' => 'Original Name',
    ]);

    // Act & Assert
    Livewire::test(EditSubject::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => 'Original Name',
            'sort_order' => 5,
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

test('cannot save a record with invalid sort_order', function (int $sortOrder) {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    Livewire::test(EditSubject::class, ['record' => $record->getRouteKey()])
        ->fillForm(['sort_order' => $sortOrder])
        ->call('save')
        ->assertHasFormErrors(['sort_order' => 'min']);
})->with([
    'zero' => 0,
    'negative' => -3,
]);

test('can save a record', function () {
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);
    $newCategory = SubjectCategory::factory()->for(School::factory())->create();
    $newData = Subject::factory()->make([
        'subject_category_id' => $newCategory->id,
        'name' => 'Updated Subject',
        'sort_order' => 10,
    ]);

    Livewire::test(EditSubject::class, ['record' => $record->getRouteKey()])
        ->fillForm($newData->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->name->toBe('Updated Subject')
        ->sort_order->toBe(10)
        ->subject_category_id->toBe($newCategory->getRouteKey());
});

test('can save a record without changes', function () {
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $record = Subject::factory()->create(['subject_category_id' => $category->id]);

    Livewire::test(EditSubject::class, ['record' => $record->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();
});
