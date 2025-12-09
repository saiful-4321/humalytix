<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            
            // KYC Information
            $table->string('nid_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('driving_license')->nullable();
            
            // Address
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            // Employment Details
            $table->foreignId('department_id')->nullable()->constrained('hrm_departments')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained('hrm_branches')->onDelete('set null');
            $table->foreignId('business_unit_id')->nullable()->constrained('hrm_business_units')->onDelete('set null');
            $table->string('designation')->nullable();
            $table->string('employment_type')->nullable(); // full_time, part_time, contract, etc.
            $table->date('joining_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->foreignId('reporting_to')->nullable()->constrained('hrm_employees')->onDelete('set null');
            
            // Salary Information
            $table->decimal('basic_salary', 15, 2)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            
            // Status
            $table->string('status')->default('active'); // active, probation, confirmed, notice_period, resigned, terminated
            $table->text('remarks')->nullable();
            $table->string('photo')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index('employee_code');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_employees');
    }
};
