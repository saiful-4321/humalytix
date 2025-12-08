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
        Schema::create('schedule_settings', function (Blueprint $table) {
            $table->id();
            
            // Backup Schedule Settings
            $table->boolean('backup_enabled')->default(true);
            $table->boolean('daily_backup_enabled')->default(true);
            $table->string('daily_backup_time')->default('02:00');
            $table->enum('daily_backup_type', ['db', 'full'])->default('db');
            
            $table->boolean('weekly_backup_enabled')->default(true);
            $table->string('weekly_backup_day')->default('sunday');
            $table->string('weekly_backup_time')->default('03:00');
            $table->enum('weekly_backup_type', ['db', 'full'])->default('full');
            
            $table->boolean('cleanup_enabled')->default(true);
            $table->string('cleanup_day')->default('monday');
            $table->string('cleanup_time')->default('04:00');
            
            // Retention Settings
            $table->integer('keep_all_backups_for_days')->default(7);
            $table->integer('keep_daily_backups_for_days')->default(16);
            $table->integer('keep_weekly_backups_for_weeks')->default(8);
            $table->integer('keep_monthly_backups_for_months')->default(4);
            $table->integer('keep_yearly_backups_for_years')->default(2);
            $table->integer('max_storage_mb')->default(5000);
            
            $table->timestamps();
        });

        // Insert default settings
        DB::table('schedule_settings')->insert([
            'backup_enabled' => true,
            'daily_backup_enabled' => true,
            'daily_backup_time' => '02:00',
            'daily_backup_type' => 'db',
            'weekly_backup_enabled' => true,
            'weekly_backup_day' => 'sunday',
            'weekly_backup_time' => '03:00',
            'weekly_backup_type' => 'full',
            'cleanup_enabled' => true,
            'cleanup_day' => 'monday',
            'cleanup_time' => '04:00',
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
            'max_storage_mb' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_settings');
    }
};
