<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('certification_schemas', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('field')->nullable();
            $table->text('description')->nullable(); $table->string('level')->nullable();
            $table->timestamps();
        });
        Schema::create('certifications', function (Blueprint $table) {
            $table->id(); $table->foreignId('schema_id')->constrained('certification_schemas');
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('assessor_id')->nullable()->constrained('users');
            $table->date('exam_date')->nullable(); $table->string('result')->nullable();
            $table->string('certificate_number')->nullable(); $table->string('status')->default('scheduled');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('certifications'); Schema::dropIfExists('certification_schemas'); }
};
