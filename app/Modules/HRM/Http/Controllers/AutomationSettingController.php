<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Modules\HRM\Models\AutomationSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AutomationSettingController extends Controller
{
    /**
     * Display automation settings
     */
    public function index()
    {
        $settings = AutomationSetting::getSettings();
        return view('HRM::pages.settings.automation', compact('settings'));
    }

    /**
     * Update automation settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'auto_trigger_workflows' => 'boolean',
            'auto_trigger_leave_workflows' => 'boolean',
            'auto_trigger_expense_workflows' => 'boolean',
            'auto_accrue_leaves' => 'boolean',
            'accrual_day_of_month' => 'integer|min:1|max:28',
            'accrual_time' => 'date_format:H:i',
            'check_expiring_documents' => 'boolean',
            'expiry_check_time' => 'date_format:H:i',
            'escalate_approvals' => 'boolean',
            'escalation_check_time' => 'date_format:H:i',
            'reminder_after_days' => 'integer|min:1',
            'escalate_after_days' => 'integer|min:1',
            'use_queue_for_notifications' => 'boolean',
            'send_email_notifications' => 'boolean',
            'send_sms_notifications' => 'boolean',
        ]);

        // Convert checkbox values
        foreach (['auto_trigger_workflows', 'auto_trigger_leave_workflows', 'auto_trigger_expense_workflows', 
                  'auto_accrue_leaves', 'check_expiring_documents', 'escalate_approvals',
                  'use_queue_for_notifications', 'send_email_notifications', 'send_sms_notifications'] as $field) {
            $validated[$field] = $request->has($field);
        }

        $settings = AutomationSetting::getSettings();
        $settings->update($validated);

        return redirect()->route('hrm.settings.automation')
            ->with('success', 'Automation settings updated successfully!');
    }

    /**
     * Manually run leave accrual
     */
    public function runLeaveAccrual()
    {
        try {
            \Artisan::call('hrm:auto-accrue-leaves');
            $output = \Artisan::output();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Leave accrual completed successfully!',
                    'output' => $output
                ]);
            }

            return redirect()->route('hrm.settings.automation')
                ->with('success', 'Leave accrual completed! ' . $output);
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('hrm.settings.automation')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Manually run document expiry check
     */
    public function runDocumentCheck()
    {
        try {
            \Artisan::call('hrm:check-expiring-documents');
            $output = \Artisan::output();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document expiry check completed successfully!',
                    'output' => $output
                ]);
            }

            return redirect()->route('hrm.settings.automation')
                ->with('success', 'Document expiry check completed! ' . $output);
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('hrm.settings.automation')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Manually run approval escalation
     */
    public function runApprovalEscalation()
    {
        try {
            \Artisan::call('hrm:escalate-approvals');
            $output = \Artisan::output();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Approval escalation completed successfully!',
                    'output' => $output
                ]);
            }

            return redirect()->route('hrm.settings.automation')
                ->with('success', 'Approval escalation completed! ' . $output);
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('hrm.settings.automation')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
