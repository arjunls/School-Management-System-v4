<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->unique();
            $table->string('graduation_year')->nullable();
            $table->string('final_status')->nullable();
            $table->string('current_occupation')->nullable();
            $table->string('current_company')->nullable();
            $table->string('current_education')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_tracing_data_updated')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('alumni'); }
};
