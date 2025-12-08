<?php

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Models\ThemeSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ThemeService
{
    public const CACHE_KEY = 'theme_settings';

    /**
     * Get theme settings.
     *
     * @param string|null $key Specific key to retrieve.
     * @return mixed
     */
    public function get($key = null)
    {
        $settings = Cache::rememberForever(self::CACHE_KEY, function () {
            $data = ThemeSetting::first();
            if ($data) {
                return $data->toArray();
            }
            return Config::get('common.theme', []);
        });

        if ($key) {
            return $settings[$key] ?? null;
        }

        return $settings;
    }

    /**
     * Clear the theme settings cache.
     *
     * @return void
     */
    public function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Determine if a hex color is dark.
     *
     * @param string $hexColor
     * @return bool
     */
    public function isDarkColor($hexColor)
    {
        // Handle gradients or other CSS values by extracting the first hex color
        if (preg_match('/#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})/', $hexColor, $matches)) {
            $hexColor = $matches[0];
        } else {
            // Default to dark if no hex found (safe fallback for complex backgrounds)
            return true;
        }

        $hexColor = ltrim($hexColor, '#');

        if (strlen($hexColor) === 3) {
            $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
        }

        // Validate hex length
        if (strlen($hexColor) != 6) {
            return true;
        }

        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        // YIQ equation
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $yiq < 128;
    }
}
