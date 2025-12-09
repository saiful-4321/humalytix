<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_final_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resignation_id')->constrained('hrm_resignations')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->decimal('last_salary', 15, 2)->default(0);
            $table->integer('pending_leaves')->default(0);
            $table->decimal('leave_encashment', 15, 2)->default(0);
            $table->decimal('gratuity', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('other_payments', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('total_payable', 15, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('payment_status')->default('pending'); // pending, processed, paid
            $table->string('payment_reference')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['resignation_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_final_settlements');
    }
};
