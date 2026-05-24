# Activity Report — Restrukturisasi Hybrid + Modular Monolith

**Date:** 2026-05-23
**Project:** School Management System v3 (smsv3)

---

## 1. Latar Belakang

Proyek SMSv3 awalnya memiliki struktur Laravel yang cukup modular (28 modul bisnis di `app/Modules/`) namun dengan beberapa kelemahan:

- Semua routes terkumpul di satu file `routes/api.php` (~343 baris)
- Migrasi database flat di `database/migrations/` (27 file)
- Tests flat di `tests/Feature/` (19 file)
- Tidak ada pemisahan shared kernel vs module-specific code
- Tidak ada pengelompokan domain (semua modul di satu level)
- Frontend API service monolitik (`src/lib/api.ts` ~397 baris)

---

## 2. Tujuan

Merestrukturisasi menjadi arsitektur **Hybrid + Modular Monolith** dengan prinsip:

1. **Shared Kernel** — Base classes, cross-cutting concerns (audit, middleware, traits)
2. **Domain-driven Module Groups** — Modules dikelompokkan per domain bisnis
3. **Self-contained Modules** — Setiap modul punya routes, controllers, models, services sendiri
4. **Organized Infrastructure** — Migrations, tests, dan configuration terorganisir per domain

---

## 3. Perubahan Backend

### 3.1 Shared Kernel (`app/Kernel/`)

