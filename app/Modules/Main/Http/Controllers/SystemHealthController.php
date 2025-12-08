<?php

namespace App\Modules\Main\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Main\Services\SystemHealthService;

class SystemHealthController extends Controller
{
    private $service;

    public function __construct(SystemHealthService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $health = $this->service->getFullHealth();
        return view('Main::pages.system_health.index', compact('health'));
    }
}
