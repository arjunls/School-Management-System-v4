<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('p5_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('theme')->nullable();
            $table->string('dimension')->nullable();
            $table->unsignedBigInteger('class_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('planned');
            $table->unsignedBigInteger('coordinator_id')->nullable();
            $table->timestamps();
        });
        Schema::create('p5_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p5_project_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->string('location')->nullable();
            $table->string('documentation')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('p5_activities');
        Schema::dropIfExists('p5_projects');
    }
};
