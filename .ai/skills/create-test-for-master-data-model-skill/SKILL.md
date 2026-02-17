---
name: model-test-generator
description: Senior QA Automation Engineer for creating production-grade Model Tests for Laravel Master Data models using latest Pest and AAA pattern. Tests custom logic only (scopes, methods, accessors, mutators, relationships).
---

# Model Test Generator

Automate creation of production-grade Model Tests for "Master Data" models using latest Pest and AAA pattern. Focus on testing custom logic ONLY (scopes, methods, accessors, mutators, relationships) — NOT Laravel's core functionality.

## Overview

This skill helps you generate comprehensive, isolation-aware tests for Laravel Master Data models. It ensures:

- **Clean, readable tests** using Pest's latest `it()` syntax
- **Proper isolation** — each test is independent and can run alone or in any order
- **Smart factory audits** — validates factories match migrations
- **Production-grade code** — follows best practices, no over-engineering (YAGNI)
- **Comprehensive validation** — runs `composer analyse` and `composer test` at the end

## Quick Start

1. Agent scans `app/Models/` to identify Master Data models (no `belongsTo` relationships)
2. Agent audits and fixes factories if needed
3. Agent generates tests for custom logic
4. Agent validates everything with `composer analyse && composer test`
5. Agent reports results with test file paths and command to run tests

## Key Principles

- **Test only what you wrote** — scopes, methods, accessors, mutators, relationships
- **Do NOT test** — Laravel's built-in CRUD, framework features, basic validation
- **Use `make()` for in-memory tests** — accessors, mutators, no DB queries
- **Use `create()` for DB-dependent tests** — scopes, relationships
- **Test edge cases FIRST** — then happy paths
- **Each test is independent** — creates its own data, no test pollution
- **Use `fake()` instead of `$this->faker`** — modern Pest syntax
- **Trust Laravel's RefreshDatabase trait** — auto-rollback after each test

## Skill Workflow

### STEP 1: DISCOVERY & IDENTIFICATION

1. Scan `app/Models/` and `database/migrations/`
2. IDENTIFY "Master Data Models": Models with NO `belongsTo` relationships (self-contained)
3. LIST identified models and ask for confirmation before proceeding

### SKIP CRITERIA

Skip model if:

- Zero custom methods, scopes, accessors, mutators
- Only basic Eloquent relationships with no custom logic

### STEP 2: FACTORY AUDIT

For each confirmed model:

1. Open `database/factories/{Model}Factory.php`
2. If missing/incomplete, CREATE/UPDATE with:
    - All columns have Faker data (no hardcoded nulls except nullable)
    - Unique fields use `sequence()` or `fake()->unique()`
    - Relationships seeded with `factory()`
    - Boolean fields use `fake()->boolean()`
    - Timestamps auto-generated

### STEP 3: GENERATE MODEL TEST

Create: `tests/Feature/Models/{ModelName}Test.php`

#### TEST FILE STRUCTURE:

```php
<?php

namespace Tests\Feature\Models;

use App\Models\ModelName;

describe('ModelName', function () {
    // Tests here
});
```

#### WHAT TO TEST:

**ACCESSORS/MUTATORS:**

- Test value transformation (e.g., name->uppercase)
- Test computed values (e.g., full_name from first_name + last_name)
- Use `make()` (no DB needed)

**SCOPES:**

- Global scopes: Verify they auto-filter
- Local scopes: Test with/without parameters
- Edge case: Test with null parameters
- Use `create()` (scope queries DB)

**RELATIONSHIPS:**

- hasMany/hasOne: Assert count + correct instance type
- belongsToMany: Test pivot data if exists
- morphMany: Verify morph_type and morph_id
- Use `create()` (must persist to DB)

**CUSTOM METHODS:**

- Test return type AND return value
- If queries DB: use `create()`, else use `make()`
- If has side effects: assert state changed

**EDGE CASES (TEST FIRST):**

- Scope with null parameters
- Relationship when parent missing
- Accessor/mutator with invalid input
- Method with unexpected values

#### TEST ISOLATION RULES:

- Each test is independent (can run alone or in any order)
- Each test creates its own data
- Use `beforeEach()` ONLY if multiple tests need identical setup
- Trust Pest's `RefreshDatabase` — auto-rollback after each test

#### CODE STYLE:

- Use `test()` syntax
- Use `expect()` assertions
- Use `fake()` instead of `$this->faker`
- AAA pattern: Arrange → Act → Assert
- Sort: Edge Cases FIRST → Happy Path
- Organize tests in the same order as the model's structure (listed below)
    1. Class Properties (Config: fillable, guarded, casts, visible, hidden)
    2. Boot method
    3. Relationships (all grouped, alphabetical)
    4. Accessors/Mutators (alphabetical by field)
    5. Scopes (alphabetical)
    6. Custom Methods / Business Logic (alphabetical)
    7. Helper Methods (alphabetical, private)

**TEMPLATE:**

```php
it('returns formatted name from years', function () {
    // Arrange
    $model = Model::factory()->make([
        'start_year' => 2025,
        'end_year' => 2026,
    ]);

    // Act & Assert
    expect($model->name)->toBe('2025/2026');
});
```

#### DO NOT TEST:

❌ Laravel's built-in: create(), update(), delete(), find()
❌ Basic relationship existence (but DO test custom logic within relationships)
❌ Framework features (validation, timestamps, etc.)

✅ ONLY test what you wrote

### STEP 4: VALIDATE & RUN TESTS

After generating each model test file:

1. Run static analysis:

```bash
composer analyse
```

2. Run all tests:

```bash
composer test
```

Both commands must pass with zero errors or warnings before considering tests complete.

### STEP 5: OUTPUT

After all validations pass, show:

1. ✅ Generated test file paths
2. 📋 List of all test cases created
3. ✅ `composer analyse` result (must pass)
4. ✅ `composer test` result (must pass)
