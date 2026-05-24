<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('cooperative_products', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('description')->nullable();
            $table->decimal('price', 15, 2); $table->integer('stock')->default(0);
            $table->string('unit')->nullable(); $table->string('category')->nullable();
            $table->string('image')->nullable(); $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('cooperative_sales', function (Blueprint $table) {
            $table->id(); $table->foreignId('product_id')->constrained('cooperative_products');
            $table->integer('quantity'); $table->decimal('total_price', 15, 2);
            $table->foreignId('buyer_id')->nullable()->constrained('users');
            $table->timestamp('sold_at')->useCurrent();
        });
        Schema::create('cooperative_savings', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained('users');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('type')->default('wajib');
            $table->timestamps();
        });
        Schema::create('cooperative_transactions', function (Blueprint $table) {
            $table->id(); $table->foreignId('saving_id')->constrained('cooperative_savings');
            $table->decimal('amount', 15, 2); $table->string('type')->default('deposit');
            $table->text('description')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cooperative_transactions'); Schema::dropIfExists('cooperative_savings'); Schema::dropIfExists('cooperative_sales'); Schema::dropIfExists('cooperative_products'); }
};
