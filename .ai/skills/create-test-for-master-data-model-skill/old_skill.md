---
name: laravel-master-data-model-test-generator
description: "STRICT test generation for Laravel 12 master data models using Pest 4.x. Detects master tables (zero foreign key constraints), generates comprehensive tests for fillable/guarded, nullable fields, and relationships. Output: /tests/Feature/Models/{ModelName}Test.php with exact test count requirements per relationship type."
framework: "Laravel 12.x ONLY"
test_framework: "Pest 4.x ONLY"
language: "PHP 8.2+"
license: "Proprietary"
---

# STRICT Master Data Model Test Generation for Laravel 12 + Pest 4.x

## CRITICAL: Read This First

**This skill is for MASTER DATA MODELS ONLY.**

Master data = Table with ZERO foreign key constraints in the migration file.

If a table has `$table->foreignId()`, it is NOT master data. Skip it.

---

## Step 1: Identify Master Data Models (STRICT RULES)

### Definition

Master data model = Migration file has NO `$table->foreignId()` or `$table->unsignedBigInteger()` columns that reference other tables.

### How to Check

```php
// MASTER DATA (NO foreign keys):
Schema::create('branches', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->string('name')->unique();
    $table->string('phone')->nullable();
    $table->timestamps();
    // ✅ NO foreignId() calls
});

// NOT MASTER DATA (HAS foreign keys):
Schema::create('students', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->string('name');
    $table->foreignId('branch_id')->constrained(); // ❌ Foreign key!
    $table->foreignId('school_id')->constrained();
    $table->timestamps();
});
```

### Master Data Models in Your App

Look at migrations. For each, check:

- `$table->foreignId(...)` → NOT master data
- `$table->constrained(...)` → NOT master data
- No foreign constraints → IS master data

---

## Step 2: Understand Pest 4.x Syntax (Laravel 12)

### Pest 4.x Features

```php
// Function-based tests (NOT class-based)
it('does something', function () {
    // Your test here
});

// Expectations (not assertions)
expect($value)->toBe($expected);
expect($value)->toBeInstanceOf(ClassName::class);
expect(fn() => dangerous())->toThrow(Exception::class);
expect($collection)->toHaveCount(3);
expect($collection)->toBeEmpty();
```

### NOT Pest 4.x (These are OLD):

```php
❌ test('does something') { } // Wrong syntax
❌ $this->assertEquals() // PHPUnit syntax
❌ public function test_name() {} // Class method syntax
```

---

## Step 3: Master Data Test Template (EXACT STRUCTURE)

Every master data test file follows this structure in this exact order:

### Section 1: Fillable/Guarded (2-3 tests)

```php
it('allows mass assignment to fillable attributes', function () {
    // ARRANGE
    $attributes = [
        'name' => 'Test Name',
        'phone' => '021123456',
        'address' => null,
    ];

    // ACT
    $branch = Branch::create($attributes);

    // ASSERT
    expect($branch)
        ->name->toBe('Test Name')
        ->phone->toBe('021123456')
        ->address->toBeNull();
});

it('prevents mass assignment to guarded fields', function () {
    // ARRANGE & ACT - Try to assign guarded field
    $branch = Branch::create([
        'id' => 'custom-id',
        'name' => 'Test',
    ]);

    // ASSERT - Should be auto-generated, not our custom value
    expect($branch->id)->not->toBe('custom-id');
});
```

### Section 2: Nullable Fields (One test per nullable field)

```php
it('stores nullable phone field correctly', function () {
    // Test WITH value
    $with = Branch::create(['name' => 'With Phone', 'phone' => '021123456']);
    expect($with->phone)->toBe('021123456');

    // Test WITH null
    $without = Branch::create(['name' => 'Without Phone', 'phone' => null]);
    expect($without->phone)->toBeNull();
});
```

### Section 3: Unique Constraints (One test per unique field)

```php
it('enforces unique name constraint', function () {
    // ARRANGE - Create first record
    Branch::factory()->create(['name' => 'Jakarta']);

    // ACT & ASSERT - Duplicate should fail
    expect(function () {
        Branch::factory()->create(['name' => 'Jakarta']);
    })->toThrow(\Illuminate\Database\QueryException::class);
});
```

### Section 4: Each Relationship (Minimum tests per relationship type)

#### HasMany Relationships (MINIMUM 5 tests)