Base classes dan cross-cutting concerns dipindahkan dari `app/Http/` dan `app/Modules/Audit/` ke namespace `App\Kernel\`:

| Old Path | New Path |
|---|---|
| `app/Http/Controllers/Controller.php` | `app/Kernel/Http/Controllers/Controller.php` |
| `app/Http/Controllers/Traits/ApiResponse.php` | `app/Kernel/Http/Controllers/Traits/ApiResponse.php` |
| `app/Http/Middleware/RoleMiddleware.php` | `app/Kernel/Http/Middleware/RoleMiddleware.php` |
| `app/Modules/Audit/` (all files) | `app/Kernel/Audit/` |
| `app/Notifications/GradeAssigned.php` | `app/Kernel/Notifications/GradeAssigned.php` |

**Autoloading:** `composer.json` updated — `"App\\Kernel\\": "app/Kernel/"`

**Bootstrap:** `bootstrap/app.php` updated — middleware alias `'role'` points to new path.

### 3.2 Domain Module Groups

Modules direstruktur dari flat list menjadi domain groups:

```
app/Modules/
├── Auth/                          # Auth (no change)
├── Academic/                      # AcademicYear, Class, Subject, Schedule
├── StudentManagement/             # Student, Parent, Attendance, Health
├── StaffManagement/               # Teacher, User
├── Learning/                      # Assignment, Quiz, ExamSchedule, Grade
├── Finance/                       # Fee
├── Communication/                 # Announcement, Message, Notification
├── Library/                       # Book, BookLoan (no change)
├── StudentLife/                   # Extracurricular
├── Reporting/                     # Report, Export, Import
├── Calendar/                      # Event (no change)
├── Dashboard/                     # Dashboard (no change)
└── Upload/                        # Upload (no change)
```

Setiap sub-module mempertahankan struktur internalnya:
- `Controllers/`
- `Models/`
- `Services/`
- `Requests/`
- `Repositories/` (if applicable)
- `Interfaces/` (if applicable)

**Cross-module references updated:** Semua `use` import dan inline references di-update ke namespace baru.

### 3.3 Routes per Module

Satu file `routes/api.php` dipecah menjadi 14 file rute per domain:

```
routes/modules/
├── auth.php
├── dashboard.php
├── academic.php
├── student-management.php
├── staff-management.php
├── learning.php
├── communication.php
├── finance.php
├── library.php
├── student-life.php
├── calendar.php
├── reporting.php
├── upload.php
└── kernel.php         # Audit logs (from Kernel)
```

`routes/api.php` sekarang hanya berisi `require` statements yang me-load semua module routes.

### 3.4 Organized Migrations

Migrations dikelompokkan ke subdirektori per domain (sebagai referensi — originals tetap di `database/migrations/`):

```
database/migrations/
├── Auth/
├── Academic/
├── StudentManagement/
├── Learning/
├── Finance/
├── Communication/
├── Library/
├── StudentLife/
├── Calendar/
├── Audit/
└── System/
```

### 3.5 Organized Tests

Tests dipindahkan dari `tests/Feature/` ke folder per domain:

```
tests/Modules/
├── Auth/
├── Academic/
├── StudentManagement/
├── StaffManagement/
├── Learning/
├── Finance/
├── Communication/
├── Library/
├── StudentLife/
├── Calendar/
├── Reporting/
└── Upload/
```

Namespaces di-update dari `Tests\Feature` ke `Tests\Modules`.

### 3.6 Files yang Diupdate

| File | Perubahan |
|---|---|
| `routes/api.php` | Rewrite: now includes module route files |
| `bootstrap/app.php` | Middleware alias path updated |
| `app/Providers/RouteServiceProvider.php` | Simplified |
| `app/Providers/RepositoryServiceProvider.php` | All namespace references updated |
| `app/Models/User.php` | All module references updated |
| `database/seeders/DatabaseSeeder.php` | All module references updated |
| `composer.json` | Added `App\\Kernel\\` autoload entry |
| `tests/Feature/*.php` | Updated module references (kept for backward compat) |

### 3.7 Files yang Dihapus

| File | Keterangan |
|---|---|
| `app/Http/Controllers/Controller.php` | Moved to Kernel |
| `app/Http/Controllers/Traits/ApiResponse.php` | Moved to Kernel |
| `app/Http/Middleware/RoleMiddleware.php` | Moved to Kernel |
| `app/Modules/Audit/` | Moved to Kernel |
| `app/Modules/AcademicYear/` | Moved to Academic/AcademicYear |
| `app/Modules/Class/` | Moved to Academic/Class |
| `app/Modules/Subject/` | Moved to Academic/Subject |
| `app/Modules/Schedule/` | Moved to Academic/Schedule |
| `app/Modules/Student/` | Moved to StudentManagement/Student |
| `app/Modules/Parent/` | Moved to StudentManagement/Parent |
| `app/Modules/Attendance/` | Moved to StudentManagement/Attendance |
| `app/Modules/Health/` | Moved to StudentManagement/Health |
| `app/Modules/Teacher/` | Moved to StaffManagement/Teacher |
| `app/Modules/User/` | Moved to StaffManagement/User |
| `app/Modules/Assignment/` | Moved to Learning/Assignment |
| `app/Modules/Quiz/` | Moved to Learning/Quiz |
| `app/Modules/ExamSchedule/` | Moved to Learning/ExamSchedule |
| `app/Modules/Grade/` | Moved to Learning/Grade |
| `app/Modules/Fee/` | Moved to Finance/Fee |
| `app/Modules/Announcement/` | Moved to Communication/Announcement |
| `app/Modules/Message/` | Moved to Communication/Message |
| `app/Modules/Notification/` | Moved to Communication/Notification |
| `app/Modules/Extracurricular/` | Moved to StudentLife/Extracurricular |
| `app/Modules/Report/` | Moved to Reporting/Report |
| `app/Modules/Export/` | Moved to Reporting/Export |
| `app/Modules/Import/` | Moved to Reporting/Import |

---

## 4. Perubahan Frontend

### 4.1 Modular API Services

`src/lib/api.ts` (~397 baris monolitik) dipecah menjadi:

```
src/lib/domains/
├── client.ts              # Axios instance + interceptors
├── index.ts               # Barrel: re-export semua services
├── auth.ts
├── dashboard.ts
├── academic.ts            # academicYears, classes, subjects, schedules
├── student-management.ts  # students, parents, attendance, health
├── staff-management.ts    # teachers
├── learning.ts            # assignments, quizzes, examSchedules, grades
├── communication.ts       # announcements, messages, notifications
├── finance.ts             # fees
├── library.ts             # books, loans
├── student-life.ts        # extracurriculars
├── calendar.ts            # events
├── reporting.ts           # reports, exports
└── upload.ts
```

`src/lib/api.ts` diubah menjadi backward-compatible re-export barrel:
```ts
export { default } from './domains/client';
export * from './domains';
```

Semua import existing (`import { ... } from '@/lib/api'`) tetap berfungsi.

---

## 5. Dependency Graph

```
Assignment ──→ Academic (Class, Subject)
Attendance ──→ Academic (AcademicYear)
Dashboard ───→ Academic (AcademicYear, Class, Grade, Attendance)
ExamSchedule ─→ Academic (Class, Subject)
Export ──────→ Academic (Class, Subject, Grade, Attendance)
Grade ───────→ Academic (AcademicYear, Subject)
Parent ──────→ Learning (Grade)
Quiz ────────→ Academic (Class, Subject)
Report ──────→ Academic (Subject, Grade, Attendance)
Schedule ────→ Academic (Class, Subject)
```

**Leaf modules** (no outward deps): AcademicYear, Announcement, Auth, Calendar, Class, Extracurricular, Fee, Health, Import, Library, Message, Notification, Student, Subject, Teacher, Upload, User

---

## 6. Catatan Penting

1. **Migrations** tetap di `database/migrations/` untuk kompatibilitas Laravel. Subdirektori hanya untuk referensi.
2. **Tests** di `tests/Feature/` dipertahankan untuk backward compatibility. Tests baru ditambahkan di `tests/Modules/`.
3. **Frontend** membutuhkan `npm install` sebelum bisa di-test (node_modules tidak ada).
4. Setelah pull/clone, jalankan `composer dump-autoload` untuk regenerate autoloader.

---

## 7. Cara Menjalankan

```bash
# Backend
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve

# Frontend  
cd frontend
cp .env.example .env.local
npm install
npm run dev
```

---

*Documentation generated 2026-05-23*
