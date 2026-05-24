<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('ppdb_periods', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('academic_year');
            $table->date('start_date'); $table->date('end_date');
            $table->integer('quota')->default(0); $table->string('status')->default('draft');
            $table->timestamps();
        });
        Schema::create('ppdb_applicants', function (Blueprint $table) {
            $table->id(); $table->foreignId('period_id')->constrained('ppdb_periods');
            $table->string('registration_number')->unique();
            $table->string('full_name'); $table->string('nisn')->nullable();
            $table->date('birth_date'); $table->string('birth_place')->nullable();
            $table->string('gender')->nullable(); $table->string('religion')->nullable();
            $table->text('address')->nullable(); $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('father_name')->nullable(); $table->string('mother_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('status')->default('registered');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ppdb_applicants'); Schema::dropIfExists('ppdb_periods'); }
};
