<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Schools\Pages\CreateSchool;
use App\Filament\Admin\Resources\Schools\Pages\EditSchool;
use App\Filament\Admin\Resources\Schools\Pages\ListSchools;
use App\Filament\Admin\Resources\Schools\Pages\ViewSchool;
use App\Filament\Admin\Resources\Schools\SchoolResource;
use App\Models\School;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    $this->get(SchoolResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    School::factory()->create();

    Livewire::test(ListSchools::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
]);

test('list page shows rows', function () {
    $records = School::factory(3)->create();

    Livewire::test(ListSchools::class)
        ->assertCanSeeTableRecords($records);
});

test('list page rows have view action', function () {
    $record = School::factory()->create();

    Livewire::test(ListSchools::class)
        ->assertActionVisible(TestAction::make('view')->table($record));
});

test('can search for records on list page', function (string $attribute) {
    $record = School::factory()->create();

    Livewire::test(ListSchools::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
]);

test('create page is accessible', function () {
    $this->get(SchoolResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    Livewire::test(CreateSchool::class)
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
        ]);
});

test('cannot create a record with invalid fields', function () {
    School::factory()->create([
        'name' => 'School ABC',
    ]);

    Livewire::test(CreateSchool::class)
        ->fillForm([
            'name' => 'School ABC',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'unique',
        ]);
});

test('can create a record', function () {
    $record = School::factory()->make();

    Livewire::test(CreateSchool::class)
        ->fillForm($record->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(School::first())->toMatchArray($record->toArray());
});

test('view page is accessible', function () {
    $record = School::factory()->create();

    $this->get(SchoolResource::getUrl('view', ['record' => $record]))->assertOk();
});

test('view page shows all information', function () {
    $record = School::factory()
        ->create();

    Livewire::test(ViewSchool::class, ['record' => $record->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $record->name,
            'address' => $record->address,
            'npsn' => $record->npsn,
            'nis_nss_nds' => $record->nis_nss_nds,
            'telp' => $record->telp,
            'postal_code' => $record->postal_code,
            'village' => $record->village,
            'subdistrict' => $record->subdistrict,
            'city' => $record->city,
            'province' => $record->province,
            'website' => $record->website,
            'email' => $record->email,
        ]);
});

test('view page has edit action', function () {
    $record = School::factory()->create();

    Livewire::test(ViewSchool::class, ['record' => $record->getRouteKey()])
        ->assertActionVisible(TestAction::make('edit')->table($record));
});

test('edit page is accessible', function () {
    $record = School::factory()->create();

    $this->get(SchoolResource::getUrl('edit', ['record' => $record]))->assertOk();
});

test('cannot save a record without required fields', function () {
    $record = School::factory()->create();

    Livewire::test(EditSchool::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'name' => 'required',
        ]);
});

test('cannot save a record with invalid fields', function () {
    [$schoolA, $schoolB] = School::factory(2)
        ->forEachSequence(
            ['name' => 'School A'],
            ['name' => 'School B'],
        )
        ->create();

    Livewire::test(EditSchool::class, ['record' => $schoolA->getRouteKey()])
        ->fillForm([
            'name' => 'School B',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('can save a record and ignore the current record', function () {
    $record = School::factory()->create([
        'name' => 'School A',
    ]);

    Livewire::test(EditSchool::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => 'School A',
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

test('can save a record', function () {
    $record = School::factory()->create();

    $newRecord = School::factory()
        ->make();

    Livewire::test(EditSchool::class, ['record' => $record->getRouteKey()])
        ->fillForm($newRecord->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh()->toArray())->toMatchArray($newRecord->toArray());
});

test('can save a record without changes', function () {
    $record = School::factory()->create();

    Livewire::test(EditSchool::class, ['record' => $record->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();
});
