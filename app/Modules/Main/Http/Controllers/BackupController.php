<?php

namespace App\Modules\Main\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackups();
        $systemCheck = $this->checkSystem();
        
        return view('Main::pages.backup.index', compact('backups', 'systemCheck'));
    }

    public function create(Request $request)
    {
        try {
            // Get backup type: db, code, files (storage), full
            $type = $request->input('type', 'full');
            // Backwards compatibility for only_db checkbox
            if ($request->has('only_db') && $request->input('only_db')) {
                $type = 'db';
            }

            Log::info('BackupController@create - Starting backup of type: ' . $type);

            $exitCode = 0;

            switch ($type) {
                case 'db':
                    // Database Only
                    config(['backup.backup.destination.filename_prefix' => 'db_']);
                    $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
                    break;

                case 'code':
                    // Source Code Only (No Storage, No Database)
                    // Temporarily modify config to exclude storage
                    config([
                        'backup.backup.source.files.include' => [
                            base_path('app'),
                            base_path('bootstrap'),
                            base_path('config'),
                            base_path('database'),
                            base_path('public'),
                            base_path('resources'),
                            base_path('routes'),
                            base_path('.env'),
                            base_path('artisan'),
                            base_path('composer.json'),
                            base_path('composer.lock'),
                            base_path('package.json'),
                        ],
                        'backup.backup.source.files.exclude' => [
                            base_path('vendor'), 
                            base_path('node_modules'), 
                            base_path('storage'), // Explicitly exclude storage
                            base_path('.git')
                        ],
                        'backup.backup.destination.filename_prefix' => 'code_'
                    ]);
                    $exitCode = Artisan::call('backup:run', ['--only-files' => true]);
                    break;

                case 'files':
                    // Storage/Files Only (No Source Code, No Database)
                    // Temporarily modify config to only include storage
                    config([
                        'backup.backup.source.files.include' => [base_path('storage')],
                        'backup.backup.source.files.exclude' => [
                            base_path('storage/framework'), // Exclude framework cache/sessions
                            base_path('storage/logs')       // Optional: Exclude logs
                        ],
                        'backup.backup.destination.filename_prefix' => 'files_'
                    ]);
                    $exitCode = Artisan::call('backup:run', ['--only-files' => true]);
                    break;

                case 'full':
                default:
                    // Full Backup (Database + Code + Storage)
                    config(['backup.backup.destination.filename_prefix' => 'full_']);
                    $exitCode = Artisan::call('backup:run');
                    break;
            }
            
            // Get command output
            $output = Artisan::output();
            
            // Check if command was successful
            if ($exitCode !== 0) {
                Log::error('BackupController@create - Backup failed with exit code: ' . $exitCode . ' - Output: ' . $output);
                
                // Check for specific errors
                if (str_contains($output, 'mysqldump: command not found')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Backup failed: mysqldump command not found. Please install MySQL client tools.'
                    ], 500);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Backup failed. Check logs for details.'
                ], 500);
            }
            
            Log::info('BackupController@create - requested by ' . ($request->user()->email ?? null) . ' - Type: ' . $type);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' backup created successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('BackupController@create - ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($fileName)
    {
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $backupPath = config('backup.backup.name') . '/' . $fileName;

            if (!$disk->exists($backupPath)) {
                return redirect()->back()->with('error', 'Backup file not found!');
            }

            Log::info('BackupController@download - File: ' . $fileName);

            return $disk->download($backupPath);

        } catch (\Exception $e) {
            Log::error('BackupController@download - ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Error downloading backup: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, $fileName)
    {
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $backupPath = config('backup.backup.name') . '/' . $fileName;

            if (!$disk->exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found!'
                ], 404);
            }

            $disk->delete($backupPath);
            
            Log::info('BackupController@delete - requested by ' . ($request->user()->email ?? null) . ' - File: ' . $fileName);

            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('BackupController@delete - ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clean(Request $request)
    {
        try {
            Artisan::call('backup:clean');
            
            Log::info('BackupController@clean - requested by ' . ($request->user()->email ?? null));

            return response()->json([
                'success' => true,
                'message' => 'Old backups cleaned successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('BackupController@clean - ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error cleaning backups: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getBackups()
    {
        $backups = [];
        
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $backupPath = config('backup.backup.name');
            
            if ($disk->exists($backupPath)) {
                $files = $disk->files($backupPath);
                
                foreach ($files as $file) {
                    $backups[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'size' => $this->formatBytes($disk->size($file)),
                        'date' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                        'timestamp' => $disk->lastModified($file)
                    ];
                }
                
                // Sort by timestamp descending (newest first)
                usort($backups, function($a, $b) {
                    return $b['timestamp'] - $a['timestamp'];
                });
            }
        } catch (\Exception $e) {
            Log::error('BackupController@getBackups - ' . $e->getMessage());
        }
        
        return $backups;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Check system requirements for backup
     */
    private function checkSystem()
    {
        $checks = [
            'mysqldump' => false,
            'zip' => false,
            'storage_writable' => false,
        ];

        // Check mysqldump
        // 1. Check configured path
        $dumpBinaryPath = config('database.connections.mysql.dump.dump_binary_path');
        if ($dumpBinaryPath && file_exists($dumpBinaryPath . '/mysqldump')) {
            $checks['mysqldump'] = true;
        } else {
            // 2. Fallback to 'which' command
            try {
                $process = new Process(['which', 'mysqldump']);
                $process->run();
                $checks['mysqldump'] = $process->isSuccessful();
            } catch (\Exception $e) {
                $checks['mysqldump'] = false;
            }
        }

        // Check zip
        // 1. Check common locations
        $commonZipPaths = [
            '/usr/bin/zip', 
            '/usr/local/bin/zip', 
            '/opt/homebrew/bin/zip',
            '/bin/zip'
        ];
        
        $zipFound = false;
        foreach ($commonZipPaths as $path) {
            if (file_exists($path)) {
                $zipFound = true;
                break;
            }
        }

        if ($zipFound) {
            $checks['zip'] = true;
        } else {
            // 2. Fallback to 'which' command
            try {
                $process = new Process(['which', 'zip']);
                $process->run();
                $checks['zip'] = $process->isSuccessful();
            } catch (\Exception $e) {
                $checks['zip'] = false;
            }
        }

        // Check storage writable
        $storagePath = storage_path('app');
        $checks['storage_writable'] = is_writable($storagePath);

        return $checks;
    }
}
