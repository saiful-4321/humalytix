<?php

namespace App\Modules\Main\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Main\Services\UserService;
use Illuminate\Http\Request;

class UserSessionController extends Controller
{

    private $pageName = "User Session";
    private $view = "Main::pages.session.";

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $result = $this->userService->getUserSession($request);
        $userDropdown = User::list();

        return view($this->view . "index", [
            "pageName" => $this->pageName,
            "userDropdown" => $userDropdown,
            "result"   => $result
        ]);
    }
    
}