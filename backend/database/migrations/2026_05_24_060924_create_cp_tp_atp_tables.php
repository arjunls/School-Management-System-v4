<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('cp', function (Blueprint $table) {
            $table->id(); $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('code')->unique(); $table->text('description');
            $table->string('phase')->nullable(); $table->string('class')->nullable(); $table->timestamps();
        });
        Schema::create('tp', function (Blueprint $table) {
            $table->id(); $table->foreignId('cp_id')->constrained('cp')->cascadeOnDelete();
            $table->string('code')->unique(); $table->text('description');
            $table->integer('order')->default(0); $table->timestamps();
        });
        Schema::create('atp', function (Blueprint $table) {
            $table->id(); $table->foreignId('tp_id')->constrained('tp')->cascadeOnDelete();
            $table->text('objective'); $table->text('material')->nullable();
            $table->text('assessment')->nullable(); $table->text('method')->nullable();
            $table->integer('hours')->nullable(); $table->integer('order')->default(0); $table->timestamps();
        });
        Schema::create('teaching_modules', function (Blueprint $table) {
            $table->id(); $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('class_id')->constrained('kelas');
            $table->string('title'); $table->text('content')->nullable();
            $table->string('file_path')->nullable(); $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('teaching_modules'); Schema::dropIfExists('atp');
        Schema::dropIfExists('tp'); Schema::dropIfExists('cp');
    }
};
