<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('class_moves', function (Blueprint $table) {
            $table->id(); $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_class_id')->constrained('kelas');
            $table->foreignId('to_class_id')->constrained('kelas');
            $table->string('academic_year'); $table->text('reason')->nullable();
            $table->boolean('is_graduated')->default(false);
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('class_moves'); }
};
