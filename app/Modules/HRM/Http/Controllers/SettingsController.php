<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return view('HRM::pages.settings.index');
    }
}
