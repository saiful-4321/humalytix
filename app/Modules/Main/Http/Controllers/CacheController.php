<?php

namespace App\Modules\Main\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CacheController extends Controller
{
    public function index()
    {
        $driver = config('cache.default');
        $cacheData = [];
        $stats = [
            'count' => 0,
            'size' => 0,
        ];

        if ($driver === 'database') {
            try {
                $cacheData = \Illuminate\Support\Facades\DB::table('cache')
                    ->orderBy('expiration', 'desc')
                    ->limit(100)
                    ->get();
                $stats['count'] = \Illuminate\Support\Facades\DB::table('cache')->count();
            } catch (\Exception $e) {
                // Table might not exist
            }
        } elseif ($driver === 'file') {
            $path = config('cache.stores.file.path');
            if (file_exists($path)) {
                $stats['size'] = $this->getDirSize($path);
                $stats['count'] = $this->getFileCount($path);

                if (request()->has('scan')) {
                    $cacheData = $this->scanCacheFiles($path, 50);
                }
            }
        }

        return view('Main::pages.cache.index', compact('driver', 'cacheData', 'stats'));
    }

    private function scanCacheFiles($directory, $limit = 50) {
        $files = [];
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
            );
            
            $count = 0;
            foreach ($iterator as $file) {
                if ($count >= $limit) break;
                
                try {
                    $content = @file_get_contents($file->getPathname());
                    if (!$content) continue;

                    // Laravel stores expiration in first 10 chars
                    $expiration = substr($content, 0, 10); 
                    
                    // The rest is the serialized value
                    $serialized = substr($content, 10);
                    try {
                        $value = unserialize($serialized);
                        // Format complex types for display
                        if (!is_string($value) && !is_numeric($value)) {
                            $value = json_encode($value);
                        }
                    } catch (\Exception $e) {
                        $value = '[Unable to unserialize data]';
                    }

                    $files[] = (object) [
                        'key' => $file->getFilename() . ' (Hash)',
                        'expiration' => (int)$expiration,
                        'value' => $value
                    ];
                    $count++;
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            // Directory access error
        }
        return $files;
    }

    private function getDirSize($directory) {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }
        return round($size / 1024 / 1024, 2); // MB
    }

    private function getFileCount($directory) {
        $count = 0;
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                $count++;
            }
        } catch (\Exception $e) {
            return 0;
        }
        return $count;
    }

    public function clearCache(Request $request)
    {
        try {
            $type = $request->input('type', 'all');
            $output = [];

            switch ($type) {
                case 'application':
                    Artisan::call('cache:clear');
                    $output[] = 'Application cache cleared successfully!';
                    break;

                case 'config':
                    Artisan::call('config:clear');
                    $output[] = 'Configuration cache cleared successfully!';
                    break;

                case 'route':
                    Artisan::call('route:clear');
                    $output[] = 'Route cache cleared successfully!';
                    break;

                case 'view':
                    Artisan::call('view:clear');
                    $output[] = 'View cache cleared successfully!';
                    break;

                case 'event':
                    Artisan::call('event:clear');
                    $output[] = 'Event cache cleared successfully!';
                    break;

                case 'all':
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    Artisan::call('event:clear');
                    $output[] = 'All caches cleared successfully!';
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid cache type'
                    ], 400);
            }

            Log::info('CacheController@clearCache - requested by ' . ($request->user()->email ?? null) . ' - Type: ' . $type);

            return response()->json([
                'success' => true,
                'message' => implode(' ', $output)
            ]);

        } catch (\Exception $e) {
            Log::error('CacheController@clearCache - ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recache(Request $request)
    {
        try {
            $type = $request->input('type', 'all');
            $output = [];

            switch ($type) {
                case 'config':
                    Artisan::call('config:cache');
                    $output[] = 'Configuration cached successfully!';
                    break;

                case 'route':
                    Artisan::call('route:cache');
                    $output[] = 'Routes cached successfully!';
                    break;

                case 'view':
                    Artisan::call('view:cache');
                    $output[] = 'Views cached successfully!';
                    break;

                case 'event':
                    Artisan::call('event:cache');
                    $output[] = 'Events cached successfully!';
                    break;

                case 'all':
                    Artisan::call('config:cache');
                    Artisan::call('route:cache');
                    Artisan::call('view:cache');
                    Artisan::call('event:cache');
                    $output[] = 'All caches rebuilt successfully!';
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid cache type'
                    ], 400);
            }

            Log::info('CacheController@recache - requested by ' . ($request->user()->email ?? null) . ' - Type: ' . $type);

            return response()->json([
                'success' => true,
                'message' => implode(' ', $output)
            ]);

        } catch (\Exception $e) {
            Log::error('CacheController@recache - ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error recaching: ' . $e->getMessage()
            ], 500);
        }
    }

    public function optimize(Request $request)
    {
        try {
            Artisan::call('optimize');
            
            Log::info('CacheController@optimize - requested by ' . ($request->user()->email ?? null));

            return response()->json([
                'success' => true,
                'message' => 'Application optimized successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('CacheController@optimize - ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error optimizing: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearOptimize(Request $request)
    {
        try {
            Artisan::call('optimize:clear');
            
            Log::info('CacheController@clearOptimize - requested by ' . ($request->user()->email ?? null));

            return response()->json([
                'success' => true,
                'message' => 'Optimization cleared successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('CacheController@clearOptimize - ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error clearing optimization: ' . $e->getMessage()
            ], 500);
        }
    }
}
