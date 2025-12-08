<?php

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Models\CompanySetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CompanyService
{
    public const CACHE_KEY = 'company_settings';

    /**
     * Get company settings.
     *
     * @param string|null $key Specific key to retrieve.
     * @return mixed
     */
    public function get($key = null)
    {
        $settings = Cache::rememberForever(self::CACHE_KEY, function () {
            $data = CompanySetting::first();
            if ($data) {
                return $data->toArray();
            }
            return Config::get('common.company', []);
        });

        if ($key) {
            return $settings[$key] ?? null;
        }

        return $settings;
    }

    /**
     * Clear the company settings cache.
     *
     * @return void
     */
    public function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }
}
