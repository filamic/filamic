# Project Roadmap & TODO

## 🚀 Priority: High (Core Systems & Readiness)

- [ ] **Middleware Readiness Check**: Implement middleware to ensure application is ready (SchoolYear and SchoolTerm must be active) before allowing access to core features. <!-- id: 1 -->
- [ ] **Pre-use Setup Page**: Create a landing page for when no active academic period exists to guide admins through initial setup. <!-- id: 2 -->
- [ ] **Student Active Status Consisitency**: Ensure `$student->is_active` remains perfectly in sync with `student_enrollment` (Source of Truth) using database transactions. <!-- id: 3 -->
- [ ] **Scope Hardening**: Use `qualifyColumn()` for all custom scopes to prevent ambiguity in joins (see `eligibleForMonthlyFee` for reference). <!-- id: 4 -->

## 💰 Finance & Invoicing

- [x] SPP Invoice Creation Flow <!-- id: 5 -->
- [x] Penalty (Denda) logic fix <!-- id: 6 -->
- [ ] **Invoice Scheduling**: Feature for automated scheduled invoice generation. <!-- id: 7 -->
- [ ] **Bulk Fee Updates**: Feature to update all unpaid invoices when a fee nominal is changed (with user confirmation). <!-- id: 8 -->
- [ ] **Print Invoices**: Implement document printing/PDF for invoices. <!-- id: 9 -->
- [ ] **Book Fees Logic**: Implement logic for class-specific book fees (e.g., class 6/3-SMP/3-SMA might have 0 book fees). <!-- id: 10 -->
- [ ] **Import/Export**:
    - [ ] Import students (with enrollment & payment accounts) <!-- id: 11 -->
    - [ ] Export students <!-- id: 12 -->
    - [ ] Export invoices (SPP & Books) <!-- id: 13 -->
    - [ ] Import payments (SPP & Books) <!-- id: 14 -->

## 🧪 Testing & Quality Assurance

- [x] **Hardened Multi-tenancy**: Global `afterEach` and `tearDown` context cleaning. <!-- id: 15 -->
- [x] **AAA Pattern**: All tests must follow Arrange, Act, Assert. <!-- id: 16 -->
- [x] **Master Data Progress**:
    - [x] Branch (Model & Resource) <!-- id: 17 -->
    - [x] School Year (Model & Resource) <!-- id: 18 -->
    - [x] School Term (Model & Resource) <!-- id: 19 -->
    - [x] Position (Model & Resource) <!-- id: 20 -->
- [ ] **Backlog**:
    - [ ] Test `HasActiveStateTrait` exclusively (standalone trait test). <!-- id: 21 -->
    - [ ] Subject & SubjectCategory (Deep dive resource tests). <!-- id: 22 -->
    - [ ] Classroom (Deep dive resource tests). <!-- id: 23 -->
    - [ ] Implement & Test future master models: `Religion`, `Extracurricular`, `ViolationType`, `Spp`. <!-- id: 24 -->

## 🛠️ Refactoring & Performance

- [ ] **Academic Period Caching**: Cache `SchoolYear` and `SchoolTerm` "active" records as they rarely change. <!-- id: 25 -->
- [ ] **Fix Denda Bug**: Resolve validation error (max:255 string on numeric value) in penalty calculation. <!-- id: 26 -->
- [ ] **Bulk Optimization**: Refactor `syncActiveStatus()` to avoid N+1 queries during bulk operations (consider `Model::withoutEvents()`). <!-- id: 27 -->
- [ ] **History Logs**: Admin only allowed to edit last student record; others must be history records. <!-- id: 28 -->

---

## 📖 Definition of Done & Rules

### Student Active Status

- A student is **Active** if:
    - Enrollment status is `ENROLLED` for the current active `SchoolYear` AND `SchoolTerm`.
    - `school_id` is NOT null.
- A student is **Inactive** if:
    - Status is manually set to inactive.
    - `school_id` is NULL.

### Active Payment Account

- Derived from the student's active enrollment class.
- Matches `classroom_id` from the active enrollment.

### Promotion Flow (Kenaikan Kelas)

1. Admin creates a `DRAFT` enrollment for the next year.
2. Finalization updates `DRAFT` to `ENROLLED` in a transaction.

### Invoicing Rules

- Invoices are generated ONLY for students active in the current year.
- Virtual Account (VA) numbers must be updated whenever specific invoice dates change.
- Book fees are optional and may be null for graduating classes.
