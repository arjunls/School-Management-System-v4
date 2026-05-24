<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('dapodik_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sync_type');
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('records_processed')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('dapodik_sync_logs'); }
};
