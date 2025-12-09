<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bonus Types/Schemes
        Schema::create('hrm_bonus_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('calculation_type', ['fixed', 'percentage', 'performance_based'])->default('fixed');
            $table->decimal('default_amount', 15, 2)->nullable();
            $table->decimal('default_percentage', 5, 2)->nullable(); // % of salary
            $table->enum('frequency', ['one_time', 'monthly', 'quarterly', 'half_yearly', 'yearly'])->default('one_time');
            $table->json('eligibility_criteria')->nullable(); // Min tenure, performance rating, etc.
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Employee Bonuses
        Schema::create('hrm_employee_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('bonus_type_id')->nullable()->constrained('hrm_bonus_types')->nullOnDelete();
            $table->string('bonus_name'); // Festival Bonus, Performance Bonus, etc.
            $table->decimal('amount', 15, 2);
            $table->date('bonus_date');
            $table->integer('bonus_month')->nullable();
            $table->integer('bonus_year');
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->boolean('is_taxable')->default(true);
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('payroll_id')->nullable()->constrained('hrm_payrolls')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Commission Schemes
        Schema::create('hrm_commission_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('calculation_type', ['percentage', 'tiered', 'fixed_per_unit'])->default('percentage');
            $table->decimal('rate', 10, 2); // % or fixed amount
            $table->json('tiers')->nullable(); // For tiered: [{min: 0, max: 100000, rate: 5}, ...]
            $table->decimal('threshold_amount', 15, 2)->nullable(); // Min sales to qualify
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Employee Commissions
        Schema::create('hrm_employee_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('commission_scheme_id')->nullable()->constrained('hrm_commission_schemes')->nullOnDelete();
            $table->integer('commission_month');
            $table->integer('commission_year');
            $table->decimal('sales_amount', 15, 2)->default(0); // Total sales
            $table->decimal('commission_rate', 10, 2); // Applied rate
            $table->decimal('commission_amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('payroll_id')->nullable()->constrained('hrm_payrolls')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Gratuity Configuration
        Schema::create('hrm_gratuity_config', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default Gratuity Policy');
            $table->decimal('min_service_years', 5, 2)->default(5.0); // Minimum years to qualify
            $table->enum('calculation_type', ['fixed_formula', 'custom'])->default('fixed_formula');
            $table->text('formula_description')->nullable(); // e.g., "Last basic * years / 2"
            $table->decimal('multiplier', 5, 2)->default(0.5); // Years of service * Basic * 0.5
            $table->decimal('max_gratuity_amount', 15, 2)->nullable(); // Cap if any
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Employee Gratuity Records
        Schema::create('hrm_employee_gratuity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('gratuity_config_id')->nullable()->constrained('hrm_gratuity_config')->nullOnDelete();
            $table->date('calculation_date'); // Usually exit/retirement date
            $table->decimal('service_years', 5, 2);
            $table->decimal('last_basic_salary', 15, 2);
            $table->decimal('calculated_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable(); // Can differ from calculated
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('payment_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_employee_gratuity');
        Schema::dropIfExists('hrm_gratuity_config');
        Schema::dropIfExists('hrm_employee_commissions');
        Schema::dropIfExists('hrm_commission_schemes');
        Schema::dropIfExists('hrm_employee_bonuses');
        Schema::dropIfExists('hrm_bonus_types');
    }
};
