<?php

namespace App\Modules\Main\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Jobs\CredentialSendMailJob;
use App\Modules\Main\Services\UserService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{

    private $pageName = "Dashboard";
    private $view = "Main::pages.dashboard.";
    private $userService;
    private $systemHealthService;

    public function __construct(UserService $userService, \App\Modules\Main\Services\SystemHealthService $systemHealthService)
    {
        $this->userService = $userService;
        $this->systemHealthService = $systemHealthService;
    }

    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $summary = $this->userService->getSummary($startDate, $endDate);
        $trend = $this->userService->getMonthlyUserTrend($startDate, $endDate);
        $systemHealth = $this->systemHealthService->getSystemHealth();

        return view($this->view . "home", [
            'pageName' => $this->pageName,
            'summary' => $summary,
            'trend' => $trend,
            'systemHealth' => $systemHealth,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }
}
