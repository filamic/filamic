<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Schools\Pages\ListSchools;
use App\Filament\Admin\Resources\Schools\SchoolResource;
use App\Models\School;

beforeEach(fn () => $this->loginAdmin());

// test('list page is accessible', function () {
//     $this->get(SchoolResource::getUrl())->assertOk();
// });

// test('list page renders columns', function (string $column) {
//     $record = School::factory()->create();

//     Livewire::test(ListSchools::class)
//         ->assertCanSeeTableColumns([$column]);
// })->with([
//     'name',
// ]);

// test('list page shows rows', function () {
//     $records = School::factory(3)->create();

//     Livewire::test(ListSchools::class)
//         ->assertCanSeeTableRecords($records);
// });

// test('list page rows have view action', function () {
//     $record = School::factory()->create();

//     Livewire::test(ListSchools::class)
//         ->assertTableActionVisible(ViewAction::class, $record);
// });

// test('can search for records on list page', function (string $attribute) {
//     $record = School::factory()->create();

//     Livewire::test(ListSchools::class)
//         ->searchTable(data_get($record, $attribute))
//         ->assertCanSeeTableRecords([$record]);
// })->with([
//     'name',
//     'services.name',
//     // 'workspaceUser.user.email',
// ]);

// test('can filter records on list page', function () {
//     $records = School::factory(3)->create();

//     Livewire::test(ListSchools::class)
//         ->assertCanSeeTableRecords($records)
//         ->filterTable('name', $records->first()->name)
//         ->assertCanSeeTableRecords($records->where('name', $records->first()->name));
// });

// test('create page is accessible', function () {
//     $this->get(SchoolResource::getUrl('create'))->assertOk();
// });

// test('cannot create a record without required fields', function () {
//     Livewire::test(CreateSchool::class)
//         ->call('create')
//         ->assertHasFormErrors(Arr::dot([
//             'name' => 'required',
//             'address' => [
//                 'street_line_1' => 'required',
//                 'suburb' => 'required',
//                 'state' => 'required',
//                 'postcode' => 'required',
//             ],
//         ]));
// });

// test('cannot create a record with invalid fields', function () {
//     Livewire::test(CreateSchool::class)
//         ->fillForm([
//             'name' => null,
//             'address' => [
//                 'state' => 'not a state',
//                 'postcode' => 'not a postcode',
//             ],
//             'can_be_team_lead' => 'not a boolean',
//         ])
//         ->call('create')
//         ->assertHasFormErrors(Arr::dot([
//             'name' => 'required',
//             'address' => [
//                 'state' => 'in',
//                 'postcode' => 'digits',
//             ],
//             'can_be_team_lead' => 'boolean',
//         ]));
// });

// test('can create a record', function () {
//     $service = Service::factory()
//         ->forResource(Worker::class)
//         ->create();

//     $record = Worker::factory()->make();

//     Livewire::test(CreateWorker::class)
//         ->fillForm([
//             ...$record->toArray(),
//             'services' => [$service->getKey()],
//         ])
//         ->call('create')
//         ->assertHasNoFormErrors();
// });

// test('view page is accessible', function () {
//     $record = Worker::factory()->create();

//     $this->get(WorkerResource::getUrl('view', ['record' => $record]))->assertOk();
// });

// test('view page shows all information', function () {
//     $record = Worker::factory()
//         ->canBeTeamLead()
//         ->create();

//     Livewire::test(ViewWorker::class, ['record' => $record->getRouteKey()])
//         ->assertSee([
//             $record->name,
//             $record->phone,
//             ...$record->services->map->name->all(),
//             $record->address->formatted(),
//             'Yes',
//         ]);
// });

// test('view page has edit action', function () {
//     $record = Worker::factory()->create();

//     Livewire::test(ViewWorker::class, ['record' => $record->getRouteKey()])
//         ->assertActionVisible(EditAction::class);
// });

// test('edit page is accessible', function () {
//     $record = Worker::factory()->create();

//     $this->get(WorkerResource::getUrl('edit', ['record' => $record]))->assertOk();
// });

// test('cannot save a record without required fields', function () {
//     $record = Worker::factory()->create();

//     Livewire::test(EditWorker::class, ['record' => $record->getRouteKey()])
//         ->fillForm([
//             'name' => null,
//             'phone' => null,
//             'address' => [
//                 'street_line_1' => null,
//                 'suburb' => null,
//                 'state' => null,
//                 'postcode' => null,
//             ],
//         ])
//         ->call('save')
//         ->assertHasFormErrors(Arr::dot([
//             'name' => 'required',
//             'phone' => 'required',
//             'address' => [
//                 'street_line_1' => 'required',
//                 'suburb' => 'required',
//                 'state' => 'required',
//                 'postcode' => 'required',
//             ],
//         ]));
// });

// test('cannot save a record with invalid fields', function () {
//     $record = Worker::factory()->create();

//     Livewire::test(EditWorker::class, ['record' => $record->getRouteKey()])
//         ->fillForm([
//             'services' => ['not a service id'],
//             'phone' => 'not a phone',
//             'address' => [
//                 'state' => 'not a state',
//                 'postcode' => 'not a postcode',
//             ],
//             'can_be_team_lead' => 'not a boolean',
//         ])
//         ->call('save')
//         ->assertHasFormErrors(Arr::dot([
//             'services.0' => 'in',
//             'phone' => 'regex',
//             'address' => [
//                 'state' => 'in',
//                 'postcode' => 'digits',
//             ],
//             'can_be_team_lead' => 'boolean',
//         ]));
// });

// test('can save a record', function () {
//     $service = Service::factory()
//         ->forResource(Worker::class)
//         ->create();

//     $record = Worker::factory()->create();

//     $record->services()->attach($service);

//     $newService = Service::factory()
//         ->forResource(Worker::class)
//         ->create();

//     $newRecord = Worker::factory()
//         ->make();

//     Livewire::test(EditWorker::class, ['record' => $record->getRouteKey()])
//         ->fillForm([
//             ...$newRecord->toArray(),
//             'services' => [$newService->getKey()],
//         ])
//         ->call('save')
//         ->assertHasNoFormErrors();

//     $record->refresh();

//     expect($record)->toArray()->toContain(...$newRecord->toArray())
//         ->and($record->services->map->getKey()->all())->toEqual([$newService->getKey()]);
// });

// test('can save a record without changes', function () {
//     $record = Worker::factory()->create();

//     Livewire::test(EditWorker::class, ['record' => $record->getRouteKey()])
//         ->call('save')
//         ->assertHasNoFormErrors();
// });
