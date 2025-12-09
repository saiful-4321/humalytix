<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Loan Types
        Schema::create('hrm_loan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->enum('interest_type', ['flat', 'reducing'])->default('flat');
            $table->integer('max_tenure_months')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Employee Loans
        Schema::create('hrm_employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('loan_type_id')->constrained('hrm_loan_types')->cascadeOnDelete();
            $table->string('loan_number')->unique();
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->enum('interest_type', ['flat', 'reducing'])->default('flat');
            $table->integer('tenure_months');
            $table->decimal('monthly_installment', 15, 2);
            $table->decimal('total_payable', 15, 2);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('outstanding_balance', 15, 2);
            $table->date('disbursement_date');
            $table->date('first_installment_date');
            $table->enum('status', ['pending', 'approved', 'active', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Loan Installments (Payment History)
        Schema::create('hrm_loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('hrm_employee_loans')->cascadeOnDelete();
            $table->integer('installment_number');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2)->default(0);
            $table->decimal('installment_amount', 15, 2);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->decimal('paid_amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->foreignId('payroll_id')->nullable()->constrained('hrm_payrolls')->nullOnDelete();
            $table->timestamps();
        });

        // Employee Advances (Salary Advance)
        Schema::create('hrm_employee_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->string('advance_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->date('disbursement_date');
            $table->integer('deduction_months')->default(1); // Deduct over how many months
            $table->decimal('monthly_deduction', 15, 2);
            $table->decimal('total_deducted', 15, 2)->default(0);
            $table->decimal('outstanding_balance', 15, 2);
            $table->enum('status', ['pending', 'approved', 'active', 'completed', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Advance Deductions (Payment History)
        Schema::create('hrm_advance_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advance_id')->constrained('hrm_employee_advances')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('deduction_date');
            $table->foreignId('payroll_id')->nullable()->constrained('hrm_payrolls')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_advance_deductions');
        Schema::dropIfExists('hrm_employee_advances');
        Schema::dropIfExists('hrm_loan_installments');
        Schema::dropIfExists('hrm_employee_loans');
        Schema::dropIfExists('hrm_loan_types');
    }
};
