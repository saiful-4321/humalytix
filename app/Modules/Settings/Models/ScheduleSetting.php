<?php

namespace App\Modules\Settings\Models;

use App\Modules\Main\Utilities\ActivityLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSetting extends Model
{
    use HasFactory, ActivityLogTrait;

    protected $table = 'schedule_settings';

    protected $fillable = [
        'backup_enabled',
        'daily_backup_enabled',
        'daily_backup_time',
        'daily_backup_type',
        'weekly_backup_enabled',
        'weekly_backup_day',
        'weekly_backup_time',
        'weekly_backup_type',
        'cleanup_enabled',
        'cleanup_day',
        'cleanup_time',
        'keep_all_backups_for_days',
        'keep_daily_backups_for_days',
        'keep_weekly_backups_for_weeks',
        'keep_monthly_backups_for_months',
        'keep_yearly_backups_for_years',
        'max_storage_mb',
    ];

    protected $casts = [
        'backup_enabled' => 'boolean',
        'daily_backup_enabled' => 'boolean',
        'weekly_backup_enabled' => 'boolean',
        'cleanup_enabled' => 'boolean',
        'keep_all_backups_for_days' => 'integer',
        'keep_daily_backups_for_days' => 'integer',
        'keep_weekly_backups_for_weeks' => 'integer',
        'keep_monthly_backups_for_months' => 'integer',
        'keep_yearly_backups_for_years' => 'integer',
        'max_storage_mb' => 'integer',
    ];

    /**
     * Get the singleton instance
     */
    public static function getSettings()
    {
        return self::first() ?? self::create([]);
    }
}
