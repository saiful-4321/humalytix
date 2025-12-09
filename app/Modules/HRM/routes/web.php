<?php

use Illuminate\Support\Facades\Route;
use App\Modules\HRM\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| HRM Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->prefix('hrm')->name('hrm.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('HRM::pages.dashboard.index');
    })->name('dashboard');

    // Employee Management
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        
        // Import/Export
        Route::get('/export/sample', [EmployeeController::class, 'sample'])->name('sample');
        Route::post('/export', [EmployeeController::class, 'export'])->name('export');
        Route::post('/import', [EmployeeController::class, 'import'])->name('import');

        // Family Members
        Route::post('/{employee}/family', [\App\Modules\HRM\Http\Controllers\FamilyMemberController::class, 'store'])->name('family.store');
        Route::put('/{employee}/family/{familyMember}', [\App\Modules\HRM\Http\Controllers\FamilyMemberController::class, 'update'])->name('family.update');
        Route::delete('/{employee}/family/{familyMember}', [\App\Modules\HRM\Http\Controllers\FamilyMemberController::class, 'destroy'])->name('family.destroy');

        // Documents
        Route::post('/{employee}/documents', [\App\Modules\HRM\Http\Controllers\EmployeeDocumentController::class, 'store'])->name('documents.store');
        Route::post('/{employee}/documents/{document}/verify', [\App\Modules\HRM\Http\Controllers\EmployeeDocumentController::class, 'verify'])->name('documents.verify');
        Route::get('/{employee}/documents/{document}/download', [\App\Modules\HRM\Http\Controllers\EmployeeDocumentController::class, 'download'])->name('documents.download');
        Route::delete('/{employee}/documents/{document}', [\App\Modules\HRM\Http\Controllers\EmployeeDocumentController::class, 'destroy'])->name('documents.destroy');

        // Employment History
        Route::post('/{employee}/history', [\App\Modules\HRM\Http\Controllers\EmploymentHistoryController::class, 'store'])->name('history.store');
        Route::put('/{employee}/history/{history}', [\App\Modules\HRM\Http\Controllers\EmploymentHistoryController::class, 'update'])->name('history.update');
        Route::delete('/{employee}/history/{history}', [\App\Modules\HRM\Http\Controllers\EmploymentHistoryController::class, 'destroy'])->name('history.destroy');

        // Skills
        Route::post('/{employee}/skills', [\App\Modules\HRM\Http\Controllers\EmployeeSkillController::class, 'store'])->name('skills.store');
        Route::put('/{employee}/skills/{skill}', [\App\Modules\HRM\Http\Controllers\EmployeeSkillController::class, 'update'])->name('skills.update');
        Route::delete('/{employee}/skills/{skill}', [\App\Modules\HRM\Http\Controllers\EmployeeSkillController::class, 'destroy'])->name('skills.destroy');
    });

    // Organization Structure
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\DepartmentController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\DepartmentController::class, 'store'])->name('store');
        Route::get('/org-chart', [\App\Modules\HRM\Http\Controllers\DepartmentController::class, 'orgChart'])->name('org-chart');
        Route::get('/{department}', [\App\Modules\HRM\Http\Controllers\DepartmentController::class, 'show'])->name('show');
        Route::get('/{department}/edit', [\App\Modules\HRM\Http\Controllers\DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{department}', [\App\Modules\HRM\Http\Controllers\DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [\App\Modules\HRM\Http\Controllers\DepartmentController::class, 'destroy'])->name('destroy');
    });
    
    // Branches
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\BranchController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\BranchController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\BranchController::class, 'store'])->name('store');
        Route::get('/{branch}', [\App\Modules\HRM\Http\Controllers\BranchController::class, 'show'])->name('show');
        Route::get('/{branch}/edit', [\App\Modules\HRM\Http\Controllers\BranchController::class, 'edit'])->name('edit');
        Route::put('/{branch}', [\App\Modules\HRM\Http\Controllers\BranchController::class, 'update'])->name('update');
        Route::delete('/{branch}', [\App\Modules\HRM\Http\Controllers\BranchController::class, 'destroy'])->name('destroy');
    });

    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in', [\App\Modules\HRM\Http\Controllers\AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [\App\Modules\HRM\Http\Controllers\AttendanceController::class, 'checkOut'])->name('check-out');
        Route::get('/my-attendance', [\App\Modules\HRM\Http\Controllers\AttendanceController::class, 'myAttendance'])->name('my-attendance');
        Route::get('/report', [\App\Modules\HRM\Http\Controllers\AttendanceController::class, 'report'])->name('report');
    });

    // Leaves
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'store'])->name('store');
        
        // Allocations
        Route::get('/allocations', [\App\Modules\HRM\Http\Controllers\LeaveAllocationController::class, 'index'])->name('allocations.index');
        Route::post('/allocations/generate', [\App\Modules\HRM\Http\Controllers\LeaveAllocationController::class, 'generate'])->name('allocations.generate');
        Route::put('/allocations/{allocation}', [\App\Modules\HRM\Http\Controllers\LeaveAllocationController::class, 'update'])->name('allocations.update');

        Route::get('/{leave}', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'show'])->name('show');
        Route::post('/{leave}/approve', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'approve'])->name('approve');
        Route::post('/{leave}/reject', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'reject'])->name('reject');
        Route::get('/my-leaves', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'myLeaves'])->name('my-leaves');
    });

    // Recruitment
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\JobController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\JobController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\JobController::class, 'store'])->name('store');
        Route::get('/{job}', [\App\Modules\HRM\Http\Controllers\JobController::class, 'show'])->name('show');
        Route::get('/{job}/edit', [\App\Modules\HRM\Http\Controllers\JobController::class, 'edit'])->name('edit');
        Route::put('/{job}', [\App\Modules\HRM\Http\Controllers\JobController::class, 'update'])->name('update');
        Route::delete('/{job}', [\App\Modules\HRM\Http\Controllers\JobController::class, 'destroy'])->name('destroy');
    });

    // Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'store'])->name('store');
        Route::get('/{payroll}', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'show'])->name('show');
        Route::post('/{payroll}/process', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'process'])->name('process');
        Route::post('/bulk-generate', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'bulkGenerate'])->name('bulk-generate');
    });
    
    // Recruitment & ATS
    Route::prefix('candidates')->name('candidates.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\CandidateController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\CandidateController::class, 'store'])->name('store');
        Route::get('/{candidate}', [\App\Modules\HRM\Http\Controllers\CandidateController::class, 'show'])->name('show');
        Route::post('/{candidate}/status', [\App\Modules\HRM\Http\Controllers\CandidateController::class, 'updateStatus'])->name('update-status');
        Route::post('/{candidate}/schedule-interview', [\App\Modules\HRM\Http\Controllers\CandidateController::class, 'scheduleInterview'])->name('schedule-interview');
    });

    // Digital Letters
    Route::prefix('letters')->name('letters.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\LetterController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\LetterController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\LetterController::class, 'store'])->name('store');
        Route::get('/{letter}', [\App\Modules\HRM\Http\Controllers\LetterController::class, 'show'])->name('show');
        Route::get('/{letter}/download', [\App\Modules\HRM\Http\Controllers\LetterController::class, 'download'])->name('download');
    });

    // Asset Management
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'store'])->name('store');
        Route::get('/{asset}/assign', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'assign'])->name('assign');
        Route::post('/{asset}/assign', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'storeAssignment'])->name('store-assignment');
        Route::post('/{asset}/return', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'returnAsset'])->name('return');
    });

    // Resignation & Offboarding
    Route::prefix('resignations')->name('resignations.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\ResignationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\ResignationController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\ResignationController::class, 'store'])->name('store');
        Route::get('/{resignation}', [\App\Modules\HRM\Http\Controllers\ResignationController::class, 'show'])->name('show');
        Route::post('/{resignation}/status', [\App\Modules\HRM\Http\Controllers\ResignationController::class, 'updateStatus'])->name('update-status');
        Route::post('/clearance/{item}', [\App\Modules\HRM\Http\Controllers\ResignationController::class, 'updateClearance'])->name('update-clearance');
        Route::post('/{resignation}/settlement', [\App\Modules\HRM\Http\Controllers\ResignationController::class, 'generateSettlement'])->name('generate-settlement');
    });

    // Shifts & Rosters
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\ShiftController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\ShiftController::class, 'store'])->name('store');
        Route::post('/assign', [\App\Modules\HRM\Http\Controllers\ShiftController::class, 'assignRoster'])->name('assign');
    });

    // Performance Management
    Route::prefix('appraisals')->name('appraisals.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\AppraisalController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\AppraisalController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\AppraisalController::class, 'store'])->name('store');
        Route::get('/{appraisal}', [\App\Modules\HRM\Http\Controllers\AppraisalController::class, 'show'])->name('show');
        Route::get('/{appraisal}/edit', [\App\Modules\HRM\Http\Controllers\AppraisalController::class, 'edit'])->name('edit');
        Route::put('/{appraisal}', [\App\Modules\HRM\Http\Controllers\AppraisalController::class, 'update'])->name('update');
        Route::delete('/{appraisal}', [\App\Modules\HRM\Http\Controllers\AppraisalController::class, 'destroy'])->name('destroy');
    });
    // Route::resource('business-units', BusinessUnitController::class);
    // Route::resource('cost-centers', CostCenterController::class);

    // Recruitment & ATS
    // Route::resource('jobs', JobController::class);
    // Route::resource('candidates', CandidateController::class);
    // Route::resource('interviews', InterviewController::class);
    // Route::resource('offers', OfferLetterController::class);

    // Attendance
    // Route::prefix('attendance')->name('attendance.')->group(function () {
    //     Route::get('/', [AttendanceController::class, 'index'])->name('index');
    //     Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
    //     Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
    //     Route::get('/reports', [AttendanceController::class, 'reports'])->name('reports');
    // });

    // Shifts & Rosters
    // Route::resource('shifts', ShiftController::class);
    // Route::resource('rosters', RosterController::class);

    // Skills
    // Route::resource('skills', SkillController::class);

    // Documents
    // Route::resource('document-types', DocumentTypeController::class);
    // Route::prefix('documents')->name('documents.')->group(function () {
    //     Route::get('/', [DocumentController::class, 'index'])->name('index');
    //     Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
    //     Route::post('/{document}/verify', [DocumentController::class, 'verify'])->name('verify');
    // });

    // Onboarding
    // Route::prefix('onboarding')->name('onboarding.')->group(function () {
    //     Route::get('/', [OnboardingController::class, 'index'])->name('index');
    //     Route::get('/{employee}', [OnboardingController::class, 'show'])->name('show');
    //     Route::post('/{employee}/tasks', [OnboardingController::class, 'addTask'])->name('add-task');
    // });

    // Offboarding
    // Route::resource('resignations', ResignationController::class);
    // Route::prefix('resignations/{resignation}')->name('resignations.')->group(function () {
    //     Route::post('/approve', [ResignationController::class, 'approve'])->name('approve');
    //     Route::post('/reject', [ResignationController::class, 'reject'])->name('reject');
    //     Route::get('/clearance', [ResignationController::class, 'clearance'])->name('clearance');
    //     Route::get('/settlement', [ResignationController::class, 'settlement'])->name('settlement');
    // });

    // Time Tracking
    // Route::resource('projects', ProjectController::class);
    // Route::resource('time-entries', TimeEntryController::class);
    // Route::resource('timesheets', TimesheetController::class);
    // Route::post('/timesheets/{timesheet}/submit', [TimesheetController::class, 'submit'])->name('timesheets.submit');
    // Route::post('/timesheets/{timesheet}/approve', [TimesheetController::class, 'approve'])->name('timesheets.approve');

    // Assets
    // Route::resource('assets', AssetController::class);
    // Route::post('/assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    // Route::post('/assets/{asset}/return', [AssetController::class, 'return'])->name('assets.return');

    // Letters
    // Route::resource('letter-templates', LetterTemplateController::class);
    // Route::prefix('letters')->name('letters.')->group(function () {
    //     Route::get('/', [LetterController::class, 'index'])->name('index');
    //     Route::post('/generate', [LetterController::class, 'generate'])->name('generate');
    //     Route::get('/{letter}/download', [LetterController::class, 'download'])->name('download');
    // });

    // Reports
    // Route::prefix('reports')->name('reports.')->group(function () {
    //     Route::get('/employees', [ReportController::class, 'employees'])->name('employees');
    //     Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
    //     Route::get('/recruitment', [ReportController::class, 'recruitment'])->name('recruitment');
    //     Route::get('/turnover', [ReportController::class, 'turnover'])->name('turnover');
    // });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('approval-chains', \App\Modules\HRM\Http\Controllers\ApprovalChainController::class);
        Route::resource('leave-types', \App\Modules\HRM\Http\Controllers\LeaveTypeController::class);
    });
});
