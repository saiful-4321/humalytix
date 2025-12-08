<?php

namespace App\Modules\Main\Services;

use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Models\UserWhiteListedIp;
use Carbon\Carbon; 

class UserWhiteListedIpService
{
    public function getAll($request)
    {
        return UserWhiteListedIp::selectRaw("user_white_listed_ips.*, users.name, users.mobile")
            ->leftJoin("users", "users.id", "user_white_listed_ips.user_id")
            ->where(function($query) use ($request) {
                if ($request->filled("user_id")) {
                    $query->where("user_id", $request->user_id);
                }
                if ($request->filled("ip_address")) {
                    $query->where("ip_address", "like", "%{$request->ip_address}%");
                }
                if ($request->filled("status")) {
                    $query->where("user_white_listed_ips.status", $request->status);
                }
            })
            ->orderBy("user_white_listed_ips.id", "desc")
            ->paginate($request->per_page ?? 10);
    }

    public function getById($id)
    {
        return UserWhiteListedIp::selectRaw("user_white_listed_ips.*, CONCAT(users.name, ' <', users.mobile, '>') as name")
            ->leftJoin("users", "users.id", "user_white_listed_ips.user_id")
            ->find($id);
    }

    public function save($request)
    {
        $user = new UserWhiteListedIp;
        $user->user_id     = $request->user_id; 
        $user->ip_address  = $request->ip_address; 
        $user->status      = ActiveInactiveEnum::Active; 
        $user->created_at  = Carbon::now()->format("Y-m-d H:i:s"); 
        return $user->save();
    }


    public function update($request)
    {
        $user = UserWhiteListedIp::find($request->id);  
        $user->status      = $request->status; 
        $user->updated_at  = Carbon::now()->format("Y-m-d H:i:s"); 
        return $user->save();
    }

}