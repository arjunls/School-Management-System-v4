<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('coach', 100)->nullable();
            $table->string('day', 20)->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->integer('max_participants')->nullable();
            $table->timestamps();
        });

        Schema::create('extracurricular_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->date('joined_at');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['extracurricular_id', 'student_id'], 'ec_participant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extracurricular_participants');
        Schema::dropIfExists('extracurriculars');
    }
};
