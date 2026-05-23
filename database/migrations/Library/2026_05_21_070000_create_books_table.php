<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn', 20)->unique();
            $table->string('publisher')->nullable();
            $table->year('published_year')->nullable();
            $table->string('category', 50)->nullable();
            $table->text('description')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('location')->nullable();
            $table->timestamps();
        });

        Schema::create('book_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('returned_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'overdue'])->default('borrowed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_loans');
        Schema::dropIfExists('books');
    }
};
