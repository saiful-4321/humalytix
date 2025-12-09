<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_log_id')->constrained('hrm_attendance_logs')->onDelete('cascade');
            $table->time('break_start');
            $table->time('break_end')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('break_type')->default('other'); // lunch, tea, prayer, other
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('attendance_log_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_breaks');
    }
};
