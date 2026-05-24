<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('description')->nullable(); $table->timestamps();
        });
        Schema::create('assets', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('code')->nullable()->unique();
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->text('description')->nullable(); $table->string('location')->nullable();
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->date('purchase_date')->nullable(); $table->string('condition')->default('good');
            $table->string('status')->default('available');
            $table->string('image')->nullable(); $table->timestamps();
        });
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->id(); $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('borrower_id')->constrained('users');
            $table->date('borrow_date'); $table->date('return_date')->nullable();
            $table->text('purpose')->nullable(); $table->string('status')->default('borrowed');
            $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('consumables', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('unit')->nullable();
            $table->integer('stock')->default(0); $table->integer('min_stock')->default(0);
            $table->string('category')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('consumables'); Schema::dropIfExists('asset_loans'); Schema::dropIfExists('assets'); Schema::dropIfExists('asset_categories'); }
};
