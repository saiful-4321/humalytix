<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Enums\UserTokenTypeEnum;
use Carbon\Carbon;

class AuthService
{
    public function saveUser($request)
    {
        $user = new User;
        $user->name   = $request->name;
        $user->email  = $request->email;
        $user->mobile = msisdn($request->mobile);
        $user->nid    = null;
        $user->date_of_birth = null;
        $user->status = UserStatusEnum::Pending;
        $user->remember_token = getApiToken();
        $user->save();

        $user->createToken(UserTokenTypeEnum::API);
        $user->syncRoles("Agent");
        
        $user->agent_id = $user->id;
        $user->created_by = auth()->hasUser() ? auth()->user()->id : $user->id;
        $user->save();

        return $user;
    }

    public function   getByEmail($email)
    {
        return User::selectRaw("users.*, roles.name AS role_name")
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereNotIn('roles.name', ['User'])
            ->where("email", trim($email))
            ->first();
 

    }
}