# Skill: Create Pest Test

You are writing a Pest test for the Filamic Laravel project. Before writing anything, you MUST read the target model/resource/action file and at least two existing test files that are similar to understand the established pattern.

## Stack

- PHP 8.4, Laravel 12, Filament 4, Pest 4 + pest-plugin-laravel 4
- All tests live in `tests/Feature/`
- `Pest.php` extends `Tests\TestCase`, uses `RefreshDatabase`, scoped to `Feature/`
- `TestCase` provides `loginAdmin()` (sets school context + acts as user) and `login()`

## Rules — Never Break These

1. `declare(strict_types=1)` as the very first line after `<?php` — no exceptions.
2. AAA pattern in every test: `// Arrange`, `// Act`, `// Assert` comments on their own lines.
3. Test descriptions are plain English sentences — no camelCase method names.
4. Use `test()` for most cases. Use `it()` only when the sentence reads better with "it" as subject (e.g. `it('casts the columns')`).
5. Never test the framework — only code we wrote.
6. Order assertions to match the order properties/methods appear in the model/resource file.
7. Never use raw IDs — always use `->getKey()` or `->getRouteKey()` for ULID models.

## Model Test Checklist

For every model test file, cover (in this order):

```
1. Mass assignment guard on `id`
2. Column casts (use ->expect(fn () => Model::factory()->create()) chained form)
3. Accessors / computed attributes
4. Custom scopes (active, inactive, etc.)
5. Trait methods (isActive, isInactive, activateExclusively, deactivateOthers)
6. Relationships (hasMany, belongsTo, hasManyThrough, etc.)
7. Model events / observers (cache clear, student sync, etc.)
8. Static helper methods (getActive, getActiveCacheKey, etc.)
```

### Mass Assignment Guard Pattern

```php
test('it prevents mass assignment to guarded id', function () {
    // ARRANGE
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // ACT
    $model = ModelName::create([
        'id' => $customId,
        // ... required fields
    ]);

    // ASSERT
    expect($model->getKey())
        ->toBeString()
        ->not->toBe($customId);
});
```

### Cast Test Pattern (chained shorthand)

```php
test('it casts the columns')
    ->expect(fn () => ModelName::factory()->create())
    ->column_one->toBeInstanceOf(SomeEnum::class)
    ->column_two->toBeBool()
    ->column_three->toBeInstanceOf(Carbon::class);
```

### Scope Test Pattern

```php
test('active scope only returns active records', function () {
    // Arrange
    $active = ModelName::factory()->active()->create();
    ModelName::factory()->inactive()->create();

    // Act
    $results = ModelName::query()->active()->get();

    // Assert
    expect($results)
        ->toHaveCount(1)
        ->first()->id->toBe($active->getKey());
});
```

### Relationship Isolation Pattern

```php
test('it isolates [children] by [parent]', function () {
    // Arrange
    $parent = Parent::factory()->create();
    $otherParent = Parent::factory()->create();

    $child = Child::factory()->for($parent)->create();
    Child::factory()->for($otherParent)->create();

    // Act
    $children = $parent->children;

    // Assert
    expect($children)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($child->getKey());
});
```

## Filament Resource Test Checklist

For every resource test file, cover (in this order):

```
1. List page accessible
2. List page renders columns (data provider with ->with([...]))
3. List page shows rows
4. Any special column behaviour (descriptions, badges, toggle)
5. Row actions visible (view, edit, delete)
6. Create page accessible
7. Cannot create without required fields
8. Cannot create with invalid data
9. Cannot create with duplicate unique fields
10. Can create a valid record
11. Any special create behaviour (deactivates others, cascades, etc.)
12. View page accessible
13. View page displays all fields
14. Edit page accessible
15. Cannot save with invalid data
16. Can save valid changes
17. Can save without changes (no regression)
18. Any special edit restrictions (disabled fields, guarded updates)
```

### Resource Test Setup

```php
<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\SomeNamespace\SomeResource;
use App\Filament\Admin\Resources\SomeNamespace\Pages\ListSomeModels;
use App\Filament\Admin\Resources\SomeNamespace\Pages\CreateSomeModel;
use App\Filament\Admin\Resources\SomeNamespace\Pages\EditSomeModel;
use App\Filament\Admin\Resources\SomeNamespace\Pages\ViewSomeModel;
use App\Models\SomeModel;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());
```

### List Columns Pattern

```php
test('list page renders columns', function (string $column) {
    // Arrange
    SomeModel::factory()->create();

    // Act & Assert
    Livewire::test(ListSomeModels::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'column_one',
    'column_two',
]);
```

### Create Validation Pattern

```php
test('cannot create a record without required fields', function () {
    Livewire::test(CreateSomeModel::class)
        ->call('create')
        ->assertHasFormErrors([
            'field_name' => 'required',
        ]);
});
```

### Can Create Pattern

```php
test('can create a record', function () {
    // Arrange
    $data = SomeModel::factory()->make()->toArray();

    // Act
    Livewire::test(CreateSomeModel::class)
        ->fillForm($data)
        ->call('create')
        ->assertHasNoFormErrors();

    // Assert
    expect(SomeModel::query()->first())
        ->not->toBeNull()
        ->some_field->toBe($data['some_field']);
});
```

## Action Test Checklist

```
1. Cannot run action when required data is missing
2. Cannot run action when related models don't exist
3. Can run action with valid data
4. Side effects are applied (invoice created, status updated, etc.)
5. Idempotency where expected (running twice is safe)
```

### Action Test Setup

```php
<?php

declare(strict_types=1);

use App\Actions\SomeAction;
use App\Models\SomeModel;

test('cannot [do thing] when [condition]', function () {
    // Arrange
    // ... missing or invalid setup

    // Act & Assert
    expect(fn () => (new SomeAction)->handle(/* args */))
        ->toThrow(SomeException::class);
});
```

## After Writing Tests

Always remind the user to run:

```
composer analyse
composer test
```

Both must pass with zero errors before committing.
