<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationSetting extends Model
{
    protected $table = 'hrm_automation_settings';

    protected $fillable = [
        'auto_trigger_workflows',
        'auto_trigger_leave_workflows',
        'auto_trigger_expense_workflows',
        'auto_accrue_leaves',
        'accrual_day_of_month',
        'accrual_time',
        'check_expiring_documents',
        'expiry_check_time',
        'expiry_thresholds',
        'escalate_approvals',
        'escalation_check_time',
        'reminder_after_days',
        'escalate_after_days',
        'use_queue_for_notifications',
        'send_email_notifications',
        'send_sms_notifications',
    ];

    protected $casts = [
        'auto_trigger_workflows' => 'boolean',
        'auto_trigger_leave_workflows' => 'boolean',
        'auto_trigger_expense_workflows' => 'boolean',
        'auto_accrue_leaves' => 'boolean',
        'check_expiring_documents' => 'boolean',
        'escalate_approvals' => 'boolean',
        'use_queue_for_notifications' => 'boolean',
        'send_email_notifications' => 'boolean',
        'send_sms_notifications' => 'boolean',
        'expiry_thresholds' => 'array',
    ];

    /**
     * Get the singleton settings instance
     */
    public static function getSettings()
    {
        return static::first() ?? static::create([]);
    }

    /**
     * Check if workflow auto-trigger is enabled for a specific module
     */
    public static function shouldAutoTriggerWorkflow(string $module): bool
    {
        $settings = static::getSettings();
        
        if (!$settings->auto_trigger_workflows) {
            return false;
        }

        return match($module) {
            'leave' => $settings->auto_trigger_leave_workflows,
            'expense' => $settings->auto_trigger_expense_workflows,
            default => true,
        };
    }

    /**
     * Check if leave accrual automation is enabled
     */
    public static function shouldAutoAccrueLeaves(): bool
    {
        return static::getSettings()->auto_accrue_leaves;
    }

    /**
     * Check if document expiry check is enabled
     */
    public static function shouldCheckExpiringDocuments(): bool
    {
        return static::getSettings()->check_expiring_documents;
    }

    /**
     * Check if approval escalation is enabled
     */
    public static function shouldEscalateApprovals(): bool
    {
        return static::getSettings()->escalate_approvals;
    }
}
