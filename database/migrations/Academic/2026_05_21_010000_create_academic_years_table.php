<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->constrained();
            $table->foreignId('term_id')->nullable()->constrained();
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->constrained();
            $table->foreignId('term_id')->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['academic_year_id', 'term_id']);
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['academic_year_id', 'term_id']);
        });

        Schema::dropIfExists('terms');
        Schema::dropIfExists('academic_years');
    }
};
