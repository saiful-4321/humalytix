<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\ThemeSetting;
use App\Modules\Settings\Services\ThemeService;
use Illuminate\Http\Request;

class ThemeSettingController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        $setting = ThemeSetting::first();
        
        if (!$setting) {
            $setting = new ThemeSetting(config('common.theme', []));
        }

        return view('Settings::theme_setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'layout_type' => 'nullable|string',
            'layout_width' => 'nullable|string',
            'layout_position' => 'nullable|string',
            'topbar_color' => 'nullable|string',
            'topbar_custom_color' => 'nullable|string',
            'sidebar_color' => 'nullable|string',
            'sidebar_custom_color' => 'nullable|string',
            'sidebar_size' => 'nullable|string',
            'footer_color' => 'nullable|string',
            'footer_custom_color' => 'nullable|string',
            'footer_enabled' => 'nullable|boolean',
            'layout_mode' => 'nullable|string',
            'body_color' => 'nullable|string',
            'body_custom_color' => 'nullable|string',
        ]);

        $setting = ThemeSetting::first();
        if (!$setting) {
            $setting = new ThemeSetting();
        }

        $data = $request->all();
        // Handle checkbox - if not present, it's unchecked (0)
        $data['footer_enabled'] = $request->has('footer_enabled') ? 1 : 0;
        
        $setting->fill($data);
        $setting->save();

        $this->themeService->clearCache();

        return redirect()->back()->with('success', 'Theme settings updated successfully.');
    }

    public function reset()
    {
        $setting = ThemeSetting::first();
        if ($setting) {
            $setting->delete();
        }
        
        $this->themeService->clearCache();

        return redirect()->back()->with('success', 'Theme settings reset to default.');
    }
}
