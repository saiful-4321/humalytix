@extends("Main::layouts.app")

@section('content')
<div class="row">
    <div class="col-lg-5 col-md-8 col-sm-12">                        
        <h2>{{ __('Schedule Settings') }}</h2>
    </div>            
    <div class="col-lg-7 col-md-4 col-sm-12 text-right">
        <ul class="breadcrumb justify-content-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>                            
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Schedule Settings</li>
        </ul>
    </div>

    <div class="col-lg-12 col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('dashboard.settings.schedule.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Master Toggle -->
            <div class="card bg-white mb-3">
                <div class="card-header border-bottom">
                    <h6 class="font-weight-medium mb-0">
                        <i class="mdi mdi-clock-outline me-2"></i>Backup Automation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="p-2">
                        <div class="form-check form-switch form-switch-lg">
                        <input class="form-check-input" type="checkbox" name="backup_enabled" 
                               id="backup_enabled" {{ $settings->backup_enabled ? 'checked' : '' }}>
                        <label class="form-check-label d-flex " for="backup_enabled">
                            <strong>Enable Automated Backups <span class="text-muted">Turn on/off all scheduled backup tasks</span></strong>
                        </label>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Daily Backup Settings -->
            <div class="card bg-white mb-3">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="font-weight-medium mb-0">
                            <i class="mdi mdi-database text-info me-2"></i>Daily Backup
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="daily_backup_enabled" 
                                   id="daily_backup_enabled" {{ $settings->daily_backup_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="daily_backup_enabled">Enabled</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="p-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Backup Time</label>
                                    <input type="time" class="form-control" name="daily_backup_time" 
                                        value="{{ $settings->daily_backup_time }}" required>
                                    <small class="text-muted">Time when daily backup will run</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Backup Type</label>
                                    <select class="form-select" name="daily_backup_type" required>
                                        <option value="db" {{ $settings->daily_backup_type == 'db' ? 'selected' : '' }}>
                                            Database Only
                                        </option>
                                        <option value="full" {{ $settings->daily_backup_type == 'full' ? 'selected' : '' }}>
                                            Full Backup (DB + Files)
                                        </option>
                                    </select>
                                    <small class="text-muted">What to include in daily backup</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Backup Settings -->
            <div class="card bg-white mb-3">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="font-weight-medium mb-0">
                            <i class="mdi mdi-backup-restore text-success me-2"></i>Weekly Backup
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="weekly_backup_enabled" 
                                   id="weekly_backup_enabled" {{ $settings->weekly_backup_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="weekly_backup_enabled">Enabled</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="p-2">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Day of Week</label>
                                    <select class="form-select" name="weekly_backup_day" required>
                                        <option value="sunday" {{ $settings->weekly_backup_day == 'sunday' ? 'selected' : '' }}>Sunday</option>
                                        <option value="monday" {{ $settings->weekly_backup_day == 'monday' ? 'selected' : '' }}>Monday</option>
                                        <option value="tuesday" {{ $settings->weekly_backup_day == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                        <option value="wednesday" {{ $settings->weekly_backup_day == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                        <option value="thursday" {{ $settings->weekly_backup_day == 'thursday' ? 'selected' : '' }}>Thursday</option>
                                        <option value="friday" {{ $settings->weekly_backup_day == 'friday' ? 'selected' : '' }}>Friday</option>
                                        <option value="saturday" {{ $settings->weekly_backup_day == 'saturday' ? 'selected' : '' }}>Saturday</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Backup Time</label>
                                    <input type="time" class="form-control" name="weekly_backup_time" 
                                        value="{{ $settings->weekly_backup_time }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Backup Type</label>
                                    <select class="form-select" name="weekly_backup_type" required>
                                        <option value="db" {{ $settings->weekly_backup_type == 'db' ? 'selected' : '' }}>
                                            Database Only
                                        </option>
                                        <option value="full" {{ $settings->weekly_backup_type == 'full' ? 'selected' : '' }}>
                                            Full Backup (DB + Files)
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cleanup Settings -->
            <div class="card bg-white mb-3">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="font-weight-medium mb-0">
                            <i class="mdi mdi-delete-sweep text-warning me-2"></i>Backup Cleanup
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="cleanup_enabled" 
                                   id="cleanup_enabled" {{ $settings->cleanup_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="cleanup_enabled">Enabled</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="p-2">
                        <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cleanup Day</label>
                                <select class="form-select" name="cleanup_day" required>
                                    <option value="sunday" {{ $settings->cleanup_day == 'sunday' ? 'selected' : '' }}>Sunday</option>
                                    <option value="monday" {{ $settings->cleanup_day == 'monday' ? 'selected' : '' }}>Monday</option>
                                    <option value="tuesday" {{ $settings->cleanup_day == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                    <option value="wednesday" {{ $settings->cleanup_day == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                    <option value="thursday" {{ $settings->cleanup_day == 'thursday' ? 'selected' : '' }}>Thursday</option>
                                    <option value="friday" {{ $settings->cleanup_day == 'friday' ? 'selected' : '' }}>Friday</option>
                                    <option value="saturday" {{ $settings->cleanup_day == 'saturday' ? 'selected' : '' }}>Saturday</option>
                                </select>
                                <small class="text-muted">Day to run cleanup task</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cleanup Time</label>
                                <input type="time" class="form-control" name="cleanup_time" 
                                       value="{{ $settings->cleanup_time }}" required>
                                <small class="text-muted">Time when cleanup will run</small>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Retention Policy -->
            <div class="card bg-white mb-3">
                <div class="card-header border-bottom">
                    <h6 class="font-weight-medium mb-0">
                        <i class="mdi mdi-calendar-clock me-2"></i>Retention Policy
                    </h6>
                </div>
                <div class="card-body">
                    <div class="p-2">
                        <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Keep All Backups For (Days)</label>
                                <input type="number" class="form-control" name="keep_all_backups_for_days" 
                                       value="{{ $settings->keep_all_backups_for_days }}" min="1" required>
                                <small class="text-muted">Keep every backup for this many days</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Keep Daily Backups For (Days)</label>
                                <input type="number" class="form-control" name="keep_daily_backups_for_days" 
                                       value="{{ $settings->keep_daily_backups_for_days }}" min="1" required>
                                <small class="text-muted">After initial period, keep one backup per day</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Keep Weekly Backups For (Weeks)</label>
                                <input type="number" class="form-control" name="keep_weekly_backups_for_weeks" 
                                       value="{{ $settings->keep_weekly_backups_for_weeks }}" min="1" required>
                                <small class="text-muted">After daily period, keep one backup per week</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Keep Monthly Backups For (Months)</label>
                                <input type="number" class="form-control" name="keep_monthly_backups_for_months" 
                                       value="{{ $settings->keep_monthly_backups_for_months }}" min="1" required>
                                <small class="text-muted">After weekly period, keep one backup per month</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Keep Yearly Backups For (Years)</label>
                                <input type="number" class="form-control" name="keep_yearly_backups_for_years" 
                                       value="{{ $settings->keep_yearly_backups_for_years }}" min="1" required>
                                <small class="text-muted">After monthly period, keep one backup per year</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Maximum Storage (MB)</label>
                                <input type="number" class="form-control" name="max_storage_mb" 
                                       value="{{ $settings->max_storage_mb }}" min="100" required>
                                <small class="text-muted">Delete oldest backups when exceeding this limit</small>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="alert alert-info">
                <h6><i class="mdi mdi-information me-2"></i>Important Information</h6>
                <ul class="mb-0">
                    <li>These schedules require a cron job to be set up on your server</li>
                    <li>Add this to your crontab: <code>* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1</code></li>
                    <li>Backup functionality requires <code>mysqldump</code> to be installed and available in PATH</li>
                    <li>Changes to retention policy will be applied to the backup configuration file</li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div class="text-end mb-5">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save me-1"></i>Save Schedule Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
