<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('industry_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->string('cooperation_type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('industry_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('industry_partners')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration_months')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('industry_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('industry_programs')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('mentor_id')->nullable()->constrained('users');
            $table->string('status')->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('industry_students');
        Schema::dropIfExists('industry_programs');
        Schema::dropIfExists('industry_partners');
    }
};
