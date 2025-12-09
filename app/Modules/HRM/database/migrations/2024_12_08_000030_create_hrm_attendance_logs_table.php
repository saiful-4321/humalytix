<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('check_in_method')->nullable(); // biometric, geofence, manual, remote
            $table->string('check_in_location')->nullable();
            $table->string('check_in_ip')->nullable();
            $table->string('check_in_photo')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->string('check_out_method')->nullable();
            $table->string('check_out_location')->nullable();
            $table->string('check_out_ip')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->foreignId('shift_id')->nullable()->constrained('hrm_shifts')->onDelete('set null');
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->decimal('break_hours', 5, 2)->default(0);
            $table->decimal('working_hours', 5, 2)->nullable();
            $table->string('status')->default('present'); // present, absent, half_day, late, on_leave, holiday, weekend
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'date']);
            $table->index(['date', 'status']);
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_attendance_logs');
    }
};
