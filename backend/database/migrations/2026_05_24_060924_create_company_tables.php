<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('field')->nullable();
            $table->text('address')->nullable(); $table->string('phone')->nullable();
            $table->string('email')->nullable(); $table->string('website')->nullable();
            $table->string('contact_person')->nullable(); $table->string('logo')->nullable();
            $table->date('mou_date')->nullable(); $table->date('mou_expiry')->nullable();
            $table->string('status')->default('active'); $table->timestamps();
        });
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title'); $table->text('description')->nullable();
            $table->text('requirements')->nullable(); $table->integer('slots')->default(1);
            $table->date('closing_date')->nullable(); $table->string('status')->default('open');
            $table->timestamps();
        });
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id(); $table->foreignId('vacancy_id')->constrained('job_vacancies')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('applied');
            $table->text('notes')->nullable(); $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('job_applications'); Schema::dropIfExists('job_vacancies');
        Schema::dropIfExists('companies');
    }
};
