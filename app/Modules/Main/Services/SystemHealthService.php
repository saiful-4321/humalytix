<?php

namespace App\Modules\Main\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemHealthService
{
    public function getSystemHealth()
    {
        return [
            'disk' => $this->getDiskUsage(),
            'memory' => $this->getMemoryUsage(),
            'database' => $this->getDatabaseSize(),
            'system' => [
                'php' => phpversion(),
                'laravel' => app()->version(),
                'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            ]
        ];
    }

    public function getFullHealth()
    {
        return [
            'disk' => $this->getDiskUsage(),
            'memory' => $this->getMemoryUsage(),
            'database' => $this->getDatabaseDetails(),
            'server' => $this->getServerDetails(),
            'php' => $this->getPhpDetails(),
            'application' => $this->getApplicationDetails(),
        ];
    }

    private function getServerDetails()
    {
        return [
            'os' => php_uname(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'client_ip' => request()->ip(),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
        ];
    }

    private function getPhpDetails()
    {
        return [
            'version' => phpversion(),
            'extensions' => get_loaded_extensions(),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
    }

    private function getApplicationDetails()
    {
        return [
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'url' => config('app.url'),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'locale' => app()->getLocale(),
        ];
    }

    private function getDatabaseDetails()
    {
        try {
            $connection = config('database.default');
            $size = 0;
            $tableCount = 0;
            $version = 'Unknown';

            if ($connection == 'mysql') {
                $dbName = config('database.connections.mysql.database');
                
                // Size
                $sizeResult = DB::select('SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS "size"
                    FROM information_schema.TABLES 
                    WHERE table_schema = ?', [$dbName]);
                $size = $sizeResult[0]->size ?? 0;

                // Tables
                $tablesResult = DB::select('SELECT COUNT(*) as count FROM information_schema.TABLES WHERE table_schema = ?', [$dbName]);
                $tableCount = $tablesResult[0]->count ?? 0;

                // Version
                $versionResult = DB::select('SELECT VERSION() as version');
                $version = $versionResult[0]->version ?? 'Unknown';
            }
            
            // Basic support for other drivers could be added here
            
            return [
                'connection' => $connection,
                'database' => $dbName ?? 'N/A',
                'version' => $version,
                'size' => $size,
                'tables' => $tableCount
            ];

        } catch (\Exception $e) {
            Log::error('SystemHealthService - DB Details Error: ' . $e->getMessage());
            return [
                'connection' => 'Error',
                'size' => 0,
                'tables' => 0,
                'version' => 'Unknown'
            ];
        }
    }

    private function getDiskUsage()
    {
        try {
            $total = disk_total_space(base_path());
            $free = disk_free_space(base_path());
            $used = $total - $free;
            
            return [
                'total' => $this->formatBytes($total),
                'free' => $this->formatBytes($free),
                'used' => $this->formatBytes($used),
                'percentage' => round(($used / $total) * 100, 1)
            ];
        } catch (\Exception $e) {
            Log::error('SystemHealthService - Disk Usage Error: ' . $e->getMessage());
            return null;
        }
    }

    private function getMemoryUsage()
    {
        // This gets the memory allocated to the PHP script, not total system RAM
        // Getting total system RAM requires shell_exec which might be disabled
        $usage = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);
        $limit = ini_get('memory_limit');

        return [
            'usage' => $this->formatBytes($usage),
            'peak' => $this->formatBytes($peak),
            'limit' => $limit
        ];
    }

    private function getDatabaseSize()
    {
         // Legacy alias for dashboard widget
         $details = $this->getDatabaseDetails();
         return $details['size'];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
