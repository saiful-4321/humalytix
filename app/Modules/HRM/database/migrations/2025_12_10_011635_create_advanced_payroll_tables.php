<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Salary Components (Heads of Income/Deduction)
        Schema::create('hrm_salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['earning', 'deduction']);
            $table->boolean('is_taxable')->default(true); // For tax calculation
            $table->enum('calculation_type', ['fixed', 'percentage', 'formula']); 
            $table->decimal('default_amount', 12, 2)->nullable(); // If fixed
            $table->decimal('default_percentage', 5, 2)->nullable(); // If percentage
            $table->unsignedBigInteger('percentage_basis_id')->nullable(); // Component ID this is a % of (e.g. Basic)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Self-referencing FK for percentage basis
            $table->foreign('percentage_basis_id')->references('id')->on('hrm_salary_components');
        });

        // 2. Salary Structures (Templates/Grades)
        Schema::create('hrm_salary_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Grade A Executive", "Intern"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Structure Components (Pivot with overrides)
        Schema::create('hrm_salary_structure_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_structure_id')->constrained('hrm_salary_structures')->onDelete('cascade');
            $table->foreignId('salary_component_id')->constrained('hrm_salary_components')->onDelete('cascade');
            $table->enum('calculation_type', ['fixed', 'percentage', 'formula'])->nullable(); // Override
            $table->decimal('amount', 12, 2)->nullable(); // Override value
            $table->decimal('percentage', 5, 2)->nullable(); // Override value
            $table->timestamps();
        });

        // 4. Employee Salary Assignment (History of Salary)
        Schema::create('hrm_employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->foreignId('salary_structure_id')->nullable()->constrained('hrm_salary_structures');
            $table->decimal('gross_salary', 12, 2)->nullable(); // Can be used as base for breakdown
            $table->decimal('basic_salary', 12, 2)->nullable(); // Explicit basic if needed
            $table->date('effective_date'); // To track increments/arrears
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Payroll Items (Detailed breakdown for each generated payroll)
        Schema::create('hrm_payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('hrm_payrolls')->onDelete('cascade');
            $table->foreignId('salary_component_id')->constrained('hrm_salary_components');
            $table->string('component_name'); // Snapshot of name
            $table->enum('type', ['earning', 'deduction']); // Snapshot of type
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
        
        // 6. Tax Configuration (Simple per-slab model)
        Schema::create('hrm_tax_slabs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // e.g. "2024-2025 Male"
            $table->decimal('min_income', 15, 2);
            $table->decimal('max_income', 15, 2)->nullable(); // Null for "Above X"
            $table->decimal('tax_rate', 5, 2); // Percentage
            $table->decimal('deduction_amount', 12, 2)->default(0); // Any fixed deduction for this slab
            $table->enum('gender', ['all', 'male', 'female', 'other'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_tax_slabs');
        Schema::dropIfExists('hrm_payroll_items');
        Schema::dropIfExists('hrm_employee_salaries');
        Schema::dropIfExists('hrm_salary_structure_components');
        Schema::dropIfExists('hrm_salary_structures');
        Schema::dropIfExists('hrm_salary_components');
    }
};
