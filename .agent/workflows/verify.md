---
description: Run codebase analysis and tests to ensure quality
---

This workflow should be run after completing any significant feature, refactor, or fix to ensure no regressions and maintain code quality.

// turbo-all

1. Run static analysis and formatting

```bash
composer analyse
```

2. Run the test suite

```bash
composer test
```
