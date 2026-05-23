<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('frequency', ['once', 'monthly', 'quarterly', 'yearly'])->default('once');
            $table->timestamps();
        });

        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'paid', 'partial', 'overdue'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'transfer', 'cheque', 'other'])->default('cash');
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_invoices');
        Schema::dropIfExists('fee_types');
    }
};
