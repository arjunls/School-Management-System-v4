<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('class_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable();
            $table->enum('type', ['midterm', 'final', 'quiz', 'other'])->default('other');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};