```php
// Test 1: Empty collection
it('returns empty collection when has no schools', function () {
    $branch = Branch::factory()->create();
    expect($branch->schools)->toBeEmpty();
});

// Test 2: Returns all related
it('returns all schools belonging to branch', function () {
    $branch = Branch::factory()->create();
    $school1 = School::factory()->for($branch)->create();
    $school2 = School::factory()->for($branch)->create();

    expect($branch->schools)
        ->toHaveCount(2)
        ->pluck('id')->toContain($school1->id, $school2->id);
});

// Test 3: Relationship isolation
it('does not mix schools from different branches', function () {
    $branch1 = Branch::factory()->create();
    $branch2 = Branch::factory()->create();
    School::factory()->for($branch1)->create();
    School::factory()->for($branch2)->create();

    expect($branch1->schools)->toHaveCount(1);
    expect($branch2->schools)->toHaveCount(1);
});

// Test 4: Eager loading works
it('can eager load schools', function () {
    $branch = Branch::factory()->create();
    School::factory(2)->for($branch)->create();

    $loaded = Branch::with('schools')->find($branch->id);
    expect($loaded->relationLoaded('schools'))->toBeTrue();
});

// Test 5: Lazy loading works
it('lazy loads schools when accessed', function () {
    $branch = Branch::factory()->create();
    School::factory()->for($branch)->create();

    $accessed = $branch->schools;
    expect($accessed)->toHaveCount(1);
});
```

#### BelongsToMany Relationships (MINIMUM 6 tests)

```php
// Test 1: Empty collection
it('returns empty collection when branch has no users', function () {
    $branch = Branch::factory()->create();
    expect($branch->users)->toBeEmpty();
});

// Test 2: Attach and retrieve
it('returns all attached users', function () {
    $branch = Branch::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $branch->users()->attach([$user1->id, $user2->id]);
    $branch->load('users');

    expect($branch->users)->toHaveCount(2);
});

// Test 3: Sync replaces
it('syncs users (replaces existing)', function () {
    $branch = Branch::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $branch->users()->attach([$user1->id]);
    $branch->users()->sync([$user2->id]);
    $branch->load('users');

    expect($branch->users)->toHaveCount(1)
        ->first()->id->toBe($user2->id);
});

// Test 4: Detach works
it('detaches users correctly', function () {
    $branch = Branch::factory()->create();
    $user = User::factory()->create();
    $branch->users()->attach($user->id);

    $branch->users()->detach($user->id);
    $branch->load('users');

    expect($branch->users)->toBeEmpty();
});

// Test 5: Isolation (different branches)
it('does not mix users from different branches', function () {
    $branch1 = Branch::factory()->create();
    $branch2 = Branch::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $branch1->users()->attach($user1->id);
    $branch2->users()->attach($user2->id);
    $branch1->load('users');
    $branch2->load('users');

    expect($branch1->users)->toHaveCount(1);
    expect($branch2->users)->toHaveCount(1);
});

// Test 6: Eager loading
it('eager loads users with relation', function () {
    $branch = Branch::factory()->create();
    User::factory(2)->create(); // Create users
    $branch->users()->attach(User::all()->pluck('id')->toArray());

    $loaded = Branch::with('users')->find($branch->id);
    expect($loaded->relationLoaded('users'))->toBeTrue();
});
```

#### HasManyThrough Relationships (MINIMUM 4 tests)

```php
// Test 1: Empty collection
it('returns empty collection when no through relations exist', function () {
    $branch = Branch::factory()->create();
    expect($branch->classrooms)->toBeEmpty();
});

// Test 2: Returns through relations
it('returns classrooms through schools', function () {
    $branch = Branch::factory()->create();
    $school = School::factory()->for($branch)->create();
    $classroom = Classroom::factory()->for($school)->create();

    expect($branch->classrooms)->toHaveCount(1);
});

// Test 3: Multiple intermediates
it('returns classrooms from multiple schools', function () {
    $branch = Branch::factory()->create();
    $school1 = School::factory()->for($branch)->create();
    $school2 = School::factory()->for($branch)->create();
    Classroom::factory()->for($school1)->create();
    Classroom::factory()->for($school2)->create();

    expect($branch->classrooms)->toHaveCount(2);
});

// Test 4: Isolation
it('does not include through relations from other parents', function () {
    $branch1 = Branch::factory()->create();
    $branch2 = Branch::factory()->create();
    $school1 = School::factory()->for($branch1)->create();
    $school2 = School::factory()->for($branch2)->create();
    Classroom::factory()->for($school1)->create();
    Classroom::factory()->for($school2)->create();

    expect($branch1->classrooms)->toHaveCount(1);
    expect($branch2->classrooms)->toHaveCount(1);
});
```

### Section 5: Attribute Persistence (1 test)

