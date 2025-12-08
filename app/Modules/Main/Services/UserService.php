<?php

namespace App\Modules\Main\Services;

use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Models\User;
use App\Modules\BoAccount\Models\BoAccountApprovalActionRemark;
use App\Modules\Main\Enums\PaymentStatusEnum;
use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Enums\UserTokenTypeEnum;
use App\Modules\Main\Models\Scopes\UserScope;
use App\Modules\Main\Models\UserSession;
use App\Modules\Main\Utilities\DocumentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function getAll($request)
    {
            return User::select("users.*")
                ->where(function($query) use ($request) {
                    if ($request->filled("agent_id")) {
                        $query->where("users.agent_id", $request->agent_id);
                    }
                    if ($request->filled("user_id")) {
                        $query->where("users.id", $request->user_id);
                    }
                    if ($request->filled("name")) {
                        $query->where("users.name", "like", "%{$request->name}%");
                    }
                    if ($request->filled("email")) {
                        $query->where("users.email", $request->email);
                    }
                    if ($request->filled("mobile")) {
                        $query->whereIn("users.mobile", msisdns($request->mobile));
                    }
                    if ($request->filled("nid")) {
                        $query->where("users.nid", $request->nid);
                    }
                    if ($request->filled("status")) {
                        $query->where("users.status", $request->status);
                    }
                    if ($request->filled("payment_status")) {
                        $query->whereIn("users.payment_status", $request->payment_status);
                    }
                    if ($request->filled("bo_status")) {
                        $query->where("users.bo_status", $request->bo_status);
                    }
                    if ($request->filled("role")) {
                        $query->orWhereHas("roles", function($query) use($request) {
                            $query->where("roles.name", $request->role);
                        });
                    }
                })
                ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->orderBy("users.id", "desc")
                ->paginate($request->per_page ?? 10);
    }

    public function getById($id)
    {
        return User::find($id);
    }

    public function createOrUpdate($request)
    {
        if (!empty($request->id)) {
            $user = User::find($request->id); 
        } else {
            $user = new User;
            // unchangeable email
            $user->email = $request->email;
            $user->api_token = getApiToken(); 
            $user->status    = UserStatusEnum::Pending;
        }

        $user->name     = $request->name;

        if ($request->filled('password')) {
            $user->password  = Hash::make($request->password);
        }
        
        $user->mobile        = msisdn($request->mobile);
        $user->nid           = $request->nid;
        $user->date_of_birth = Carbon::parse($request->date_of_birth)->format("Y-m-d");
        $user->remember_token = getApiToken();

        if ($request->filled('refresh_token')) {
            $user->api_token = getApiToken();
        }

        if ($request->filled('status')) {
            $user->status    = $request->status;
        } else {
            $user->status    = UserStatusEnum::Verified;
        }
        
        $user->save();

        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        // refresh snactum token
        if ($request->filled('refresh_token')) {
            $user->createToken(UserTokenTypeEnum::API);
        }
 
        return $user;
    }
 
    public function getUsersWithNameAndMobile()
    {
        return User::selectRaw("id, CONCAT(name, ' <',  mobile, '>') as name")
            ->where("status", ActiveInactiveEnum::Active)
            ->pluck("name", "id");
    }

    public function changePassword($request)
    {
        $user = User::withoutGlobalScope(new UserScope)->find($request->id ?? auth()->user()->id);
        Log::info($user);
        $user->password = Hash::make($request->password);
        $user->updated_by        = auth()->user()->id;
        $user->updated_at        = Carbon::now()->format("Y-m-d H:i:s"); 
        return $user->save();
    }

    public function approvalUpdate($request)
    {
        $actionType = ($request->action_type == UserStatusEnum::Approved ? UserStatusEnum::Approved : UserStatusEnum::Rejected);

        $user = User::find($request->id);
        $user->status = $actionType;

        if ($request->action_type == UserStatusEnum::Approved && !empty($request->client_code)) {
            $user->client_code = strtoupper($request->client_code);
        }

        $user->updated_at = Carbon::now();
        $user->save();

        BoAccountApprovalActionRemark::create([
            "user_id"      => $request->id,
            "remark"       => $request->reason ?? null,
            "status"       => $actionType,
            "action_by"    => auth()->user()->id ?? null,
            "created_at"   => Carbon::now(),
        ]);
        
        return $user;
    }

    public function resendRequest($request)
    {
        $user = User::find($request->id);
        $user->status = UserStatusEnum::Completed;
        $user->updated_at = Carbon::now();
        return $user->save();
    }

    public function cdblApproval($request)
    {
        $path = DocumentService::upload($request, 'file', 'bo_cdbl_approval_file');
        if (!$path) {
            return false;
        }

        $user = User::find($request->id);
        $user->bo_status = ActiveInactiveEnum::Active;
        $user->bo_cdbl_approval_file = $path;
        $user->updated_at = Carbon::now();
        return $user->save();
    }


    public function getApprovalRevisionByUserId($id)
    {
        return BoAccountApprovalActionRemark::query()
            ->select(["bo_account_approval_action_remark.*", "u.name", "u.email"])
            ->leftJoin("users As u", "u.id", "action_by")
            ->where("user_id", $id)
            ->get();
    }

    public function getUserSession($request)
    {
        $users = UserSession::with(['user', 'pretend_user']);
        if ($request->filled('user_id')) { 
            $users->where('user_id', $request->user_id);
        }
        if ($request->filled('pretend_user_name')) {
            $users->whereHas('pretend_user', function ($q) use ($request) {
                $q->where('email', $request->pretend_user_name);
            });
        }
        if ($request->filled('is_pretend')) {
            $users->where('is_pretend', $request->is_pretend);
        }
        if ($request->filled('guard')) {
            $users->where('guard', $request->guard);
        }
        if ($request->filled('start_date')) {
            $users->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $users->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $users->where('status', $request->status);
        }

        return $users->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 10);
    }

    public function saveUserSession($type, $pretendId = null, $guard = 'web')
    {
        try {
            $sessionId = session()->getId();
            if ($type === 'login') { 
                UserSession::insert([
                    'session_id'   => $sessionId,
                    'ip_address'   => request()->ip(),
                    'guard'        => $guard,
                    'user_id'      => auth()->id(),
                    'is_pretend'   => !empty($pretendId) ? "Yes" : "No",
                    'pretend_user_id' => $pretendId,
                    'user_agent'   => request()->userAgent(),
                    'logged_in_at' => now(),
                ]);
            } elseif ($type === 'logout') {
                UserSession::where('session_id', $sessionId)->update(['logged_out_at' => now()]);
            }
        } catch (Exception $e) {
            Log::error("UserService@saveUserSession - Error: " . $e->getMessage());
        }
    }
 
    public function dropdown()
    {
        return User::whereNotIn('status', [UserStatusEnum::Pending])
            ->pluck(DB::raw("CONCAT(name, ' (', email, ')' ) AS name"), 'id');
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $user = User::where(function($query) use($id) {
                    if (auth()->hasUser() && auth()->user()->hasRole(['Agent'])) {
                        $query->where("created_by", auth()->user()->id);
                    }
                })
                ->where("id", $id)
                ->whereNotIn("payment_status", [PaymentStatusEnum::Processing, PaymentStatusEnum::Paid])
                ->firstOrFail();

            $user->boAccount()->delete();
            $user->boAuthorizeInfo()->delete();
            $user->boBankInfo()->delete();
            $user->boNomineeInfo()->delete();
            $user->boDocuments()->delete();
            $user->boPayments()->delete();
            $user->boApprovalAction()->delete();
            $user->ekyc()->delete();
            $user->delete();

            DB::commit();
            
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("UserService@delete - Error: ". json_encode($e));
            return false;
        }
    }


    public function getSummary($startDate = null, $endDate = null)
    {
        $query = User::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->selectRaw("
                count(*) as total,
                sum(CASE WHEN status='Pending' THEN 1 ELSE 0 END) AS total_pending,
                sum(CASE WHEN status='Verified' THEN 1 ELSE 0 END) AS total_verified,
                sum(CASE WHEN status='inprogress' THEN 1 ELSE 0 END) AS total_inprogress,
                sum(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS total_completed,
                sum(CASE WHEN status='approved' THEN 1 ELSE 0 END) AS total_approved,
                sum(CASE WHEN status='rejected' THEN 1 ELSE 0 END) AS total_rejected
            ")
            ->first();
    }

    public function getMonthlyUserTrend($startDate = null, $endDate = null)
    {
        $query = User::query();
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date, count(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }


}