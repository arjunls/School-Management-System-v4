<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('code', 10); // R, I, A, S, E, C (Holland/RIASEC)
            $table->string('label'); // Realistic, Investigative, etc.
            $table->integer('score');
            $table->date('test_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('career_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('plan_type'); // study, work, entrepreneur
            $table->string('institution')->nullable(); // university/company name
            $table->string('major')->nullable(); // jurusan
            $table->string('goal')->nullable(); // tujuan
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_plans');
        Schema::dropIfExists('career_interests');
    }
};
