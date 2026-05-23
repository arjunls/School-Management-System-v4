<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->enum('target_role', ['all', 'admin', 'teacher', 'student', 'parent'])->default('all');
            $table->dateTime('publish_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
