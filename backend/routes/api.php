<?php

/*
|--------------------------------------------------------------------------
| API Routes - Hybrid Modular Monolith
|--------------------------------------------------------------------------
|
| Each domain module registers its own routes.
| The Shared Kernel (App\Kernel) provides base classes and cross-cutting concerns.
|
*/

require __DIR__ . '/modules/auth.php';
require __DIR__ . '/modules/dashboard.php';
require __DIR__ . '/modules/academic.php';
require __DIR__ . '/modules/student-management.php';
require __DIR__ . '/modules/staff-management.php';
require __DIR__ . '/modules/learning.php';
require __DIR__ . '/modules/communication.php';
require __DIR__ . '/modules/finance.php';
require __DIR__ . '/modules/library.php';
require __DIR__ . '/modules/student-life.php';
require __DIR__ . '/modules/calendar.php';
require __DIR__ . '/modules/reporting.php';
require __DIR__ . '/modules/upload.php';
require __DIR__ . '/modules/kernel.php';
