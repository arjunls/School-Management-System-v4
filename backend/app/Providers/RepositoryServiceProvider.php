<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // User
        $this->app->bind(
            \App\Modules\StaffManagement\User\Interfaces\UserRepositoryInterface::class,
            \App\Modules\StaffManagement\User\Repositories\UserRepository::class
        );

        // Student
        $this->app->bind(
            \App\Modules\StudentManagement\Student\Interfaces\StudentRepositoryInterface::class,
            \App\Modules\StudentManagement\Student\Repositories\StudentRepository::class
        );

        // Teacher
        $this->app->bind(
            \App\Modules\StaffManagement\Teacher\Interfaces\TeacherRepositoryInterface::class,
            \App\Modules\StaffManagement\Teacher\Repositories\TeacherRepository::class
        );

        // Subject
        $this->app->bind(
            \App\Modules\Academic\Subject\Interfaces\SubjectRepositoryInterface::class,
            \App\Modules\Academic\Subject\Repositories\SubjectRepository::class
        );

        // Schedule
        $this->app->bind(
            \App\Modules\Academic\Schedule\Interfaces\ScheduleRepositoryInterface::class,
            \App\Modules\Academic\Schedule\Repositories\ScheduleRepository::class
        );

        // Grade
        $this->app->bind(
            \App\Modules\Learning\Grade\Interfaces\GradeRepositoryInterface::class,
            \App\Modules\Learning\Grade\Repositories\GradeRepository::class
        );

        // Attendance
        $this->app->bind(
            \App\Modules\StudentManagement\Attendance\Interfaces\AttendanceRepositoryInterface::class,
            \App\Modules\StudentManagement\Attendance\Repositories\AttendanceRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