```php
it('persists all attributes to database', function () {
    // ARRANGE
    $attributes = [
        'name' => 'Jakarta Branch',
        'phone' => '021123456',
        'address' => 'Jl. Merdeka',
    ];

    // ACT
    $created = Branch::create($attributes);
    $retrieved = Branch::find($created->id);

    // ASSERT - Fresh retrieval has same data
    expect($retrieved)
        ->name->toBe('Jakarta Branch')
        ->phone->toBe('021123456')
        ->address->toBe('Jl. Merdeka');
});
```

---

## Step 4: Test Count Requirements (STRICT)

### Minimum tests per master data model:

- **Fillable/Guarded**: 2 tests
- **Each nullable field**: 1 test
- **Each unique constraint**: 1 test
- **Each HasMany**: 5 tests minimum
- **Each BelongsToMany**: 6 tests minimum
- **Each HasManyThrough**: 4 tests minimum
- **Attribute persistence**: 1 test

### Example: Branch Model

- 2 fillable tests
- 3 nullable field tests (phone, whatsapp, address)
- 1 unique name test
- 5 schools HasMany tests
- 6 users BelongsToMany tests
- 4 classrooms HasManyThrough tests
- 1 persistence test
  = **22 minimum tests**

---

## Step 5: What NOT to Test (CRITICAL)

❌ DO NOT TEST:

- Laravel's automatic timestamp casting
- Framework's ULID/UUID generation
- Validation rules (form/request level)
- Database query internals
- Factory behavior (factory tests are separate)
- Default relationship classes (BelongsTo, HasMany)
- Soft deletes (unless custom logic)
- Model scopes (unless custom behavior)

✅ ONLY TEST:

- Mass assignment (your guarded definition)
- Nullable field behavior (both with and without value)
- Unique constraints (your migration definition)
- Relationship loading and isolation (your model definition)
- Custom accessors/mutators (if they exist)
- Custom methods (if they exist)
- Attribute persistence

---

## Step 6: AAA Pattern (MUST FOLLOW)

Every test must be structured:

```php
it('test name describes behavior', function () {
    // ARRANGE - Set up the world
    $branch = Branch::factory()->create();
    $school = School::factory()->for($branch)->create();

    // ACT - Do the thing being tested
    $result = $branch->schools;

    // ASSERT - Verify the outcome
    expect($result)->toHaveCount(1);
});
```

**No skipping sections. EVER.**

---

## Step 7: Common Pitfalls (AVOID THESE)

### Pitfall 1: Collection Order

```php
❌ WRONG:
->pluck('id')->toEqual($expected->pluck('id'));
// Collections may be in different order

✅ RIGHT:
->pluck('id')->toContain($id1, $id2);
// OR sort before comparing:
$actual = $branch->schools->pluck('id')->sort()->values()->toArray();
expect($actual)->toBe($expected->sort()->values()->toArray());
```

### Pitfall 2: Missing Factory Data

```php
❌ WRONG:
Student::factory()->create(); // Fails if 'name' is required

✅ RIGHT:
Student::factory(['name' => 'John'])->create();
// OR fix StudentFactory to provide 'name' in definition()
```

### Pitfall 3: Testing Framework Code

```php
❌ WRONG:
expect($branch->schools())->toBeInstanceOf(HasMany::class);
// You didn't write HasMany, Laravel did

✅ RIGHT:
expect($branch->schools)->toHaveCount(2);
// Test the outcome, not the framework
```

### Pitfall 4: Not Testing Null in Nullable Fields

```php
❌ WRONG:
it('stores phone field', function () {
    $branch = Branch::create(['phone' => '021123456']);
    expect($branch->phone)->toBe('021123456');
});
// What about when phone is null?

✅ RIGHT:
it('stores nullable phone field correctly', function () {
    $with = Branch::create(['phone' => '021123456']);
    $without = Branch::create(['phone' => null]);

    expect($with->phone)->toBe('021123456');
    expect($without->phone)->toBeNull();
});
```

---

## Step 8: Generate Tests (AI Instruction)

When asking AI to generate tests:

```
STRICT INSTRUCTION:
You are generating Pest 4.x tests for Laravel 12 ONLY.

Model: {ModelName}
Master Data: YES (confirm: no foreign keys in migration)
Relationships: {List each relationship}

GENERATE EXACTLY:
- 2 fillable/guarded tests
- {N} nullable field tests (1 per field: phone, address, etc)
- {N} unique constraint tests (1 per unique column)
- For each HasMany: MINIMUM 5 tests (empty, all, isolation, eager, lazy)
- For each BelongsToMany: MINIMUM 6 tests (empty, attach, sync, detach, isolation, eager)
- For each HasManyThrough: MINIMUM 4 tests (empty, through, multiple, isolation)
- 1 persistence test

Total minimum: {COUNT} tests

USE:
- Pest 4.x function-based syntax ONLY
- expect() for assertions ONLY
- AAA pattern (Arrange / Act / Assert) CLEARLY SEPARATED
- Each test name describes BEHAVIOR (not "test relationship" but "does not mix users from different branches")

DO NOT:
- Test Laravel framework code (timestamps, ULIDs, query builders)
- Test factory behavior
- Use PHPUnit syntax
- Use class-based test syntax
- Test validation rules
- Test model scopes

File location: tests/Feature/Models/{ModelName}Test.php
```

