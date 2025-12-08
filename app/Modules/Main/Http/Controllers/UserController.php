<?php

namespace App\Modules\Main\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Http\Requests\PasswordChangeRequest;
use App\Modules\Main\Http\Requests\PasswordResetRequest;
use App\Modules\Main\Utilities\JsonResponse;
use App\Modules\Main\Http\Requests\UserRequest;
use App\Modules\Main\Models\Role;
use App\Modules\Main\Services\EkycProgressbarService;
use App\Modules\Main\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Modules\Settings\Models\CompanySetting;

class UserController extends Controller
{
    private $view = "Main::pages.user.";
    private $pageName = "Users";
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $status = UserStatusEnum::getAll();
        $userDropdown = User::list();
        $roles  = Role::whereNotIn("name", ["Super Admin", "User"])
                    ->pluck('name', 'name')
                    ->toArray();
        $result = $this->userService->getAll($request);
        
        return view($this->view . "index", [
            "pageName"  => $this->pageName,
            "roles"     => $roles,
            "userDropdown" => $userDropdown,
            "status"    => $status,
            "result"    => $result
        ]);
    }

    public function create()
    {
        $data = (object)[
            'page'   => 'User',
            'method' => 'Create',
            'action' => route('dashboard.user.store'),
            'roles'  => Role::whereNotIn("name", ["Super Admin", "User"])
                        ->pluck('name', 'name')
                        ->toArray(), 
            'status' => UserStatusEnum::getAll(),
            'item'   => [],
        ]; 

        return view($this->view . 'form', compact('data'));
    }

    public function store(UserRequest $request)
    {
        try {
            
            $user = $this->userService->createOrUpdate($request);

            // save form progress
            EkycProgressbarService::save($user->id, 'basic_registration');
            EkycProgressbarService::save($user->id, 'otp_verification');
            
            if ($user) {
                return JsonResponse::success('Save Successful!');
            } else {
                return JsonResponse::internalError('Please try again!');
            }
            
        } catch(\Exception $e) {
            Log::error('UserController@store - Error: '. $e->getMessage() . ', File: '. $e->getFile() .', Line:'. $e->getLine());
            return JsonResponse::internalError('Something went wrong!');
        }
    }


    public function edit($id)
    {
        $data = (object)[
            'page'   => 'User',
            'method' => 'Update',
            'action' => route('dashboard.user.update', $id),
            'roles'  => Role::whereNotIn("name", ["Super Admin", "User"])
                        ->pluck('name', 'name')
                        ->toArray(), 
            'status' => UserStatusEnum::getAll(),
            'item'   => $this->userService->getById($id),
        ]; 
         
        return view($this->view . 'form', compact('data'));
    }


    public function update(UserRequest $request, $id)
    {
        try {
            $update = $this->userService->createOrUpdate($request);
            
            if ($update) {
                return JsonResponse::success('Update Successful!');
            } else {
                return JsonResponse::internalError('Please try again!');
            }
            
        } catch(\Exception $e) {
            Log::error('UserController@update - Error: '. $e->getMessage() . ', File: '. $e->getFile() .', Line:'. $e->getLine());
            return JsonResponse::internalError('Something went wrong!');
        }
    }

    public function delete($id)
    {
        try {

            if (auth()->user()->id == $id) {
                return back()->withErrors("You can't delete yourself!");
            }

            $agentHasBo = User::selectRaw("CONCAT(name, ' - ', mobile) AS cuser")
                ->where("agent_id", $id)
                ->whereNot('id', $id)
                ->pluck("cuser")
                ->toArray();
            if (!empty($agentHasBo)) {
                return back()->withErrors("This agent has some BO, please delete these BO before deleting this user. The available BO are :- " . implode(' | ', $agentHasBo));
            }
            
            $delete = $this->userService->delete($id);

            if ($delete) {
                return back()->with('success', 'Delete Successful!');
            } else {
                return back()->withErrors('Please try again!');
            }
            
        } catch(\Exception $e) {
            Log::error('UserController@delete - Error: '. $e->getMessage() . ', File: '. $e->getFile() .', Line:'. $e->getLine());
            return back()->withErrors('Something went wrong!');
        }
    }

    // profile view
    public function profile()
    {
        $item = $this->userService->getById(auth()->user()->id);
        $roles = Role::pluck('name', 'name');

        return view($this->view.'profile', compact(
            'item',
            'roles'
        ));
    }

    // profile edit
    public function profileEdit()
    {
        $data = (object)[
            'page'   => 'Profile',
            'method' => 'Update',
            'action' => route('dashboard.user.profile.update', auth()->user()->id),
            'item'   => $this->userService->getById(auth()->user()->id),
        ]; 
        
        return view($this->view . 'profile-form', compact('data'));
    }

    // password reset
    public function resetPassword(Request $request)
    {
        $data = (object)[
            'page'   => '',
            'method' => 'Reset Password',
            'action' => route('dashboard.user.reset-password', $request->id),
        ]; 
        
        return view($this->view . 'reset-password', compact('data'));
    }

    public function restPasswordUpdate(PasswordResetRequest $request)
    {
        try {
            $update = $this->userService->changePassword($request);
            
            if ($update) {
                return JsonResponse::success('Password Reset Successful!');
            } else {
                return JsonResponse::internalError('Please try again!');
            }
            
        } catch(\Exception $e) {
            Log::error('UserController@restPasswordUpdate - Error: '. $e->getMessage() . ', File: '. $e->getFile() .', Line:'. $e->getLine());
            return JsonResponse::internalError('Something went wrong!');
        }
    }
    

    // password change
    public function changePassword(Request $request)
    {
        $data = (object)[
            'page'   => '',
            'method' => 'Change Password',
            'action' => route('dashboard.user.profile.password-update'),
        ];
        
        return view($this->view . 'change-password', compact('data'));
    }

    public function passwordUpdate(PasswordChangeRequest $request)
    {
        try {
            $update = $this->userService->changePassword($request);
            
            if ($update) {
                return JsonResponse::success('Update Successful!');
            } else {
                return JsonResponse::internalError('Please try again!');
            }
            
        } catch(\Exception $e) {
            Log::error('UserController@passwordUpdate - Error: '. $e->getMessage() . ', File: '. $e->getFile() .', Line:'. $e->getLine());
            return JsonResponse::internalError('Something went wrong!');
        }
    }

    public function pretendLogin(Request $request, $id)
    {
        try {

            createOrUpdateUserSession('login', $id);

            // keep impresonate id for pretend user back
            session([
                'impersonate_id'  => auth()->user()->id,
                'impersonate_url' => url()->previous()
            ]);
            
            // Delete all user sessions except the current one
            session()->getHandler()->destroy(
                session()->getId()
            );

            if (Auth::loginUsingId($id)) { 
                session()->put('user_login_token', uniqid());
                return redirect()->route('dashboard.home')->with('success', 'Pretend login successful!');
            } 
            
            return redirect()->back()->with('error', 'Please try again!');
            
        } catch(\Exception $e) {
            Log::error('UserController@pretendLogin - Error: '. $e->getMessage() . ', File: '. $e->getFile() .', Line:'. $e->getLine());
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function removePretend(Request $request)
    {
        if (session()->has('impersonate_id')) {
            $impersonateId  = session()->get('impersonate_id'); 
            $impersonateUrl = session()->get('impersonate_url'); 

            // Delete all user sessions except the current one
            session()->getHandler()->destroy(
                session()->getId()
            );

            if (Auth::loginUsingId($impersonateId)) { 
                session()->put('user_login_token', uniqid());
                session()->forget('impersonate_id'); 
                session()->forget('impersonate_url'); 
                return redirect($impersonateUrl)->with('success', 'Pretend login removed successfully!');
            }
        }
        return redirect()->back()->with('error', 'Please try again!');
    } 

    public function export(Request $request) {
        set_time_limit(0); 
        ini_set('memory_limit', '-1');

        $format = $request->query('format', 'xlsx');
        
        // Build Query (Replicating UserService filters)
        $query = User::query()
            ->with(['roles'])
            ->where(function($q) use ($request) {
                if ($request->filled("agent_id")) $q->where("agent_id", $request->agent_id);
                if ($request->filled("user_id")) $q->where("id", $request->user_id);
                if ($request->filled("name")) $q->where("name", "like", "%{$request->name}%");
                if ($request->filled("email")) $q->where("email", $request->email);
                if ($request->filled("mobile")) $q->whereIn("mobile", msisdns($request->mobile));
                if ($request->filled("nid")) $q->where("nid", $request->nid);
                if ($request->filled("status")) $q->where("status", $request->status);
                if ($request->filled("role")) {
                    $q->whereHas("roles", function($q2) use($request) {
                        $q2->where("name", $request->role);
                    });
                }
            })
            ->orderBy("id", "desc");

        if ($format == 'pdf') {
            // PDF Limit: 1000 rows
            $result = $query->limit(1000)->get();
            
            $company = CompanySetting::first();
            $pdf = Pdf::loadView('Main::pages.user.pdf', compact('result', 'company'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('users.pdf');
        }
        
        // Excel/CSV Generator
        return (new FastExcel($this->userGenerator($query)))->download('users.'.$format, function ($user) {
            return [
                'Name' => $user->name,
                'Email' => $user->email,
                'Mobile' => $user->mobile, 
                'Roles' => $user->roles->pluck('name')->implode(', '),
                'Status' => $user->status,
                'Join Date' => optional($user->created_at)->format('Y-m-d'),
            ];
        });
    }

    private function userGenerator($query) {
        foreach ($query->cursor() as $user) {
            yield $user;
        }
    }

    public function sample(Request $request) {
        $format = $request->query('format', 'xlsx');
        $sampleData = collect([
            [
                'Name' => 'John Doe',
                'Email' => 'john@example.com', 
                'Phone' => '01XXXXXXXXX',
                'Password' => 'secret123',
                'Role' => 'User'
            ]
        ]);
        return (new FastExcel($sampleData))->download('sample_users.'.$format);
    }

    public function import(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);
        
        $dryRun = $request->has('dry_run');
        
        $file = $request->file('file');
        // Create a temp file with correct extension to ensure FastExcel works
        $tempPath = sys_get_temp_dir() . '/import_' . uniqid() . '.' . $file->getClientOriginalExtension();
        copy($file->getRealPath(), $tempPath);

        try {
            if ($dryRun) {
                ini_set('memory_limit', '-1');
                set_time_limit(0);
                
                $previewData = [];
                $search = $request->input('preview_search');

                // Performance Optimization: Cache existing emails to avoid 100k DB queries
                $existingEmails = User::pluck('email')->map(fn($e) => strtolower($e))->flip()->toArray();
                
                (new FastExcel)->import($tempPath, function ($originalLine) use (&$previewData, $search, $existingEmails) {
                    $originalLine = (array) $originalLine; // Ensure array
                    $line = array_change_key_case($originalLine, CASE_LOWER);
                    
                    // Search Filter
                    if ($search) {
                        $rowValues = implode(' ', array_values($originalLine));
                        if (stripos($rowValues, $search) === false) {
                            return; // Skip if doesn't match
                        }
                    }
                    
                    $status = 'New';
                    if (isset($line['email']) && isset($existingEmails[strtolower($line['email'])])) {
                        $status = 'Update';
                    }
                    $previewData[] = array_merge($originalLine, ['_status' => $status]);
                    
                    // No Limit
                });
                
                return JsonResponse::success('Preview', ['preview' => $previewData, 'count' => count($previewData)]);
            }
            
            // Actual Import - Process Row by Row to save memory
            set_time_limit(0); 
            ini_set('memory_limit', '-1');

            // Optimization 1: Pre-hash default password to avoid 100k bcrypt calls (HUGE speedup)
            $defaultPass = '12345678';
            $defaultHash = bcrypt($defaultPass);
            
            DB::beginTransaction();
            $count = 0;
            
            (new FastExcel)->import($tempPath, function ($originalLine) use (&$count, $defaultPass, $defaultHash) {
                $originalLine = (array) $originalLine; 
                $line = array_change_key_case($originalLine, CASE_LOWER);
    
                if (!isset($line['email'])) return; 
    
                $password = $line['password'] ?? $defaultPass;
                // Use pre-calculated hash if password is default, else hash it (slow)
                $passwordHash = ($password == $defaultPass) ? $defaultHash : bcrypt($password);

                $user = User::updateOrCreate(
                    ['email' => $line['email']],
                    [
                        'name' => $line['name'] ?? '',
                        'mobile' => $line['phone'] ?? null,
                        'password' => $passwordHash, 
                    ]
                );
                
                if (isset($line['role'])) {
                    $user->syncRoles(explode(',', $line['role']));
                }
                
                $count++;
                // Optimization 2: Commit in chunks to manage DB transaction log size
                if ($count % 1000 == 0) {
                    DB::commit();
                    DB::beginTransaction();
                }
            });
            
            DB::commit(); // Final commit
                
            if ($request->ajax()) {
                return JsonResponse::success('Users imported successfully!');
            }
            return back()->with('success', 'Users imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getMessage());
            if ($request->ajax() || $dryRun) {
                 return JsonResponse::internalError('Error: ' . $e->getMessage());
            }
            return back()->withErrors('Error: ' . $e->getMessage());
        } finally {
            if (file_exists($tempPath)) unlink($tempPath);
        }
    }
}
