<?php

namespace App\Modules\Main\Http\Controllers;

use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Http\Requests\UserWhitelistedIpRequest;
use App\Modules\Main\Services\UserService;
use App\Modules\Main\Services\UserWhiteListedIpService;
use App\Modules\Main\Utilities\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class UserWhiteListedIpController extends Controller
{
    private $view = "Main::pages.user_whitelisted_ip.";
    private $pageName = "User White Listed IP";
    private $userWhiteListedIpService;
    private $userService;

    public function __construct(UserWhiteListedIpService $userWhiteListedIpService, UserService $userService)
    {
        $this->userWhiteListedIpService = $userWhiteListedIpService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $status = ActiveInactiveEnum::getAll();
        $users  = $this->userService->getUsersWithNameAndMobile();
        $result = $this->userWhiteListedIpService->getAll($request);

        return view($this->view . "index", [
            "pageName" => $this->pageName,
            "status"   => $status,
            "users"   => $users,
            "result"   => $result
        ]);
    }

    public function create()
    {
        $data = (object)[
            'page'   => $this->pageName,
            'method' => 'Create',
            'action' => route('dashboard.user-whitelisted-ip.store'),
            'users'  => $this->userService->getUsersWithNameAndMobile(),
            'item'   => [],
        ];

        return view($this->view . 'form', compact('data'));
    }

    public function store(UserWhitelistedIpRequest $request)
    {
        try {
            
            $save = $this->userWhiteListedIpService->save($request);
            
            if ($save) {
                return JsonResponse::success('Save Successful!');
            } else {
                return JsonResponse::internalError('Please try again!');
            }
            
        } catch(\Exception $e) {
            Log::error('UserWhiteListedIpController@store - '. $e->getMessage());
            return JsonResponse::internalError('Something went wrong!');
        }
    }


    public function edit($id)
    {
        $data = (object)[
            'page'   => $this->pageName,
            'method' => 'Update',
            'action' => route('dashboard.user-whitelisted-ip.update', $id),
            'users'  => $this->userService->getUsersWithNameAndMobile(),
            'item'   => $this->userWhiteListedIpService->getById($id),
        ]; 
        
        return view($this->view . 'form', compact('data'));
    }


    public function update(UserWhitelistedIpRequest $request, $id)
    {
        try {
        
            $update = $this->userWhiteListedIpService->update($request);
            
            if ($update) {
                return JsonResponse::success('Update Successful!');
            } else {
                return JsonResponse::internalError('Please try again!');
            }
            
        } catch(\Exception $e) {
            Log::error('UserWhiteListedIpController@update - '. $e->getMessage());
            return JsonResponse::internalError('Something went wrong!');
        }
    }

}
