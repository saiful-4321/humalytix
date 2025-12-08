# Cache & Backup Management System

## Overview
A comprehensive system for managing application cache and backups with a modern, user-friendly interface.

## Features Implemented

### 1. Cache Management (`/dashboard/cache`)
**Capabilities:**
- **Clear Cache Options:**
  - Clear All Caches (application, config, route, view, event)
  - Clear Application Cache
  - Clear Config Cache
  - Clear Route Cache
  - Clear View Cache
  - Clear Event Cache

- **Rebuild Cache Options:**
  - Rebuild All Caches
  - Rebuild Config Cache
  - Rebuild Route Cache
  - Rebuild View Cache
  - Rebuild Event Cache

- **Optimization:**
  - Optimize Application (runs `php artisan optimize`)
  - Clear Optimization (runs `php artisan optimize:clear`)

**Features:**
- Beautiful, card-based UI with icons
- AJAX-powered operations (no page reload)
- SweetAlert2 confirmations and notifications
- Separate sections for clearing vs rebuilding
- Loading states during operations

### 2. Backup Management (`/dashboard/backup`)
**Powered by:** `spatie/laravel-backup` (industry-standard Laravel package)

**Capabilities:**
- **Create Backups:**
  - Full Backup (Database + Files)
  - Database Only Backup
  
- **Manage Backups:**
  - View all available backups with file size and creation date
  - Download backups to local storage
  - Delete individual backups
  - Clean old backups (automatic retention policy)

**Features:**
- Visual cards for creating different backup types
- Table view of all backups with actions
- File size display in human-readable format
- Download backups directly from browser
- Confirmation dialogs for destructive actions
- Information section with best practices

## Technical Implementation

### Controllers
1. **CacheController** (`app/Modules/Main/Http/Controllers/CacheController.php`)
   - Handles all cache operations
   - Uses Laravel's Artisan facade
   - Comprehensive error handling and logging

2. **BackupController** (`app/Modules/Main/Http/Controllers/BackupController.php`)
   - Integrates with Spatie Backup package
   - Manages backup creation, download, deletion
   - Automatic cleanup functionality

### Routes
All routes are protected with permissions:
- `cache-management` permission for cache operations
- `backup-management` permission for backup operations

### Views
- **Cache Management:** `app/Modules/Main/resources/views/pages/cache/index.blade.php`
- **Backup Management:** `app/Modules/Main/resources/views/pages/backup/index.blade.php`

### Configuration
Backup configuration is in `config/backup.php`:
- Default storage disk: `local`
- Backup location: `storage/app/backups/`
- Automatic cleanup based on retention policy

## Permissions Required

You need to create these permissions in your system:
1. `cache-management` - For accessing cache management
2. `backup-management` - For accessing backup system

## Usage

### Cache Management
1. Navigate to `/dashboard/cache`
2. Choose operation (Clear, Rebuild, or Optimize)
3. Confirm action
4. Wait for completion

### Backup Management
1. Navigate to `/dashboard/backup`
2. Click "Create Full Backup" or "Create DB Backup"
3. Wait for backup creation
4. Download or manage backups from the list

## Best Practices

### Cache
- Clear caches after code changes
- Rebuild caches in production for better performance
- Use "Optimize Application" before deploying

### Backup
- Create backups before major updates
- Download important backups to external storage
- Regularly clean old backups to save disk space
- Test restore process periodically

## Backup Storage

By default, backups are stored in:
```
storage/app/backups/[app-name]/
```

You can configure additional storage destinations (S3, FTP, etc.) in `config/backup.php`.

## Scheduled Backups

To enable automatic backups, add to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:clean')->daily()->at('01:00');
    $schedule->command('backup:run')->daily()->at('02:00');
}
```

## Next Steps

1. **Add Permissions:** Create `cache-management` and `backup-management` permissions
2. **Assign to Roles:** Assign these permissions to appropriate roles (Admin, Super Admin)
3. **Add Menu Items:** Add menu items in your sidebar for easy access
4. **Configure Backup:** Review and customize `config/backup.php` for your needs
5. **Setup Cron:** Configure Laravel scheduler for automatic backups

## Menu Integration Example

Add these to your sidebar menu:

```php
[
    'name' => 'Cache Management',
    'route' => 'dashboard.cache',
    'icon' => 'mdi-delete-sweep',
    'permission' => 'cache-management'
],
[
    'name' => 'Backup System',
    'route' => 'dashboard.backup',
    'icon' => 'mdi-backup-restore',
    'permission' => 'backup-management'
]
```
