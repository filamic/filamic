# Model Test Generator Skill - Implementation Guide

## Overview

This is a production-grade agent skill for generating comprehensive Model Tests for Laravel Master Data models using Pest.

**Key Features:**
- ✅ Automatic discovery of Master Data models (no `belongsTo` relationships)
- ✅ Factory audit and auto-fix based on migrations
- ✅ Generates production-grade Pest tests (AAA pattern)
- ✅ Tests custom logic only (scopes, methods, accessors, mutators, relationships)
- ✅ Full validation with `composer analyse` and `composer test`
- ✅ Zero over-engineering (YAGNI principle)

## Skill Structure

```
model-test-generator/
├── SKILL.md                 # Main skill definition
├── meta.json               # Skill metadata and configuration
├── evals/
│   └── evals.json         # Test cases for evaluating the skill
└── agents/
    ├── executor.md        # How to execute/run the skill
    └── grader.md          # How to grade/evaluate results
```

## File Descriptions

### SKILL.md
The main skill definition. Contains:
- Objective and overview
- Quick start guide
- Key principles
- Complete 5-step workflow
- Test structure templates
- What to test and what NOT to test

### meta.json
Skill metadata including:
- Input/output specifications
- Requirements (Laravel 10+, Pest v3.x)
- Workflow steps
- Best practices
- Common mistakes
- Troubleshooting guide

### evals/evals.json
Evaluation test cases for the skill:
1. Generate tests for simple model with accessor
2. Generate tests for model with scope
3. Generate tests for model with relationship
4. Skip model with no custom logic
5. Identify Master Data models correctly

### agents/executor.md
Instructions for executing the skill:
- Validates environment
- Discovers Master Data models
- Gets user confirmation
- Audits/creates factories
- Generates tests
- Validates with composer commands
- Reports results and metrics

### agents/grader.md
Instructions for grading/evaluating results:
- Grading criteria (6 sections)
- Pass/fail conditions for each
- Weighting system
- Metrics to check
- Common issues to diagnose
- Pass rate thresholds

## How to Use This Skill

### Option 1: Direct Use (Human Interaction)
1. Provide the SKILL.md to an AI agent with access to your Laravel project
2. Agent will:
   - Scan your `app/Models/` directory
   - Identify Master Data models
   - Ask for confirmation
   - Audit/create factories
   - Generate tests
   - Validate everything

**Example prompt to agent:**
```
Use the model-test-generator skill to generate tests for my Laravel project.
Project path: /path/to/my/laravel/app
```

### Option 2: Automated Integration (CI/CD)
1. Add to your CI/CD pipeline
2. Agent runs automatically on model changes
3. Generates tests and validates
4. Reports pass/fail status

### Option 3: Skill Registry
1. Register this skill in your AI agent's skill registry
2. Use in multi-step workflows
3. Combine with other skills for comprehensive testing

## Running Evaluations

To evaluate if the skill works correctly:

```bash
# Using skill-creator to test this skill
# Executor reads SKILL.md + executor.md
# Grader reads grader.md + expectations from evals.json
# Comparator blind-compares results
# Analyzer suggests improvements
```

See evals.json for specific test cases.

## Key Principles (Important!)

✅ **DO:**
- Test custom scopes, methods, accessors, mutators
- Test relationships (count + type)
- Test edge cases first
- Use `make()` for in-memory tests
- Use `create()` for DB-dependent tests
- Each test is independent
- Use `fake()` instead of `$this->faker`
- Follow AAA pattern

❌ **DON'T:**
- Test Laravel's built-in CRUD
- Test framework features (validation, timestamps)
- Create test pollution (shared state)
- Over-engineer with unnecessary tests
- Use `test()` syntax (use `it()`)
- Use `$this->assert*` (use `expect()`)

## Test Generation Output

When the skill generates tests, you get:

```
tests/Feature/Models/
├── UserTest.php
├── ProductTest.php
├── CategoryTest.php
└── SchoolYearTest.php
```

Each test file:
- Uses `it()` and `expect()` syntax
- Organized with `describe()` blocks
- Follows AAA pattern
- Tests edge cases FIRST
- Each test independent
- Uses proper factory methods

## Validation

After generating tests, the skill runs:

```bash
composer analyse    # Static analysis (must pass)
composer test       # All tests (must pass)
```

Both must pass with zero errors/failures.

## Integration with Your Project

Your project needs:
- ✅ Laravel 10+
- ✅ Pest v3.x
- ✅ `app/Models/` directory
- ✅ `database/migrations/` directory
- ✅ `database/factories/` directory
- ✅ `tests/Feature/` directory
- ✅ `composer.json` with Pest dependency

Setup in your `pest.php`:
```php
pest()
    ->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');
```

## Example Generated Test

```php
<?php

namespace Tests\Feature\Models;

use App\Models\SchoolYear;

describe('SchoolYear', function () {
    describe('Accessors', function () {
        it('returns formatted name from years', function () {
            // Arrange
            $schoolYear = SchoolYear::factory()->make([
                'start_year' => 2025,
                'end_year' => 2026,
            ]);

            // Act & Assert
            expect($schoolYear->name)->toBe('2025/2026');
        });
    });

    describe('Scopes', function () {
        it('filters active years', function () {
            // Arrange
            SchoolYear::factory()->active()->create();
            SchoolYear::factory()->inactive()->create();

            // Act
            $active = SchoolYear::active()->get();

            // Assert
            expect($active)->toHaveCount(1);
        });

        it('handles null parameter gracefully', function () {
            // Arrange
            SchoolYear::factory(3)->create();

            // Act & Assert
            expect(fn () => SchoolYear::byYear(null)->get())
                ->not->toThrow();
        });
    });

    describe('Relationships', function () {
        it('has many classes', function () {
            // Arrange
            $schoolYear = SchoolYear::factory()->create();
            SchoolClass::factory(3)->for($schoolYear)->create();

            // Act
            $classes = $schoolYear->classes;

            // Assert
            expect($classes)->toHaveCount(3);
            expect($classes->first())->toBeInstanceOf(SchoolClass::class);
        });
    });
});
```

## Troubleshooting

### "No Master Data models found"
- Check that `app/Models/` exists
- Verify models have custom logic (scopes, methods, etc.)
- Check that models don't have `belongsTo` relationships

### "Factory missing columns"
- Skill will auto-create/update the factory
- Verify migration has all required columns
- Check factory syntax after generation

### "composer test failed"
- Review test failures in transcript
- Check test assertions are correct
- Verify factories create valid data

### "Tests are not isolated"
- Each test should create its own data
- Avoid using `beforeEach()` with mutable state
- Check for hardcoded IDs or assumptions

## Next Steps

1. **Try the skill** on a sample Laravel project
2. **Review generated tests** for quality
3. **Run `composer test`** to verify
4. **Iterate and improve** using the grader.md feedback
5. **Deploy to your workflow** once confident

## Support

For issues or improvements:
- Check `meta.json` troubleshooting section
- Review executor.md for common errors
- Review grader.md to understand evaluation criteria
- Check evals.json for example test cases

---

**Version**: 1.0.0  
**Last Updated**: February 2026  
**Status**: Production Ready
