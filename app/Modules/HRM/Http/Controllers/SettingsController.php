<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return view('HRM::pages.settings.index');
    }

    public function general()
    {
        $weeklyHolidays = \Illuminate\Support\Facades\DB::table('hrm_settings')
            ->where('name', 'weekly_holidays')
            ->value('payload');
            
        $weeklyHolidays = $weeklyHolidays ? json_decode($weeklyHolidays, true) : [];

        return view('HRM::pages.settings.general.index', compact('weeklyHolidays'));
    }

    public function storeGeneral(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'weekly_holidays' => 'array',
            'weekly_holidays.*' => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'
        ]);

        $payload = json_encode($request->weekly_holidays ?? []);

        \Illuminate\Support\Facades\DB::table('hrm_settings')->updateOrInsert(
            ['name' => 'weekly_holidays'],
            [
                'group' => 'general',
                'payload' => $payload,
                'updated_at' => now(),
                'created_at' => now() // logic will ignore this on update, which is fine
            ]
        );

        return redirect()->back()->with('success', 'General settings updated successfully.');
    }
}
