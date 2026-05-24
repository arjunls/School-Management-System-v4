<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dormitories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender');
            $table->integer('capacity');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('dormitory_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dormitory_id')->constrained('dormitories')->cascadeOnDelete();
            $table->string('name');
            $table->integer('capacity');
            $table->integer('floor')->nullable();
            $table->timestamps();
        });

        Schema::create('dormitory_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('dormitory_rooms')->cascadeOnDelete();
            $table->foreignId('student_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date')->nullable();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dormitory_assignments');
        Schema::dropIfExists('dormitory_rooms');
        Schema::dropIfExists('dormitories');
    }
};