---

## File Template (Copy This)

```php
<?php

declare(strict_types=1);

use App\Models\{ModelName};
use App\Models\{RelatedModel1};
use App\Models\{RelatedModel2};

/**
 * {ModelName} Model Tests
 *
 * {ModelName} is a MASTER DATA model - no foreign key constraints.
 * Tests: Fillable/guarded, nullable fields, unique constraints, relationships.
 */

// ==================== FILLABLE/GUARDED ====================

it('allows mass assignment to fillable attributes', function () {
    // ARRANGE

    // ACT

    // ASSERT
});

it('prevents mass assignment to guarded fields', function () {
    // ARRANGE & ACT

    // ASSERT
});

// ==================== NULLABLE FIELDS ====================

it('stores nullable {field} field correctly', function () {
    // ARRANGE & ACT

    // ASSERT
});

// ==================== UNIQUE CONSTRAINTS ====================

it('enforces unique {field} constraint', function () {
    // ARRANGE

    // ACT & ASSERT
});

// ==================== RELATIONSHIPS: {RelationName} ====================

it('returns empty collection when has no {related}', function () {
    // ARRANGE

    // ACT

    // ASSERT
});

// ... more relationship tests following minimum requirements
```

---

## References (VERIFIED for Laravel 12 + Pest 4.x)

- [Laravel 12 Documentation](https://laravel.com/docs/12)
- [Pest Documentation](https://pestphp.com/docs/)
- [Pest Expectations Reference](https://pestphp.com/docs/expectations)

**DO NOT trust external references. Verify in official docs first.**

---

## Checklist Before Committing Test File

- [ ] File location: `tests/Feature/Models/{ModelName}Test.php`
- [ ] Pest 4.x syntax ONLY (no `test()`, no `$this->`)
- [ ] AAA pattern visible in every test
- [ ] Test count meets minimums (2 fillable + N nullable + N unique + N relationships + 1 persistence)
- [ ] No framework code testing (no timestamps, ULIDs, query builders)
- [ ] No PHPUnit syntax
- [ ] All nullable fields tested BOTH ways (with value AND null)
- [ ] All relationships test isolation (different parent records)
- [ ] All HasMany/HasManyThrough test empty collections
- [ ] All BelongsToMany test attach/sync/detach
- [ ] No factory behavior tests
- [ ] Relationships load without errors (eager + lazy)
- [ ] Unique constraints throw QueryException
- [ ] Attribute persistence verified

---

## Example: Complete Small Master Data Test

```php
<?php

declare(strict_types=1);

use App\Models\Role;

it('allows mass assignment to fillable attributes', function () {
    // ARRANGE
    $attributes = ['name' => 'Admin', 'description' => 'Administrator role'];

    // ACT
    $role = Role::create($attributes);

    // ASSERT
    expect($role)
        ->name->toBe('Admin')
        ->description->toBe('Administrator role');
});

it('prevents mass assignment to guarded id', function () {
    // ARRANGE & ACT
    $role = Role::create(['id' => 999, 'name' => 'User']);

    // ASSERT
    expect($role->id)->not->toBe(999);
});

it('stores nullable description correctly', function () {
    // ARRANGE & ACT
    $with = Role::create(['name' => 'Admin', 'description' => 'Admin role']);
    $without = Role::create(['name' => 'User', 'description' => null]);

    // ASSERT
    expect($with->description)->toBe('Admin role');
    expect($without->description)->toBeNull();
});

it('enforces unique name constraint', function () {
    // ARRANGE
    Role::factory()->create(['name' => 'Admin']);

    // ACT & ASSERT
    expect(function () {
        Role::factory()->create(['name' => 'Admin']);
    })->toThrow(\Illuminate\Database\QueryException::class);
});

it('persists all attributes to database', function () {
    // ARRANGE
    $attributes = ['name' => 'Editor', 'description' => 'Can edit posts'];

    // ACT
    $created = Role::create($attributes);
    $retrieved = Role::find($created->id);

    // ASSERT
    expect($retrieved)
        ->name->toBe('Editor')
        ->description->toBe('Can edit posts');
});
```

That's it. Clear. Strict. No bullshit.
