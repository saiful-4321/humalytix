<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // OT Policies (Overtime Rules)
        Schema::create('hrm_ot_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('calculation_basis', ['hourly_rate', 'daily_rate', 'monthly_rate'])->default('hourly_rate');
            $table->decimal('multiplier', 5, 2)->default(1.5); // 1.5x for regular OT
            $table->decimal('weekend_multiplier', 5, 2)->default(2.0); // 2x for weekend
            $table->decimal('holiday_multiplier', 5, 2)->default(2.5); // 2.5x for holidays
            $table->decimal('night_shift_multiplier', 5, 2)->default(1.25); // 1.25x for night shift
            $table->time('night_shift_start')->default('22:00:00');
            $table->time('night_shift_end')->default('06:00:00');
            $table->integer('min_ot_minutes')->default(30); // Minimum OT to qualify
            $table->integer('max_ot_hours_per_day')->nullable();
            $table->integer('max_ot_hours_per_month')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Employee OT Records
        Schema::create('hrm_employee_overtime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('ot_policy_id')->nullable()->constrained('hrm_ot_policies')->nullOnDelete();
            $table->date('ot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('total_minutes');
            $table->decimal('total_hours', 5, 2);
            $table->enum('ot_type', ['regular', 'weekend', 'holiday', 'night_shift'])->default('regular');
            $table->decimal('multiplier', 5, 2)->default(1.5);
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('ot_amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('payroll_id')->nullable()->constrained('hrm_payrolls')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Shift Differential Rules
        Schema::create('hrm_shift_differentials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('shift_type', ['morning', 'afternoon', 'evening', 'night', 'graveyard', 'rotating'])->default('night');
            $table->time('shift_start');
            $table->time('shift_end');
            $table->decimal('rate_multiplier', 5, 2)->default(1.0); // Additional pay multiplier
            $table->decimal('fixed_allowance', 10, 2)->default(0); // Or fixed amount
            $table->enum('calculation_type', ['multiplier', 'fixed'])->default('multiplier');
            $table->json('applicable_days')->nullable(); // [1,2,3,4,5] for weekdays
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_shift_differentials');
        Schema::dropIfExists('hrm_employee_overtime');
        Schema::dropIfExists('hrm_ot_policies');
    }
};
