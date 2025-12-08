<?php

namespace App\Modules\Main\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request; 
use App\Modules\Main\Enums\ActivityLogEnum;
use App\Modules\Main\Services\ActivityLogService;
use App\Modules\Main\Services\UserService;

class ActivityLogController extends Controller
{
    private $request;
    private $activityLogService;
    private $userService;
    private $view = "Main::pages.activity_log.";

    public function __construct(Request $request, ActivityLogService $activityLogService, UserService $userService)
    {
        $this->request = $request;
        $this->activityLogService = $activityLogService;
        $this->userService = $userService;
    }

    public function index()
    {
        $pageName = "Activity Log";
        $logNames = ActivityLogEnum::getAll();
        $users  = $this->userService->dropdown();
        $result = $this->activityLogService->getAll($this->request);

        return view("{$this->view}index", compact(
            "pageName",
            "users",
            "logNames",
            "result"
        ));
    }

}
