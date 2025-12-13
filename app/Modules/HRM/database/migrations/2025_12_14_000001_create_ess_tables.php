<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Attendance Regularization Requests
        Schema::create('hrm_attendance_regularizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable(); // Approver remarks
            $table->timestamps();
        });

        // Letter Requests (e.g., Salary Certificate)
        Schema::create('hrm_letter_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->string('type'); // salary_certificate, noc, experience_letter, etc.
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->foreignId('letter_id')->nullable()->constrained('hrm_employee_letters')->nullOnDelete(); // Linked generated letter
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hrm_letter_requests');
        Schema::dropIfExists('hrm_attendance_regularizations');
    }
};
