<?php

namespace App\Modules\Main\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Main\Models\Module;
use App\Modules\Main\Models\Permission;
use App\Modules\Main\Models\Role;
use App\Modules\Main\Utilities\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Modules\Settings\Models\CompanySetting;

class RoleController extends Controller
{
    private $viewPath;

    public function __construct()
    {
        $this->viewPath = 'Main::pages.role.';
    }


    /*
    *
    * MODULE
    * -------------------------------------------------------
    */

    public function module(Request $request)
    {  
        $query = Module::query();
        
        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        // Filter by start date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        // Filter by end date
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $result = $query->paginate(10); 
        return view($this->viewPath . 'module', compact(
            'result'
        )); 
    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function createModule()
    { 
        $data = (object)[
            'page'   => 'Module',
            'method' => 'Create',
            'action'  => route('dashboard.role.module'),
            'item'   => [],
        ]; 

        return view($this->viewPath . 'module-form', compact('data'));
    }


    public function storeModule(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permission_modules,name|max:255', 
        ]);

        try {

            Module::create([
                'name' => $request->name,
                'status'  => 1
            ]);

            Log::info('RoleController@storeModule - requested by  ' . ($request->user()->email ?? null) . ' - '. json_encode($request->all())); 

            return JsonResponse::success('Save successful!', $request->all()); 

        } catch(\Exception $e) {
            Log::error('RoleController@storeModule - '. $e->getMessage()); 
            return JsonResponse::internalError('Something went wrong!');
        } 
    }

 
    public function editModule($id)
    { 
        $data = (object)[
            'page'   => 'Module',
            'method' => 'Update',
            'action' => route('dashboard.role.module.update', $id),
            'item'   => Module::find($id)
        ]; 

        return view($this->viewPath . 'module-form', compact('data'));
    }


    public function updateModule(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|max:255|unique:permission_modules,name,'.$id, 
            'status'   => 'required|in:0,1', 
        ]);

        try {

            Module::where('id', $id)
            ->update([
                'name'    => $request->name,
                'status'  => $request->status
            ]);

            Log::info('RoleController@updateModule - requested by  ' . ($request->user()->email ?? null) . ' - '. json_encode($request->all())); 
            
            return JsonResponse::success('Update successful!', $request->all()); 

        } catch(\Exception $e) {
            Log::error('RoleController@updateModule - '. $e->getMessage()); 
            return JsonResponse::internalError('Something went wrong!');
        } 
    }



    /*
    *
    * PERMISSION
    * -------------------------------------------------------
    */

    public function permission(Request $request)
    {
        $query = DB::table('permissions AS p')
            ->selectRaw('p.*, m.name AS module')
            ->leftJoin('permission_modules AS m', 'm.id', 'p.module_id');
        
        // Filter by permission name
        if ($request->filled('name')) {
            $query->where('p.name', 'like', '%' . $request->name . '%');
        }
        
        // Filter by module name
        if ($request->filled('module')) {
            $query->where('m.name', 'like', '%' . $request->module . '%');
        }
        
        // Filter by start date
        if ($request->filled('start_date')) {
            $query->whereDate('p.created_at', '>=', $request->start_date);
        }
        
        // Filter by end date
        if ($request->filled('end_date')) {
            $query->whereDate('p.created_at', '<=', $request->end_date);
        }
        
        $result = $query->orderBy("m.name", "asc")
            ->orderBy("p.name", "asc")
            ->paginate(10);

        return view($this->viewPath . 'permission', compact(
            'result',
        )); 
    }


    public function createPermission()
    { 
        $data = (object)[
            'page'    => 'Permission',
            'method'  => 'Create',
            'action'  => route('dashboard.role.permission'),
            'item'    => [],
            'modules' => Module::where('status', '1')->pluck('name', 'id')
        ]; 

        return view($this->viewPath . 'permission-form', compact('data'));
    }

    public function storePermission(Request $request) 
    {
        $request->validate([
            'name'      => 'required|string|unique:permissions,name|max:255', 
            'module_id' => 'required|exists:permission_modules,id', 
        ]);

        try {

            Permission::create([
                'name'       => $request->name,
                'guard_name' => 'web',
                'module_id'  => $request->module_id
            ]);

            Log::info('RoleController@storePermission - requested by  ' . ($request->user()->email ?? null) . ' - '. json_encode($request->all())); 
            return JsonResponse::success('Save successful!', $request->all());  

        } catch(\Exception $e) {
            Log::error('RoleController@storePermission - '. $e->getMessage());
            return JsonResponse::internalError('Something went wrong!');
        } 
    }


    public function editPermission($id)
    { 
        $data = (object)[
            'page'    => 'Permission',
            'method'  => 'Update',
            'action'  => route('dashboard.role.permission.update', $id),
            'item'    => Permission::find($id),
            'modules' => Module::where('status', '1')->pluck('name', 'id')
        ]; 

        return view($this->viewPath . 'permission-form', compact('data'));
    }


    public function updatePermission(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:permissions,name,'.$id, 
            'module_id' => 'required|exists:permission_modules,id', 
        ]);

        try {

            Permission::where('id', $id)
            ->update([
                'name'       => $request->name,
                'module_id'  => $request->module_id
            ]);

            Log::info('RoleController@updatePermission - requested by  ' . ($request->user()->email ?? null) . ' - '. json_encode($request->all())); 

            return JsonResponse::success('Update successful!', $request->all());   

        } catch(\Exception $e) {
            Log::error('RoleController@updatePermission - '. $e->getMessage()); 
            return JsonResponse::internalError('Something went wrong!');
        } 
    }
    
    public function exportPermission(Request $request) {
        $format = $request->query('format', 'xlsx');
        $result = Permission::with('module')->get();

        if ($format == 'pdf') {
            $company = CompanySetting::first();
            $data = [
                 'result' => $result,
                 'company' => $company,
                 'title' => 'Permission List Report'
            ];
            $pdf = Pdf::loadView('Main::pages.role.permission-pdf', $data);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('permissions.pdf');
        }

        return (new FastExcel($result))->download('permissions.' . $format, function ($perm) {
            return [
                'ID' => $perm->id,
                'Name' => $perm->name,
                'Module' => $perm->module->name ?? 'N/A',
                'Guard' => $perm->guard_name
            ];
        });
    }

    public function samplePermission(Request $request) {
        $format = $request->query('format', 'xlsx');
        $sampleData = collect([
            [
                'Name' => 'user-list',
                'Module' => 'User Management',
                'Guard' => 'web'
            ]
        ]);
        return (new FastExcel($sampleData))->download('sample_permissions.'.$format);
    }

    public function importPermission(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);
        
        $dryRun = $request->has('dry_run');
        
        $file = $request->file('file');
        // Create a temp file with correct extension to ensure FastExcel works
        $tempPath = sys_get_temp_dir() . '/import_p_' . uniqid() . '.' . $file->getClientOriginalExtension();
        copy($file->getRealPath(), $tempPath);

        try {
            if ($dryRun) {
                ini_set('memory_limit', '-1');
                set_time_limit(0);
                
                $previewData = [];
                $search = $request->input('preview_search');
                
                // Cache existing permissions
                $existingPermissions = Permission::pluck('name')->flip()->toArray();

                (new FastExcel)->import($tempPath, function ($originalLine) use (&$previewData, $search, $existingPermissions) {
                    $originalLine = (array) $originalLine; // Ensure array
                    $line = array_change_key_case($originalLine, CASE_LOWER);
                    
                    // Search Filter
                    if ($search) {
                        $rowValues = implode(' ', array_values($originalLine));
                        if (stripos($rowValues, $search) === false) {
                            return; // Skip if doesn't match
                        }
                    }
                    
                    // Normalize keys
                    $name = $line['name'] ?? null;
                    
                    $status = 'New';
                    if ($name && isset($existingPermissions[$name])) {
                        $status = 'Update';
                    }
                    $previewData[] = array_merge($originalLine, ['_status' => $status]);
                    
                    // No Limit
                });
                
                return JsonResponse::success('Preview', ['preview' => $previewData, 'count' => count($previewData)]);
            }

            // Actual Import
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            
            DB::beginTransaction();
            $count = 0;
            
            // Cache modules: Name => ID
            $modulesCache = Module::pluck('id', 'name')->toArray();

            (new FastExcel)->import($tempPath, function ($originalLine) use (&$count, &$modulesCache) {
                 $originalLine = (array) $originalLine; // Ensure array
                 $line = array_change_key_case($originalLine, CASE_LOWER);
                 
                 $name = $line['name'] ?? null;
                 $moduleName = $line['module'] ?? null;

                 if (!$name) return;

                 // Logic: Module Handling with Cache
                 $moduleId = null;
                 if (!empty($moduleName)) {
                     if (isset($modulesCache[$moduleName])) {
                         $moduleId = $modulesCache[$moduleName];
                     } else {
                         // Create new module
                         $newModule = Module::create(['name' => $moduleName, 'status' => 1]);
                         $moduleId = $newModule->id;
                         $modulesCache[$moduleName] = $moduleId;
                     }
                 }
                 
                 Permission::updateOrCreate(
                    ['name' => $name],
                    [
                        'guard_name' => $line['guard'] ?? 'web',
                        'module_id' => $moduleId, 
                    ]
                 );
                 
                 $count++;
                 if ($count % 1000 == 0) {
                     DB::commit();
                     DB::beginTransaction();
                 }
            });
            
            DB::commit();
            
            if ($request->ajax()) {
                return JsonResponse::success('Permissions imported successfully!');
            }
            return back()->with('success', 'Permissions imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            if ($request->ajax() || $dryRun) {
                 return JsonResponse::internalError('Error: ' . $e->getMessage());
            }
            return back()->withErrors('Error: ' . $e->getMessage());
        } finally {
            if (file_exists($tempPath)) unlink($tempPath);
        }
    }


    /*
    *
    * ROLE
    * -------------------------------------------------------
    */

    public function role(Request $request)
    {  
        $query = Role::with('permissions');
        
        // Filter by role name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        // Filter by start date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        // Filter by end date
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $result = $query->paginate(10);

        return view($this->viewPath . 'role', compact(
            'result' 
        )); 
    } 

    public function createRole(Request $request)
    {
        $result = Permission::selectRaw('
                permissions.id,
                permissions.module_id,
                permissions.name,
                m.name AS module,
                CASE WHEN rhp.permission_id THEN "checked" ELSE "" END AS checked
            ')
            ->leftJoin('permission_modules AS m', 'm.id', 'permissions.module_id')
            ->leftJoin('role_has_permissions AS rhp', function($join) use($request) {
                $join->on('rhp.permission_id', '=', 'permissions.id');
            })
            ->groupBy('permissions.id')
            ->get();
    
        $modules = [];
        foreach ($result as $item) 
        {
            if (!auth()->user()->hasRole("Developer")) { 
                // ignore for other user
                if (in_array($item->name, ["module-create", "module-update", "module-list", "permission-list", "permission-create", "permission-update"])) {
                    continue;
                }
            }
            $modules[$item->module][] = (object)[
                'id'        => $item->id,
                'module_id' => $item->module_id,
                'name'      => $item->name,
                'module'    => $item->module,
                'checked'   => $item->checked
            ];
        }

        return view($this->viewPath . 'role-create', compact(
            'modules'
        )); 
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|not_in:Developer|unique:roles,name',
            'permissions' => 'sometimes|array|max:255', 
        ], [
            "name.not_in" => "The 'Developer' word is reserved for the system. Please select a different one."
        ]);

        try {

            $role = Role::create([
                'name'        => $request->name,
                'guard_name'  => 'web'
            ]);

            $role->syncPermissions($request->permissions); 

            Log::info('RoleController@storeRole - requested by  ' . ($request->user()->email ?? null) . ' - '. json_encode($request->all())); 

            return redirect()
                ->route('dashboard.role.has-permission.edit', $role->id)
                ->with('success', 'Save successful!')
                ->withInput(); 

        } catch(\Exception $e) {
            Log::error('RoleController@storeRole - '. $e->getMessage()); 

            return redirect()
                ->back()
                ->withErrors('Something went wrong!')
                ->withInput(); 
        } 
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $result = Permission::selectRaw('
                permissions.id,
                permissions.module_id,
                permissions.name,
                m.name AS module,
                CASE WHEN rhp.permission_id THEN "checked" ELSE "" END AS checked
            ')
            ->leftJoin('permission_modules AS m', 'm.id', 'permissions.module_id')
            ->leftJoin('role_has_permissions AS rhp', function($join) use($id) {
                $join->on('rhp.permission_id', '=', 'permissions.id');
                $join->where('rhp.role_id', '=', $id);
            })
            ->get();
    
        $modules = [];

        foreach ($result as $item) 
        {
            if (!auth()->user()->hasRole(["Super Admin", "Developer"])) { 
                // ignore for other user
                if (in_array($item->name, ["module-create", "module-update", "module-list", "permission-list", "permission-create", "permission-update"])) {
                    continue;
                }
            }

            $modules[$item->module][] = (object)[
                'id'        => $item->id,
                'module_id' => $item->module_id,
                'name'      => $item->name,
                'module'    => $item->module,
                'checked'   => $item->checked
            ];
        }

        return view($this->viewPath . 'role-edit', compact(
            'role',
            'modules'
        )); 
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'id'          => 'required|numeric',
            'name'        => 'required|string|max:255|not_in:Developer|unique:roles,name,'.$request->id,
            'permissions' => 'sometimes|array|max:255' 
        ], [
            "name.not_in" => "The 'Developer' word is reserved for the system. Please select a different one."
        ]);

        try {

            $role = Role::where('id', $request->id)->first();
            $role->name = $request->name;
            $role->save(); 

            $role->syncPermissions($request->permissions); 

            Log::info('RoleController@updateRole - requested by  ' . ($request->user()->email ?? null) . ' - '. json_encode($request->all())); 

            return redirect()
                ->back()
                ->with('success', 'Update successful!')
                ->withInput(); 

        } catch(\Exception $e) {
            Log::error('RoleController@updateRole - '. $e->getMessage()); 

            return redirect()
                ->back()
                ->withErrors('Something went wrong!')
                ->withInput(); 
        } 
    } 

}
