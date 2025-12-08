<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // user-auto-delete
        $schedule->command('app:user-auto-delete')->everyMinute();
        
        // send otp 
        // $schedule->command('app:otp-send')->everyMinute();

        // ===== BACKUP SCHEDULE (Dynamic from Database) =====
        try {
            $settings = \App\Modules\Settings\Models\ScheduleSetting::getSettings();
            
            if ($settings && $settings->backup_enabled) {
                // Daily Backup
                if ($settings->daily_backup_enabled) {
                    $command = $settings->daily_backup_type == 'db' 
                        ? 'backup:run --only-db' 
                        : 'backup:run';
                    
                    $schedule->command($command)
                        ->dailyAt($settings->daily_backup_time)
                        ->onSuccess(function () {
                            \Log::info('Daily backup completed successfully');
                        })
                        ->onFailure(function () {
                            \Log::error('Daily backup failed');
                        });
                }
                
                // Weekly Backup
                if ($settings->weekly_backup_enabled) {
                    $command = $settings->weekly_backup_type == 'db' 
                        ? 'backup:run --only-db' 
                        : 'backup:run';
                    
                    $weeklySchedule = $schedule->command($command)
                        ->weekly()
                        ->at($settings->weekly_backup_time);
                    
                    // Set the day of week
                    switch (strtolower($settings->weekly_backup_day)) {
                        case 'sunday': $weeklySchedule->sundays(); break;
                        case 'monday': $weeklySchedule->mondays(); break;
                        case 'tuesday': $weeklySchedule->tuesdays(); break;
                        case 'wednesday': $weeklySchedule->wednesdays(); break;
                        case 'thursday': $weeklySchedule->thursdays(); break;
                        case 'friday': $weeklySchedule->fridays(); break;
                        case 'saturday': $weeklySchedule->saturdays(); break;
                    }
                    
                    $weeklySchedule
                        ->onSuccess(function () {
                            \Log::info('Weekly backup completed successfully');
                        })
                        ->onFailure(function () {
                            \Log::error('Weekly backup failed');
                        });
                }
                
                // Cleanup
                if ($settings->cleanup_enabled) {
                    $cleanupSchedule = $schedule->command('backup:clean')
                        ->weekly()
                        ->at($settings->cleanup_time);
                    
                    // Set the day of week
                    switch (strtolower($settings->cleanup_day)) {
                        case 'sunday': $cleanupSchedule->sundays(); break;
                        case 'monday': $cleanupSchedule->mondays(); break;
                        case 'tuesday': $cleanupSchedule->tuesdays(); break;
                        case 'wednesday': $cleanupSchedule->wednesdays(); break;
                        case 'thursday': $cleanupSchedule->thursdays(); break;
                        case 'friday': $cleanupSchedule->fridays(); break;
                        case 'saturday': $cleanupSchedule->saturdays(); break;
                    }
                    
                    $cleanupSchedule
                        ->onSuccess(function () {
                            \Log::info('Backup cleanup completed successfully');
                        })
                        ->onFailure(function () {
                            \Log::error('Backup cleanup failed');
                        });
                }
            }
        } catch (\Exception $e) {
            \Log::error('Schedule loading error: ' . $e->getMessage());
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
