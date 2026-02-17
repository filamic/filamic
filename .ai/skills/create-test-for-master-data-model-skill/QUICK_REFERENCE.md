# Model Test Generator - Quick Reference

## Skill in 30 Seconds

**What it does:** Generates production-grade Pest tests for Laravel Master Data models (models without `belongsTo`). Tests custom logic only (scopes, methods, accessors, mutators, relationships).

**What you get:**
- Discovered Master Data models ✅
- Audited/fixed factories ✅
- Generated test files ✅
- Validated with `composer analyse && composer test` ✅

**No bloat:** Only tests what you wrote, not Laravel's core.

---

## The 5 Steps

### Step 1: Discovery
- Scans `app/Models/`
- Identifies Master Data (no `belongsTo`)
- Asks for confirmation

### Step 2: Factory Audit
- Opens `database/factories/{Model}Factory.php`
- Creates/updates if missing/incomplete
- Ensures all columns have Faker data

### Step 3: Generate Tests
- Creates `tests/Feature/Models/{ModelName}Test.php`
- Tests: accessors, mutators, scopes, relationships, methods
- Edge cases FIRST, then happy paths
- Uses: `it()`, `expect()`, `fake()`, AAA pattern

### Step 4: Validate
```bash
composer analyse  # Must pass
composer test     # Must pass
```

### Step 5: Report
- Shows generated file paths
- Lists all test cases
- Shows validation results

---

## What to Test (The Rules)

### ✅ TEST THESE:
- **Accessors/Mutators**: Value transformation, computed values
- **Scopes**: Global + local, with/without parameters, edge cases
- **Relationships**: Count, instance type, pivot data
- **Custom Methods**: Return type, return value, side effects
- **Edge Cases**: null parameters, missing data, wrong types

### ❌ DON'T TEST THESE:
- Laravel's CRUD: create(), update(), delete(), find()
- Framework features: validation, timestamps, casting (unless custom)
- Basic relationships with no logic
- Anything Laravel already handles

---

## Factory Usage (Critical!)

| Use | Method | Why |
|-----|--------|-----|
| Accessor/Mutator test | `make()` | No DB needed |
| Scope test | `create()` | Scope queries DB |
| Relationship test | `create()` | Must persist |
| Custom method (no DB) | `make()` | Fast, in-memory |
| Custom method (with DB) | `create()` | Needs DB access |

---

## Test Structure (Template)

```php
<?php

namespace Tests\Feature\Models;

use App\Models\ModelName;

describe('ModelName', function () {
    describe('Accessors', function () {
        it('returns formatted name', function () {
            // Arrange
            $model = Model::factory()->make([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]);

            // Act & Assert
            expect($model->fullName)->toBe('John Doe');
        });
    });

    describe('Scopes', function () {
        it('filters active records', function () {
            // Arrange
            Model::factory(3)->active()->create();
            Model::factory(2)->inactive()->create();

            // Act
            $active = Model::active()->get();

            // Assert
            expect($active)->toHaveCount(3);
        });

        it('handles null parameter', function () {
            Model::factory(5)->create();
            
            expect(fn () => Model::byYear(null)->get())
                ->not->toThrow();
        });
    });

    describe('Relationships', function () {
        it('has many related records', function () {
            // Arrange
            $parent = Parent::factory()->create();
            Child::factory(3)->for($parent)->create();

            // Act
            $children = $parent->children;

            // Assert
            expect($children)->toHaveCount(3);
            expect($children->first())->toBeInstanceOf(Child::class);
        });
    });
});
```

---

## Code Style Checklist

✅ Use `it()` not `test()`  
✅ Use `expect()` not `$this->assert*`  
✅ Use `fake()` not `$this->faker`  
✅ Use `describe()` to organize  
✅ Edge cases FIRST  
✅ AAA pattern (Arrange, Act, Assert)  
✅ Each test independent  
✅ make() for in-memory, create() for DB  
✅ Clear test names  
✅ Clear assertions  

---

## Common Mistakes (Avoid These!)

❌ Testing Laravel's built-in functionality  
❌ Creating test pollution (shared state)  
❌ Using `make()` for scope tests (use `create()`)  
❌ Using `$this->faker` (use `fake()`)  
❌ Not organizing with `describe()`  
❌ Testing happy paths without edge cases  
❌ Over-engineering with 50+ tests per model  
❌ Hardcoding data instead of using Faker  
❌ Tests that fail in different order  

---

## Pest Syntax Cheat Sheet

### Test Definition
```php
it('does something', function () { ... });
```

### Assertions
```php
expect($value)->toBe(true);
expect($collection)->toHaveCount(3);
expect($model)->toBeInstanceOf(Model::class);
expect(fn () => doSomething())->not->toThrow();
expect($value)->toEqual($expected)->and($other)->toBe(true);
```

### Factory Methods
```php
$model = Model::factory()->make();           // In-memory
$model = Model::factory()->create();         // Persisted
$models = Model::factory(5)->create();       // Multiple
$model = Model::factory()->active()->create(); // With state
$model = Model::factory()->for($parent)->create(); // With relation
```

### Faker Data
```php
fake()->name()
fake()->email()
fake()->boolean()
fake()->unique()->name()
fake()->numberBetween(1, 100)
fake()->word()
```

### Organization
```php
describe('ModelName', function () {
    describe('Accessors', function () {
        it('test 1', function () { ... });
    });
    
    describe('Scopes', function () {
        it('test 2', function () { ... });
    });
});
```

---

## Validation Commands

```bash
# Check for errors (static analysis)
composer analyse

# Run all tests
composer test

# Run specific test file
php artisan test tests/Feature/Models/UserTest.php

# Watch mode (auto-rerun on file change)
php artisan test --watch tests/Feature/Models

# With coverage
php artisan test --coverage tests/Feature/Models
```

---

## Expected Output

After skill completes:

```
✅ Model Test Generation Complete

📋 Generated Test Files:
- tests/Feature/Models/UserTest.php (8 tests)
- tests/Feature/Models/ProductTest.php (5 tests)

📊 Summary:
- Models scanned: 5
- Master Data models: 2
- Factories created/updated: 1
- Test files generated: 2
- Total tests: 13

✅ Validation Results:
- composer analyse: PASSED
- composer test: PASSED

🚀 Run: php artisan test tests/Feature/Models
```

---

## Troubleshooting Quick Answers

**Q: No models found?**  
A: Check `app/Models/` exists, models have custom logic

**Q: Factory error?**  
A: Skill auto-fixes, check migration has all columns

**Q: Test failed?**  
A: Check factory creates valid data, assertions are correct

**Q: Tests not isolated?**  
A: Each test must create own data, no shared state

**Q: composer analyse failed?**  
A: Fix static analysis errors, re-run skill

---

## Files Generated

- `tests/Feature/Models/{Model}Test.php` ← Your tests
- `database/factories/{Model}Factory.php` ← Updated/created factories
- All tests pass `composer analyse && composer test`

---

**That's it!** The skill handles the rest.
