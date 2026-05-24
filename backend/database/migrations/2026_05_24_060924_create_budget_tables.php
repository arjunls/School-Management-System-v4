<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('budget_categories', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('source')->nullable();
            $table->text('description')->nullable(); $table->timestamps();
        });
        Schema::create('budgets', function (Blueprint $table) {
            $table->id(); $table->foreignId('category_id')->constrained('budget_categories');
            $table->string('name'); $table->text('description')->nullable();
            $table->decimal('planned_amount', 15, 2); $table->decimal('realized_amount', 15, 2)->default(0);
            $table->string('period'); $table->string('status')->default('planned');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('budgets'); Schema::dropIfExists('budget_categories'); }
};
