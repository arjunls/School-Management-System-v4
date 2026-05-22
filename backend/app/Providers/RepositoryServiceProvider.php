<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // User
        $this->app->bind(
            \App\Modules\User\Interfaces\UserRepositoryInterface::class,
            \App\Modules\User\Repositories\UserRepository::class
        );

        // Student
        $this->app->bind(
            \App\Modules\Student\Interfaces\StudentRepositoryInterface::class,
            \App\Modules\Student\Repositories\StudentRepository::class
        );

        // Teacher
        $this->app->bind(
            \App\Modules\Teacher\Interfaces\TeacherRepositoryInterface::class,
            \App\Modules\Teacher\Repositories\TeacherRepository::class
        );

        // Subject
        $this->app->bind(
            \App\Modules\Subject\Interfaces\SubjectRepositoryInterface::class,
            \App\Modules\Subject\Repositories\SubjectRepository::class
        );

        // Schedule
        $this->app->bind(
            \App\Modules\Schedule\Interfaces\ScheduleRepositoryInterface::class,
            \App\Modules\Schedule\Repositories\ScheduleRepository::class
        );

        // Grade
        $this->app->bind(
            \App\Modules\Grade\Interfaces\GradeRepositoryInterface::class,
            \App\Modules\Grade\Repositories\GradeRepository::class
        );

        // Attendance
        $this->app->bind(
            \App\Modules\Attendance\Interfaces\AttendanceRepositoryInterface::class,
            \App\Modules\Attendance\Repositories\AttendanceRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
