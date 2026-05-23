<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('class_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users');
            $table->dateTime('due_date');
            $table->string('attachment_path')->nullable();
            $table->integer('max_score')->default(100);
            $table->timestamps();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('graded_at')->nullable();
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignments');
    }
};
