<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\CompanySetting;
use App\Modules\Settings\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        $setting = CompanySetting::first();
        // If no record exists, we might want to show empty or default values.
        // But for editing, we usually want to show what's in DB or empty if nothing.
        // However, the prompt asked to use config as default.
        // Let's pass the object if it exists, otherwise we can pass an object with config values or just null and handle in view.
        // To make it easy for the view, let's pass the model instance if exists, else new instance with config values filled?
        // Actually, let's just pass the model. If null, the view should handle it or we create a new instance.
        
        if (!$setting) {
            $setting = new CompanySetting(config('common.company', []));
        }

        return view('Settings::company_setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            // Add other validations as needed
            'logo_white' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_white_small' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_dark_small' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'registration_active' => 'nullable',
            'password_reset_active' => 'nullable',
        ]);

        $setting = CompanySetting::first();
        if (!$setting) {
            $setting = new CompanySetting();
        }

        $data = $request->except(['logo_white', 'logo_dark', 'logo_white_small', 'logo_dark_small', 'favicon', '_token']);
        $data['registration_active'] = $request->has('registration_active') ? 1 : 0;
        $data['password_reset_active'] = $request->has('password_reset_active') ? 1 : 0;

        // Handle File Uploads
        $fields = ['logo_white', 'logo_dark', 'logo_white_small', 'logo_dark_small', 'favicon'];
        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($setting->$field) {
                    $relativePath = str_replace('storage/', '', $setting->$field);
                    if (Storage::disk('public')->exists($relativePath)) {
                        Storage::disk('public')->delete($relativePath);
                    }
                }
                
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $path = 'uploads/company';
                
                $filePath = $file->storeAs($path, $filename, 'public');
                $data[$field] = 'storage/' . $filePath;
            }
        }

        $setting->fill($data);
        $setting->save();

        $this->companyService->clearCache();

        return redirect()->back()->with('success', 'Company settings updated successfully.');
    }
}
