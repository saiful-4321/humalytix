# Backup & Cache Management - Issues and Solutions

## Issues Identified and Fixed

### ✅ Issue 1: Route Cache Error (FIXED)
**Error**: `Unable to prepare route [dashboard/user/{id}/reset-password] for serialization. Another route has already been assigned name [dashboard.user.reset-password]`

**Cause**: Duplicate route names in `app/Modules/Main/routes/web.php` - both GET and POST routes for reset-password had the same name.

**Solution**: Changed the POST route name from `user.reset-password` to `user.reset-password.update`

**File Changed**: `app/Modules/Main/routes/web.php` (line 54)

---

### ⚠️ Issue 2: No Backups Found & mysqldump Not Available

**Error**: `sh: mysqldump: command not found`

**Cause**: The `mysqldump` utility is not in the system PATH. This is required by the Spatie Laravel Backup package to create database backups.

**Solutions**:

#### Option A: Install MySQL Tools (Recommended)
Install MySQL or just the MySQL client tools on your Mac:

```bash
# Using Homebrew
brew install mysql-client

# Add to PATH (add this to your ~/.zshrc or ~/.bash_profile)
echo 'export PATH="/usr/local/opt/mysql-client/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
```

#### Option B: Use Docker/Laravel Sail
If you're using Docker or Laravel Sail, you need to run the backup command inside the container:

```bash
# For Laravel Sail
./vendor/bin/sail artisan backup:run --only-db

# For Docker Compose
docker-compose exec app php artisan backup:run --only-db
```

#### Option C: Configure Custom mysqldump Path
If mysqldump is installed but not in PATH, you can configure the path in `config/database.php`:

```php
'mysql' => [
    // ... other config
    'dump' => [
        'dump_binary_path' => '/path/to/mysql/bin', // e.g., '/usr/local/mysql/bin'
    ],
],
```

#### Option D: Use SQLite for Testing (Temporary)
If you just want to test the backup feature, you can temporarily switch to SQLite which doesn't require mysqldump:

1. Update `.env`:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database.sqlite
   ```

2. Create the database file:
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

---

### ✨ Issue 3: Scheduled/Automatic Backups

**Feature Request**: Add scheduled automatic backups

**Solution**: Laravel's task scheduler can be configured to run backups automatically.

#### Step 1: Configure the Schedule

Add to `app/Console/Kernel.php` in the `schedule()` method:

```php
protected function schedule(Schedule $schedule)
{
    // Run backup daily at 2 AM
    $schedule->command('backup:run --only-db')
        ->daily()
        ->at('02:00');
    
    // Or run backup every 6 hours
    // $schedule->command('backup:run --only-db')->everySixHours();
    
    // Clean old backups weekly
    $schedule->command('backup:clean')
        ->weekly()
        ->sundays()
        ->at('03:00');
    
    // Monitor backups daily
    $schedule->command('backup:monitor')
        ->daily()
        ->at('04:00');
}
```

#### Step 2: Setup Cron Job

For the scheduler to work, you need to add a cron entry on your server:

```bash
# Edit crontab
crontab -e

# Add this line (replace /path/to/project with your actual path)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

#### Step 3: Alternative - Use Supervisor (For Production)

Create a supervisor config file `/etc/supervisor/conf.d/laravel-scheduler.conf`:

```ini
[program:laravel-scheduler]
process_name=%(program_name)s
command=/usr/bin/php /path/to/project/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/scheduler.log
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-scheduler
```

---

## Backup Configuration Details

### Current Configuration (`config/backup.php`)

- **Backup Name**: Uses `APP_NAME` from .env ("Starter Minia")
- **Storage Location**: `storage/app/Starter Minia/`
- **Disk**: `local` (points to `storage/app/`)
- **Databases**: Uses default connection from `.env` (mysql)
- **Retention Policy**:
  - Keep all backups for 7 days
  - Keep daily backups for 16 days
  - Keep weekly backups for 8 weeks
  - Keep monthly backups for 4 months
  - Keep yearly backups for 2 years
  - Maximum storage: 5000 MB

### Files Included in Full Backup
- All files in `base_path()` (project root)
- **Excluded**: `vendor/`, `node_modules/`

### Available Backup Commands

```bash
# Create full backup (database + files)
php artisan backup:run

# Create database-only backup
php artisan backup:run --only-db

# Create files-only backup
php artisan backup:run --only-files

# Clean old backups based on retention policy
php artisan backup:clean

# Monitor backup health
php artisan backup:monitor

# List all backups
php artisan backup:list
```

---

## Testing the Backup Feature

Once mysqldump is available, test the backup:

1. **Create a test backup**:
   ```bash
   php artisan backup:run --only-db
   ```

2. **Check if backup was created**:
   ```bash
   ls -lh "storage/app/Starter Minia/"
   ```

3. **Test via web interface**:
   - Go to: Dashboard → Backup
   - Click "Create DB Backup"
   - Wait for success message
   - Backup should appear in the list

---

## Backup Management UI Features

The web interface (`/dashboard/backup`) provides:

✅ **Create Backups**:
- Full Backup (database + files)
- Database Only Backup

✅ **Manage Backups**:
- View all backups with size and date
- Download backups
- Delete individual backups
- Clean old backups automatically

✅ **Information**:
- What gets backed up
- Best practices
- Backup statistics

---

## Recommended Production Setup

1. **Install MySQL tools** on the server
2. **Configure scheduled backups** (daily at minimum)
3. **Set up backup monitoring** (get notified of failures)
4. **Configure off-site storage** (S3, FTP, etc.)
5. **Test restore process** regularly
6. **Monitor disk space** usage

### Example Production Schedule

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Daily database backup at 2 AM
    $schedule->command('backup:run --only-db')
        ->dailyAt('02:00')
        ->onSuccess(function () {
            Log::info('Daily backup completed successfully');
        })
        ->onFailure(function () {
            Log::error('Daily backup failed');
        });
    
    // Weekly full backup on Sunday at 3 AM
    $schedule->command('backup:run')
        ->weekly()
        ->sundays()
        ->at('03:00');
    
    // Clean old backups every Monday at 4 AM
    $schedule->command('backup:clean')
        ->weekly()
        ->mondays()
        ->at('04:00');
    
    // Monitor backup health daily at 5 AM
    $schedule->command('backup:monitor')
        ->dailyAt('05:00');
}
```

---

## Next Steps

1. ✅ Fix route cache error (COMPLETED)
2. ⚠️ Install mysqldump or configure alternative
3. 🔄 Test backup creation
4. 📅 Configure scheduled backups (optional)
5. 🔔 Configure backup notifications (optional)

---

## Support & Documentation

- **Spatie Laravel Backup**: https://spatie.be/docs/laravel-backup
- **Laravel Task Scheduling**: https://laravel.com/docs/scheduling
- **Backup Best Practices**: See the information card on the Backup Management page
