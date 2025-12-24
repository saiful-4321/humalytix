<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hrm_automation_settings', function (Blueprint $table) {
            $table->id();
            
            // Workflow Auto-Trigger Settings
            $table->boolean('auto_trigger_workflows')->default(true);
            $table->boolean('auto_trigger_leave_workflows')->default(true);
            $table->boolean('auto_trigger_expense_workflows')->default(true);
            
            // Leave Accrual Settings
            $table->boolean('auto_accrue_leaves')->default(true);
            $table->integer('accrual_day_of_month')->default(1); // 1-28
            $table->time('accrual_time')->default('00:00');
            
            // Document Expiry Settings
            $table->boolean('check_expiring_documents')->default(true);
            $table->time('expiry_check_time')->default('09:00');
            $table->json('expiry_thresholds')->nullable(); // [30, 15, 7] days
            
            // Approval Escalation Settings
            $table->boolean('escalate_approvals')->default(true);
            $table->time('escalation_check_time')->default('10:00');
            $table->integer('reminder_after_days')->default(3);
            $table->integer('escalate_after_days')->default(7);
            
            // Notification Settings
            $table->boolean('use_queue_for_notifications')->default(true);
            $table->boolean('send_email_notifications')->default(false);
            $table->boolean('send_sms_notifications')->default(false);
            
            $table->timestamps();
        });

        // Insert default settings
        DB::table('hrm_automation_settings')->insert([
            'auto_trigger_workflows' => true,
            'auto_trigger_leave_workflows' => true,
            'auto_trigger_expense_workflows' => true,
            'auto_accrue_leaves' => true,
            'accrual_day_of_month' => 1,
            'accrual_time' => '00:00',
            'check_expiring_documents' => true,
            'expiry_check_time' => '09:00',
            'expiry_thresholds' => json_encode([30, 15, 7]),
            'escalate_approvals' => true,
            'escalation_check_time' => '10:00',
            'reminder_after_days' => 3,
            'escalate_after_days' => 7,
            'use_queue_for_notifications' => true,
            'send_email_notifications' => false,
            'send_sms_notifications' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_automation_settings');
    }
};
