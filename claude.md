# CLAUDE.md

## Auto-Detect Stack (do this first, every session)

Read these files before anything else, every session:

- `composer.json` → PHP, Laravel, Filament versions and all packages
- `package.json` → Tailwind, Alpine, Vite versions
- `composer.lock` → Confirm exact installed versions

Never assume versions. Always verify from these files.

## Workflow — No Skipping, Ever

1. Read all relevant files before writing anything. List what you read.
2. Create or update `todo/YYYY-MM-DD.md` before starting any task.
3. Show plan and wait for approval before any major change.
4. One feature = one branch. Rebase on `main` before PR. Resolve all conflicts.
5. Commit small and often. Conventional commits: `feat:` `fix:` `refactor:` `test:` `chore:`
6. Write Pest tests for everything we write.
7. Before marking anything done, run:
    - `composer analyse` → IDE helper models refresh + Pint formatting + PHPStan analysis
    - `composer test` → clears config + runs full Pest suite
    - Both must pass with zero errors before committing.
8. GitHub Actions must be green before requesting review.

## Code Rules

- Simplicity over cleverness. If it needs a comment to explain what it does, rewrite it.
- Readability over convenience, always.
- No raw SQL — Eloquent or Query Builder only.
- `declare(strict_types=1)` on every PHP file.
- Business logic lives in Actions only. Never in controllers, Livewire components, or Filament resources.
- One Action = one responsibility. Never let an Action do two things.
- Livewire components: one responsibility. If it's growing, split it.
- No N+1 queries. Eager load always. Verify before committing.
- No hardcoded strings — use config, enums, or constants.
- Validate at Form Request layer.
- Authorization via Policies only. Never inline role checks.
- Mass assignment: always `$guarded`.
- Models use ULID as primary key — never expose or assume integer IDs.
- Never log sensitive user data.
- Never install a package without asking first and explaining why.

## Architecture

- `app/Actions/` — all business logic, one class one job
- `app/Enums/` — all enums, backed enums preferred
- `app/Filament/` — resources and pages only, zero business logic
- Livewire: `app/Livewire/`, views: `resources/views/livewire/`
- No `app/Services/` — if you're tempted to create one, use an Action instead

## Action Pattern

- Each Action is a single-purpose class with a `handle()` method
- Actions are invokable or have a clear `handle()` — be consistent, check existing Actions first
- Actions can call other Actions — never duplicate logic
- Name Actions clearly by what they do: `CreateUser`, `PublishPost`, `SendInvoice`
- Read existing Actions in `app/Actions/` before creating new ones to match the style

## Testing (Pest)

- Never test the framework — only code we wrote
- Test every Action, model scope, relationship, and custom method
- Model tests: order assertions to match model property/method declaration order
- Filament resource tests: order assertions to match resource field/action declaration order
- Actions tested in isolation — mock dependencies
- Feature tests for full HTTP flows
- Before writing any test, read existing tests in `tests/` to match the project's style

## Static Analysis & Formatting

- PHPStan is the source of truth — zero errors required
- Pint handles formatting — never manually format, let Pint do it
- IDE helper keeps model docblocks up to date — always runs via `composer analyse`
- If PHPStan flags something, fix the root cause — never suppress with `@phpstan-ignore` unless absolutely necessary and always leave a comment explaining why

## Security Baseline

- CSRF enabled everywhere
- Policies on every model
- All user input validated and sanitized
- No sensitive data in logs
- Mass assignment protected via `$guarded`

## What You Must Never Do

- Never speculate about code you haven't opened and read
- Never change more than what's asked — surgical changes only
- Never skip the plan step, even for "small" tasks
- Never assume a package API — check the installed version from composer.json
- Never suppress PHPStan errors without a written reason
- When unsure, ask. Don't guess.
