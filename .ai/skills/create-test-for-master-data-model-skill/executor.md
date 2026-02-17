# Executor: Model Test Generator

This document describes how to execute the Model Test Generator skill.

## Input

The executor receives:
- **laravel_project_path**: Path to the Laravel project root (contains `app/Models`, `database/migrations`, `database/factories`)
- **models_to_test**: Optional array of specific model names to test (if not provided, auto-discover)
- **output_path**: Where to save generated test files (default: `tests/Feature/Models/`)

## Execution Steps

### 1. Validate Environment
- Check that Laravel project structure exists
- Verify `app/Models/` exists
- Verify `database/factories/` exists
- Verify `database/migrations/` exists
- Check `composer.json` has `pestphp/pest` as dependency

### 2. Discovery Phase
- Scan `app/Models/` directory
- For each model:
  - Read the model file
  - Check if it has `belongsTo` relationships
  - Check if it has custom logic (methods, scopes, accessors, mutators)
  - If no `belongsTo` AND has custom logic → add to Master Data Models list

### 3. Get User Confirmation
- Display list of identified Master Data Models
- Ask user: "Proceed with generating tests for these models? (y/n)"
- If no: STOP
- If yes: CONTINUE

### 4. Factory Audit (for each confirmed model)
- Open `database/factories/{Model}Factory.php`
- If missing:
  - Create new factory with:
    - All columns from migration
    - Faker data for each column
    - Unique fields use `sequence()` or `fake()->unique()`
    - Boolean fields use `fake()->boolean()`
    - Save to `database/factories/{Model}Factory.php`
- If exists but incomplete:
  - Compare migration columns vs factory columns
  - Add missing columns with appropriate Faker data
  - Save updated factory

### 5. Generate Tests (for each confirmed model)
- Read the model file completely
- Identify all custom logic:
  - Accessors/Mutators
  - Scopes (global and local)
  - Relationships
  - Custom methods
  - Model events (if any)
- For each custom logic item, create at least one test
- Generate test file at `tests/Feature/Models/{ModelName}Test.php`

**Test Generation Rules:**
- Use `it()` syntax
- Use `expect()` for assertions
- Use `describe()` to organize tests
- Sort tests: Edge Cases FIRST → Happy Path
- Use AAA pattern (Arrange, Act, Assert)
- Use `fake()` instead of `$this->faker`
- Use `make()` for in-memory tests (accessors, mutators)
- Use `create()` for DB-dependent tests (scopes, relationships)

### 6. Validate Tests
- Run: `composer analyse`
  - Must pass with zero errors
  - If fails: STOP, report errors
- Run: `composer test`
  - Must pass with zero failures
  - If fails: STOP, report failures

### 7. Report Results

**Output format:**

```
✅ Model Test Generation Complete

📋 Generated Test Files:
- tests/Feature/Models/{Model1}Test.php (8 tests)
- tests/Feature/Models/{Model2}Test.php (5 tests)
- tests/Feature/Models/{Model3}Test.php (3 tests)

📊 Summary:
- Models scanned: 5
- Master Data models: 3
- Factories created/updated: 2
- Test files generated: 3
- Total tests: 16

✅ Validation Results:
- composer analyse: PASSED
- composer test: PASSED

🚀 Next Steps:
Run individual tests:
  php artisan test tests/Feature/Models/{ModelName}Test.php

Run all model tests:
  php artisan test tests/Feature/Models

Watch mode:
  php artisan test --watch tests/Feature/Models
```

## Error Handling

If any step fails:
1. Report which step failed
2. Show error message
3. Suggest fix
4. STOP (do not continue)

**Common errors:**
- Model file not found → Check app/Models/ directory
- Factory syntax error → Check factory definition
- Test generation failed → Check model structure
- composer analyse failed → Show errors, suggest fixes
- composer test failed → Show test failures, suggest fixes

## Output Files

1. **Generated Test Files**: `tests/Feature/Models/{ModelName}Test.php`
2. **Updated/Created Factories**: `database/factories/{Model}Factory.php`
3. **Execution Transcript**: `transcript.md` (for skill evaluation)

## Execution Metrics to Capture

- **models_scanned**: Count of models in app/Models/
- **master_data_models**: Count identified as Master Data
- **factories_created**: Count of new factories created
- **factories_updated**: Count of existing factories updated
- **test_files_generated**: Count of test files created
- **total_tests**: Sum of all tests across all files
- **composer_analyse_time**: Wall clock time for analyse
- **composer_test_time**: Wall clock time for tests
- **errors**: Any errors that occurred

## Notes for Executor

- **Use `fake()` consistently** — modern Pest syntax requires this
- **Trust RefreshDatabase** — Pest auto-rollback, no manual cleanup needed
- **Each test must be independent** — avoid shared state
- **Test only custom logic** — skip Laravel's built-in features
- **Use proper factory methods** — make() for in-memory, create() for DB
- **Clear assertion messages** — help developers understand failures
- **Organize with describe blocks** — makes tests readable
