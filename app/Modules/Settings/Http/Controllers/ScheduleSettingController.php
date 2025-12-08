<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\ScheduleSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleSettingController extends Controller
{
    public function index()
    {
        $settings = ScheduleSetting::getSettings();
        
        return view('Settings::schedule_setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'backup_enabled' => 'boolean',
            'daily_backup_enabled' => 'boolean',
            'daily_backup_time' => 'required|string',
            'daily_backup_type' => 'required|in:db,full',
            'weekly_backup_enabled' => 'boolean',
            'weekly_backup_day' => 'required|string',
            'weekly_backup_time' => 'required|string',
            'weekly_backup_type' => 'required|in:db,full',
            'cleanup_enabled' => 'boolean',
            'cleanup_day' => 'required|string',
            'cleanup_time' => 'required|string',
            'keep_all_backups_for_days' => 'required|integer|min:1',
            'keep_daily_backups_for_days' => 'required|integer|min:1',
            'keep_weekly_backups_for_weeks' => 'required|integer|min:1',
            'keep_monthly_backups_for_months' => 'required|integer|min:1',
            'keep_yearly_backups_for_years' => 'required|integer|min:1',
            'max_storage_mb' => 'required|integer|min:100',
        ]);

        // Convert checkboxes to boolean
        $validated['backup_enabled'] = $request->has('backup_enabled');
        $validated['daily_backup_enabled'] = $request->has('daily_backup_enabled');
        $validated['weekly_backup_enabled'] = $request->has('weekly_backup_enabled');
        $validated['cleanup_enabled'] = $request->has('cleanup_enabled');

        try {
            $settings = ScheduleSetting::getSettings();
            $settings->update($validated);

            // Update backup config file
            $this->updateBackupConfig($settings);

            Log::info('ScheduleSettingController@update - requested by ' . ($request->user()->email ?? null));

            return redirect()
                ->route('dashboard.backup.schedule')
                ->with('success', 'Schedule settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('ScheduleSettingController@update - ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Error updating schedule settings: ' . $e->getMessage());
        }
    }

    /**
     * Update the backup configuration file
     */
    private function updateBackupConfig($settings)
    {
        $configPath = config_path('backup.php');
        $config = file_get_contents($configPath);

        // Update retention settings
        $config = preg_replace(
            "/'keep_all_backups_for_days' => \d+/",
            "'keep_all_backups_for_days' => {$settings->keep_all_backups_for_days}",
            $config
        );
        
        $config = preg_replace(
            "/'keep_daily_backups_for_days' => \d+/",
            "'keep_daily_backups_for_days' => {$settings->keep_daily_backups_for_days}",
            $config
        );
        
        $config = preg_replace(
            "/'keep_weekly_backups_for_weeks' => \d+/",
            "'keep_weekly_backups_for_weeks' => {$settings->keep_weekly_backups_for_weeks}",
            $config
        );
        
        $config = preg_replace(
            "/'keep_monthly_backups_for_months' => \d+/",
            "'keep_monthly_backups_for_months' => {$settings->keep_monthly_backups_for_months}",
            $config
        );
        
        $config = preg_replace(
            "/'keep_yearly_backups_for_years' => \d+/",
            "'keep_yearly_backups_for_years' => {$settings->keep_yearly_backups_for_years}",
            $config
        );
        
        $config = preg_replace(
            "/'delete_oldest_backups_when_using_more_megabytes_than' => \d+/",
            "'delete_oldest_backups_when_using_more_megabytes_than' => {$settings->max_storage_mb}",
            $config
        );

        file_put_contents($configPath, $config);
        
        // Clear config cache
        \Artisan::call('config:clear');
    }
}
