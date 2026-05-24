<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_teaching_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('topic')->nullable();
            $table->text('material')->nullable();
            $table->text('notes')->nullable();
            $table->integer('present_students')->nullable();
            $table->integer('absent_students')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
            $table->unique(['teacher_id', 'class_id', 'subject_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_teaching_logs');
    }
};
