<?php

declare(strict_types=1);

use App\Enums\SemesterEnum;
use App\Filament\Admin\Resources\SchoolYears\Pages\CreateSchoolYear;
use App\Filament\Admin\Resources\SchoolYears\Pages\EditSchoolYear;
use App\Filament\Admin\Resources\SchoolYears\Pages\ListSchoolYears;
use App\Filament\Admin\Resources\SchoolYears\Pages\ViewSchoolYear;
use App\Filament\Admin\Resources\SchoolYears\SchoolYearResource;
use App\Models\SchoolYear;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    $this->get(SchoolYearResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    SchoolYear::factory()->create();

    Livewire::test(ListSchoolYears::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
    'semester',
    'is_active',
]);

test('list page shows rows', function () {
    $records = SchoolYear::factory(3)->create();

    Livewire::test(ListSchoolYears::class)
        ->assertCanSeeTableRecords($records);
});

test('list page rows have view action', function () {
    $record = SchoolYear::factory()->create();

    Livewire::test(ListSchoolYears::class)
        ->assertActionVisible(TestAction::make('view')->table($record));
});

test('can search for records on list page', function (string $attribute) {
    $record = SchoolYear::factory()->create();

    Livewire::test(ListSchoolYears::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
]);

test('create page is accessible', function () {
    $this->get(SchoolYearResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    Livewire::test(CreateSchoolYear::class)
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'semester' => 'required',
        ]);
});

test('cannot create a record with invalid semester', function () {
    Livewire::test(CreateSchoolYear::class)
        ->fillForm([
            'name' => '2024/2025',
            'semester' => 3,
            'start_date' => '2024-06-01',
            'end_date' => '2024-01-01',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'semester',
            'end_date' => 'after',
        ]);
});

test('cannot create a record with duplicate semester', function () {
    SchoolYear::factory()->create([
        'name' => '2024/2025',
        'semester' => SemesterEnum::ODD,
    ]);

    Livewire::test(CreateSchoolYear::class)
        ->fillForm([
            'name' => '2024/2025',
            'semester' => SemesterEnum::ODD,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'semester' => 'unique',
        ]);
});

test('can create a record', function () {
    $data = SchoolYear::factory()->make([
        'name' => '2025/2026',
        'semester' => SemesterEnum::ODD,
    ]);

    Livewire::test(CreateSchoolYear::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(SchoolYear::first())
        ->name->toBe('2025/2026')
        ->semester->toBe(SemesterEnum::ODD);
});

test('can create an active school year', function () {
    $data = SchoolYear::factory()->make([
        'name' => '2024/2025',
        'is_active' => true,
    ]);

    Livewire::test(CreateSchoolYear::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(SchoolYear::where('name', '2024/2025')->first()->is_active)->toBeTrue();
});

test('creating an active school year deactivates others', function () {
    $existingActive = SchoolYear::factory()
        ->active()
        ->create([
            'name' => '2023/2024',
        ]);

    $data = SchoolYear::factory()->make([
        'name' => '2024/2025',
        'is_active' => true,
    ]);

    Livewire::test(CreateSchoolYear::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect($existingActive->refresh()->is_active)->toBeFalse()
        ->and(SchoolYear::where('name', '2024/2025')->first()->is_active)->toBeTrue()
        ->and(SchoolYear::active()->count())->toBe(1);
});

test('creating an inactive school year does not deactivate others', function () {
    $existingActive = SchoolYear::factory()->active()->create(['name' => '2023/2024']);
    $data = SchoolYear::factory()->make([
        'name' => '2024/2025',
        'is_active' => false,
    ]);

    Livewire::test(CreateSchoolYear::class)
        ->fillForm($data->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect($existingActive->refresh()->is_active)->toBeTrue()
        ->and(SchoolYear::where('name', '2024/2025')->first()->is_active)->toBeFalse()
        ->and(SchoolYear::active()->count())->toBe(1);
});

test('view page is accessible', function () {
    $record = SchoolYear::factory()->create();

    $this->get(SchoolYearResource::getUrl('view', ['record' => $record]))->assertOk();
});

test('view page displays all information', function () {
    $record = SchoolYear::factory()->create([
        'name' => '2024/2025',
        'semester' => SemesterEnum::ODD,
    ]);

    Livewire::test(ViewSchoolYear::class, ['record' => $record->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $record->name,
            'semester' => $record->semester,
            'start_date' => $record->start_date,
            'end_date' => $record->end_date,
            'is_active' => $record->is_active,
        ]);
});

test('view page has edit action', function () {
    $record = SchoolYear::factory()->create();

    Livewire::test(ViewSchoolYear::class, ['record' => $record->getRouteKey()])
        ->assertActionVisible(TestAction::make('edit')->table($record));
});

test('edit page is accessible', function () {
    $record = SchoolYear::factory()->create();

    $this->get(SchoolYearResource::getUrl('edit', ['record' => $record]))->assertOk();
});

test('cannot save a record without required fields', function () {
    $record = SchoolYear::factory()->create();

    Livewire::test(EditSchoolYear::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => null,
            'semester' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'name' => 'required',
            'semester' => 'required',
        ]);
});

test('cannot save a record when end_date is before start_date', function () {
    $record = SchoolYear::factory()->create();

    Livewire::test(EditSchoolYear::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'start_date' => '2024-06-01',
            'end_date' => '2024-01-01',
        ])
        ->call('save')
        ->assertHasFormErrors(['end_date']);
});

test('can save a record', function () {
    $record = SchoolYear::factory()->create([
        'start_date' => '2024-01-01',
        'end_date' => '2024-12-31',
    ]);

    Livewire::test(EditSchoolYear::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => '2026/2027',
            'semester' => SemesterEnum::EVEN,
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->name->toBe('2026/2027')
        ->semester->toBe(SemesterEnum::EVEN);
});

test('list page has toggle column for is_active', function () {
    $record = SchoolYear::factory()->create();

    Livewire::test(ListSchoolYears::class)
        ->assertCanRenderTableColumn('is_active');
});

test('can save a record without changes', function () {
    $record = SchoolYear::factory()->create();

    Livewire::test(EditSchoolYear::class, ['record' => $record->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();
});
