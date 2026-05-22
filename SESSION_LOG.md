# Session Log — 22 May 2026

## Environment
- OS: Linux (Debian)
- PHP 8.4.21 + MariaDB 11.8.6
- Node.js (latest)
- Backend: Laravel 11 + Sanctum
- Frontend: Next.js 16 + React 19 + TypeScript 5 + Zod + React Hook Form + Zustand + TanStack Query
- Testing: PHPUnit (backend), Vitest 4 + Testing Library (frontend), Playwright (E2E)

## What Was Done

### Bugs Fixed
1. **KelasFactory** — namespace mismatch in factory reference (`Kelas::factory()` → `Kelas::create()`)
2. **AttendanceRecord** — `date` cast removed (format mismatch)
3. **Quiz model** — stale `Classes` import (→ `Kelas`), `Subject` namespace, wrong FK in relationships
4. **Submission model** — `$timestamps = false`
5. **MessageController** — missing `use Message` import, broken `withCount` subquery
6. **Quiz migration** — FK `classes` → `kelas`
7. **Extracurricular migration** — index name `extracurricular_participants_extracurricular_id_student_id_unique` too long (66 chars > MySQL 64 limit), renamed to `ec_participant_unique`

### Testing
- **120 backend tests** pass (20 files, 231 assertions)
- **85 frontend tests** pass (18 files): 13 components, 1 API layer, 1 context, 1 i18n, 1 toast, 1 skeleton, 2 page tests (Login, Students)
- Frontend testing setup: Vitest 4 + Testing Library + jsdom, `vitest.config.ts`, `tests/setup.ts`
- API mock: `src/lib/__mocks__/api.ts` with all 26 named exports
- `window.matchMedia` mocked globally in `tests/setup.ts`

### Code Quality / Architecture
- **29 FormRequest classes** across 15 modules, all extending `ApiFormRequest` base (JSON 422 errors instead of redirect)
- **15 controllers** refactored from inline `Validator::make()` → FormRequest type-hints (~500 lines removed)
- **2 orphaned interfaces** removed (`AcademicYearRepositoryInterface`, `TermRepositoryInterface`) + empty `Interfaces/` dir
- **9 service-level `Validator::make()`** calls left untouched (lower priority — throw `ValidationException` caught by controllers)

### API Documentation (Scribe)
- `@group` docblocks on all 27 controllers
- `@unauthenticated` on auth endpoints
- Generated HTML docs (`/docs`), Postman collection, OpenAPI 3.0.3 spec with Sanctum Bearer auth
- Scribe configured to skip response calls (no live server needed)

### Infrastructure
- **MariaDB 11.8** installed, `school_management` + `school_management_test` databases created
- **`school` user** created with password (not root — auth plugin mismatch)
- **`.env.testing`** — `school` MySQL user, `school_management_test` database
- **`.env.e2e`** — file-based SQLite (so global-setup and webServer share same db)
- **GitHub Actions** (`.github/workflows/tests.yml`) with 3 jobs: backend (MySQL), frontend, e2e (MySQL + Playwright)
- Frontend build passes (25+ routes)

### Database State
- All 49 tables created and seeded
- Users: admin@school.com, teacher@school.com, student@school.com, ahmad@school.com, dewi@school.com, parent@school.com (all password: `password`)
- 3 classes (X A, XI A, XII A), 5 subjects (Mathematics, Science, English, Social Studies, Indonesian)

## Known Issues
1. **PHP built-in server stuck in D state** in this shell environment (1 CPU / 1 GB RAM / 2 GB swap). Likely I/O blocking due to resource constraints. Does NOT affect CI — GitHub Actions runners have 2+ CPU / 7 GB RAM.
2. **E2E Playwright tests not yet executed** — blocked by server issue above.
3. **`.env.e2e` exists** but server couldn't start to validate end-to-end flow.
4. **CI workflow not yet tested** — needs to be pushed to GitHub to trigger.

## Next Steps (for next session)
1. `git push` to GitHub (trigger Actions workflow automatically)
2. If E2E needs local validation: try `php -S 127.0.0.0:8000 -t public/ server.php` or use `npx concurrently` to run backend + frontend
3. Verify GitHub Actions pipeline passes all 3 jobs
4. Optional: Fix 9 remaining service-level `Validator::make()` calls (low priority)
5. Optional: Add remaining frontend page tests
6. Optional: Add API integration tests (Laravel `TestCase` with `RefreshDatabase`)

## Key Commands
```bash
# Backend tests
cd /root/School-management-system/backend && php artisan test

# Frontend tests
cd /root/School-management-system && npm run test        # Vitest
cd /root/School-management-system && npm run test:ui      # Vitest UI

# Migrate + seed
cd /root/School-management-system/backend && php artisan migrate:fresh --force && php artisan db:seed --force

# Generate API docs
cd /root/School-management-system/backend && php artisan scribe:generate

# Start servers
cd /root/School-management-system/backend && php artisan serve --port=8000 &
cd /root/School-management-system && npm run dev &

# E2E tests
cd /root/School-management-system && npx playwright test

# CI check (before push)
cd /root/School-management-system/backend && php artisan test
cd /root/School-management-system && npm run test
```
