<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transportation_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('pickup_point');
            $table->string('dropoff_point');
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('transportation_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plate_number');
            $table->integer('capacity');
            $table->string('driver_name');
            $table->string('driver_phone');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('transportation_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('transportation_routes')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('transportation_vehicles')->nullOnDelete();
            $table->foreignId('student_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('pickup_point');
            $table->string('dropoff_point');
            $table->decimal('fee', 10, 2)->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transportation_students');
        Schema::dropIfExists('transportation_vehicles');
        Schema::dropIfExists('transportation_routes');
    }
};
