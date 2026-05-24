<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('teacher_details', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nip')->nullable()->unique(); $table->string('nuptk')->nullable();
            $table->string('certification')->nullable(); $table->string('education')->nullable();
            $table->string('education_institution')->nullable(); $table->integer('graduation_year')->nullable();
            $table->text('address')->nullable(); $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable(); $table->string('religion')->nullable();
            $table->string('marital_status')->nullable(); $table->string('employment_status')->default('active');
            $table->date('join_date')->nullable(); $table->string('subject_specialization')->nullable();
            $table->timestamps();
        });
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained('users');
            $table->date('date'); $table->time('check_in')->nullable(); $table->time('check_out')->nullable();
            $table->string('status')->default('hadir'); $table->text('notes')->nullable();
            $table->timestamps(); $table->unique(['user_id', 'date']);
        });
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id(); $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('evaluator_id')->constrained('users');
            $table->string('type')->default('pkg'); $table->date('evaluation_date');
            $table->decimal('score', 5, 2)->nullable(); $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('performance_evaluations'); Schema::dropIfExists('teacher_attendances'); Schema::dropIfExists('teacher_details'); }
};
