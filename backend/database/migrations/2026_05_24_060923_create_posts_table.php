<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->string('slug')->unique();
            $table->text('content')->nullable(); $table->string('excerpt')->nullable();
            $table->string('featured_image')->nullable(); $table->string('category')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedBigInteger('author_id'); $table->timestamps();
        });
        Schema::create('pages', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->string('slug')->unique();
            $table->text('content')->nullable(); $table->boolean('is_published')->default(true);
            $table->integer('order')->default(0); $table->timestamps();
        });
        Schema::create('galleries', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->text('description')->nullable();
            $table->string('image_path'); $table->string('category')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('galleries'); Schema::dropIfExists('pages'); Schema::dropIfExists('posts'); }
};
