# Grader: Model Test Generator

This document describes how to grade the execution of the Model Test Generator skill.

## Input

The grader receives:
- **transcript.md**: Execution log from executor
- **outputs/**: Directory containing:
  - Generated test files
  - Updated/created factory files
  - metrics.json with execution metrics
- **expectations**: List of expectations to grade against (from evals.json)

## Grading Criteria

### 1. Discovery & Identification
✅ **PASS** if:
- Models scanned correctly from app/Models/
- Master Data models identified (no belongsTo relationships)
- User asked for confirmation before proceeding
- Transcript shows list of identified models

❌ **FAIL** if:
- Models not found in directory
- Identified models have belongsTo relationships (incorrect)
- User not asked for confirmation
- Models with no custom logic included

**Weight**: 15%

### 2. Factory Audit
✅ **PASS** if:
- All factories exist or were created
- Factory has all columns from migration
- Unique fields use sequence() or fake()->unique()
- Boolean fields use fake()->boolean()
- Relationships seeded with factory()
- Faker data used for all non-nullable columns

❌ **FAIL** if:
- Factory missing columns from migration
- Hardcoded null values for nullable columns
- Boolean fields not using proper Faker
- Factory syntax errors
- Missing relationship seeding

**Weight**: 20%

### 3. Test Generation
✅ **PASS** if:
- Test files created at tests/Feature/Models/{Model}Test.php
- Correct namespace (Tests\Feature\Models)
- Use it() syntax (not test())
- Use expect() assertions (not $this->assert*)
- Use describe() blocks to organize tests
- Tests cover: accessors, mutators, scopes, relationships, methods
- At least 1 test per custom logic item
- Tests are readable and follow AAA pattern

❌ **FAIL** if:
- Test files in wrong location
- Wrong namespace used
- Using old Pest syntax (test() instead of it())
- Using old assertion style ($this->assertTrue instead of expect())
- No describe blocks
- Missing test coverage for custom logic
- Tests not following AAA pattern
- Unclear test names

**Weight**: 30%

### 4. Test Isolation
✅ **PASS** if:
- Each test creates its own data
- No shared state between tests
- beforeEach() used only for shared setup
- Tests can run in any order
- No hardcoded IDs or assumptions about data

❌ **FAIL** if:
- Tests depend on previous test's data
- Shared mutable variables
- Tests fail when run in different order
- Hardcoded IDs or data values
- Test pollution detected

**Weight**: 15%

### 5. Code Style
✅ **PASS** if:
- Uses fake() instead of $this->faker
- Uses make() for in-memory tests (accessors, mutators)
- Uses create() for DB-dependent tests (scopes, relationships)
- Edge cases tested FIRST, then happy paths
- Clear assertion messages
- Proper use of fake data

❌ **FAIL** if:
- Uses $this->faker
- Incorrect factory method usage (make vs create)
- Happy paths tested before edge cases
- Unclear assertion messages
- Hardcoded data instead of Faker

**Weight**: 10%

### 6. Validation
✅ **PASS** if:
- composer analyse runs with zero errors
- composer test runs with all tests passing
- Execution transcript shows both commands passing
- metrics.json shows success

❌ **FAIL** if:
- composer analyse has errors
- composer test has failures
- Transcript shows errors
- Commands did not run or failed

**Weight**: 10%

## Grading Process

1. **Review Transcript**
   - Check discovery phase messages
   - Check factory audit results
   - Check test generation output
   - Check validation results

2. **Examine Generated Files**
   - Open each test file
   - Check syntax is correct
   - Check test coverage
   - Check test organization
   - Check assertion clarity

3. **Review Metrics**
   - Check models_scanned > 0
   - Check master_data_models > 0
   - Check test_files_generated > 0
   - Check total_tests > 0
   - Check composer_analyse_time > 0
   - Check composer_test_time > 0

4. **Run Checklist Against Expectations**
   - For each expectation from evals.json
   - Check if PASS or FAIL
   - Record evidence (line from transcript, file snippet)
   - Calculate pass rate

5. **Summarize Results**

```json
{
  "evaluation": {
    "eval_name": "...",
    "total_expectations": 8,
    "passed": 7,
    "failed": 1,
    "pass_rate": 87.5,
    "sections": {
      "discovery": {
        "status": "PASS",
        "weight": 15,
        "notes": "..."
      },
      "factory_audit": {
        "status": "PASS",
        "weight": 20,
        "notes": "..."
      },
      "test_generation": {
        "status": "PASS",
        "weight": 30,
        "notes": "..."
      },
      "test_isolation": {
        "status": "PASS",
        "weight": 15,
        "notes": "..."
      },
      "code_style": {
        "status": "PASS",
        "weight": 10,
        "notes": "..."
      },
      "validation": {
        "status": "FAIL",
        "weight": 10,
        "notes": "composer test failed: test_returns_full_name",
        "evidence": "Expected 'John Doe' but got 'john doe'"
      }
    },
    "weighted_score": 89.3
  }
}
```

## Common Issues to Check

### Test File Not Generated
**Evidence**: Test file missing from outputs/
**Cause**: Model identified as no custom logic when it should have been tested
**Fix**: Re-examine model for custom logic

### Wrong Factory Method Used
**Evidence**: Test uses make() for scope test (should be create())
**Cause**: Executor didn't distinguish between in-memory vs DB tests
**Fix**: Verify executor logic for factory method selection

### Tests Not Isolated
**Evidence**: Test fails when run alone, passes in suite
**Cause**: Test depends on previous test's data
**Fix**: Ensure each test creates own data

### Validation Failed
**Evidence**: composer analyse or composer test failed
**Cause**: Generated code has syntax errors or test failures
**Fix**: Check error messages in transcript, fix code generation logic

### Missing Test Coverage
**Evidence**: Model has 5 custom methods but only 2 tests
**Cause**: Executor didn't discover all custom logic
**Fix**: Improve code parsing to find all methods/scopes/accessors

## Grading Notes

- **Pass rate >= 90%**: Skill is production-ready
- **Pass rate 80-89%**: Skill is functional but needs iteration
- **Pass rate < 80%**: Skill needs significant improvement
- Always review user_notes from executor — may reveal issues that passed expectations miss
- Check metrics for anomalies (e.g., 0 tests generated when should be > 0)
