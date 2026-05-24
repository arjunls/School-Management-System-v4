<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tefa_products', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0); $table->integer('stock')->default(0);
            $table->string('unit')->nullable(); $table->string('category')->nullable();
            $table->string('image')->nullable(); $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('tefa_productions', function (Blueprint $table) {
            $table->id(); $table->string('batch_no');
            $table->foreignId('product_id')->constrained('tefa_products');
            $table->integer('quantity'); $table->string('status')->default('planned');
            $table->date('production_date')->nullable(); $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('tefa_sales', function (Blueprint $table) {
            $table->id(); $table->foreignId('product_id')->constrained('tefa_products');
            $table->integer('quantity'); $table->decimal('total_price', 15, 2);
            $table->string('customer_name')->nullable(); $table->date('sale_date');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tefa_sales'); Schema::dropIfExists('tefa_productions'); Schema::dropIfExists('tefa_products'); }
};
