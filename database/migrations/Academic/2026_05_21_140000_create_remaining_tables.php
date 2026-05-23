<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('week_number', 10)->nullable();
            $table->text('topic');
            $table->text('learning_objectives')->nullable();
            $table->text('activities')->nullable();
            $table->text('resources')->nullable();
            $table->text('assessment')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });

        Schema::create('student_disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['achievement', 'violation']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('points')->default(0);
            $table->date('record_date');
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['sick', 'personal', 'vacation', 'other']);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->integer('rating')->unsigned()->comment('1-5');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['teacher_id', 'student_id']);
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 50)->nullable();
            $table->string('serial_number', 100)->nullable()->unique();
            $table->string('location')->nullable();
            $table->integer('total_quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->enum('condition', ['good', 'fair', 'damaged'])->default('good');
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('teacher_evaluations');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('student_disciplines');
        Schema::dropIfExists('syllabuses');
    }
};
