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

    // Employee Self-Service (ESS)
    Route::prefix('ess')->name('ess.')->group(function () {
        Route::get('/dashboard', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'profile'])->name('profile');
        Route::put('/profile', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'updateProfile'])->name('profile.update');
        Route::get('/attendance', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'attendance'])->name('attendance');
        Route::post('/attendance/regularize', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'regularizeAttendance'])->name('attendance.regularize');
        Route::get('/payslips', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'payslips'])->name('payslips');
        Route::get('/assets', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'assets'])->name('assets');
        Route::get('/salary-certificate', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'salaryCertificate'])->name('salary-certificate');
        Route::post('/salary-certificate', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'requestSalaryCertificate'])->name('salary-certificate.request');
        Route::get('/holidays', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'holidays'])->name('holidays');
        Route::get('/expenses', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'expenses'])->name('expenses');
        Route::post('/expenses', [\App\Modules\HRM\Http\Controllers\EmployeeServiceController::class, 'storeExpense'])->name('expenses.store');
    });

    // Manager Self-Service (MSS)
    Route::prefix('mss')->name('mss.')->group(function () {
        Route::get('/dashboard', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'dashboard'])->name('dashboard');
        Route::get('/team', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'team'])->name('team');
        Route::get('/approvals', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'approvals'])->name('approvals');
        
        // Leave Approvals
        Route::post('/leaves/{leave}/approve', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'approveLeave'])->name('leaves.approve');
        Route::post('/leaves/{leave}/reject', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'rejectLeave'])->name('leaves.reject');
        
        // Regularization
        Route::post('/regularization/{regularization}/approve', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'approveRegularization'])->name('regularization.approve');
        Route::post('/regularization/{regularization}/reject', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'rejectRegularization'])->name('regularization.reject');
        
        // Expenses
        Route::post('/expenses/{expense}/approve', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'approveExpense'])->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'rejectExpense'])->name('expenses.reject');

        // Roster
        Route::get('/roster', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'roster'])->name('roster');
        Route::post('/roster', [\App\Modules\HRM\Http\Controllers\ManagerServiceController::class, 'storeRoster'])->name('roster.store');
    });

    // Employee Management
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::get('/{employee}/assign-user', [EmployeeController::class, 'assignUser'])->name('assign-user');
        Route::post('/{employee}/assign-user', [EmployeeController::class, 'storeUserAssignment'])->name('store-user-assignment');
        
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
        Route::get('/{employee}/documents/download-all', [EmployeeController::class, 'downloadDocuments'])->name('documents.download-all');
        Route::delete('/{employee}/documents/{document}', [\App\Modules\HRM\Http\Controllers\EmployeeDocumentController::class, 'destroy'])->name('documents.destroy');

        // Employment History
        Route::post('/{employee}/history', [\App\Modules\HRM\Http\Controllers\EmploymentHistoryController::class, 'store'])->name('history.store');
        Route::put('/{employee}/history/{history}', [\App\Modules\HRM\Http\Controllers\EmploymentHistoryController::class, 'update'])->name('history.update');
        Route::delete('/{employee}/history/{history}', [\App\Modules\HRM\Http\Controllers\EmploymentHistoryController::class, 'destroy'])->name('history.destroy');

        // Skills
        Route::post('/{employee}/skills', [\App\Modules\HRM\Http\Controllers\EmployeeSkillController::class, 'store'])->name('skills.store');
        Route::put('/{employee}/skills/{skill}', [\App\Modules\HRM\Http\Controllers\EmployeeSkillController::class, 'update'])->name('skills.update');
        Route::delete('/{employee}/skills/{skill}', [\App\Modules\HRM\Http\Controllers\EmployeeSkillController::class, 'destroy'])->name('skills.destroy');
        
        // Salary Management
        Route::get('/{employee}/salary', [\App\Modules\HRM\Http\Controllers\EmployeeSalaryController::class, 'edit'])->name('salary.edit');
        Route::put('/{employee}/salary', [\App\Modules\HRM\Http\Controllers\EmployeeSalaryController::class, 'update'])->name('salary.update');
        Route::post('/salary/calculate', [\App\Modules\HRM\Http\Controllers\EmployeeSalaryController::class, 'calculateBreakdown'])->name('salary.calculate');
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

    // Business Units
    Route::resource('business-units', \App\Modules\HRM\Http\Controllers\BusinessUnitController::class);
    
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

    // Document Types
    Route::prefix('document-types')->name('document-types.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\DocumentTypeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\DocumentTypeController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\DocumentTypeController::class, 'store'])->name('store');
        Route::get('/{documentType}/edit', [\App\Modules\HRM\Http\Controllers\DocumentTypeController::class, 'edit'])->name('edit');
        Route::put('/{documentType}', [\App\Modules\HRM\Http\Controllers\DocumentTypeController::class, 'update'])->name('update');
        Route::delete('/{documentType}', [\App\Modules\HRM\Http\Controllers\DocumentTypeController::class, 'destroy'])->name('destroy');
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
        
        Route::get('/dashboard', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'dashboard'])->name('dashboard');
        Route::get('/my-leaves', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'myLeaves'])->name('my-leaves');
        
        Route::get('/{leave}', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'show'])->name('show');
        Route::post('/{leave}/approve', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'approve'])->name('approve');
        Route::post('/{leave}/reject', [\App\Modules\HRM\Http\Controllers\LeaveController::class, 'reject'])->name('reject');
    });

    // Expense Management
    Route::group(['prefix' => 'expenses', 'as' => 'expenses.'], function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\ExpenseController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\ExpenseController::class, 'store'])->name('store');
        Route::put('/{expense}/approve', [\App\Modules\HRM\Http\Controllers\ExpenseController::class, 'approve'])->name('approve');
        Route::put('/{expense}/reject', [\App\Modules\HRM\Http\Controllers\ExpenseController::class, 'reject'])->name('reject');
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
        Route::get('/{payroll}/pdf', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'downloadPDF'])->name('download-pdf');
        Route::post('/{payroll}/process', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'process'])->name('process');
        Route::delete('/{payroll}', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-generate', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'bulkGenerate'])->name('bulk-generate');
    });
    
    // Loans & Advances
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\LoanController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\LoanController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\LoanController::class, 'store'])->name('store');
        Route::get('/{loan}', [\App\Modules\HRM\Http\Controllers\LoanController::class, 'show'])->name('show');
        Route::post('/{loan}/approve', [\App\Modules\HRM\Http\Controllers\LoanController::class, 'approve'])->name('approve');
        Route::post('/{loan}/reject', [\App\Modules\HRM\Http\Controllers\LoanController::class, 'reject'])->name('reject');
        Route::delete('/{loan}', [\App\Modules\HRM\Http\Controllers\LoanController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('advances')->name('advances.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\AdvanceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\AdvanceController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\AdvanceController::class, 'store'])->name('store');
        Route::post('/{advance}/approve', [\App\Modules\HRM\Http\Controllers\AdvanceController::class, 'approve'])->name('approve');
        Route::post('/{advance}/reject', [\App\Modules\HRM\Http\Controllers\AdvanceController::class, 'reject'])->name('reject');
        Route::delete('/{advance}', [\App\Modules\HRM\Http\Controllers\AdvanceController::class, 'destroy'])->name('destroy');
    });
    
    // Overtime
    Route::prefix('overtime')->name('overtime.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\OvertimeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\OvertimeController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\OvertimeController::class, 'store'])->name('store');
        Route::post('/{overtime}/approve', [\App\Modules\HRM\Http\Controllers\OvertimeController::class, 'approve'])->name('approve');
        Route::post('/{overtime}/reject', [\App\Modules\HRM\Http\Controllers\OvertimeController::class, 'reject'])->name('reject');
        Route::delete('/{overtime}', [\App\Modules\HRM\Http\Controllers\OvertimeController::class, 'destroy'])->name('destroy');
    });
    
    // Bonuses
    Route::prefix('bonuses')->name('bonuses.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\BonusController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\BonusController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\BonusController::class, 'store'])->name('store');
        Route::post('/{bonus}/approve', [\App\Modules\HRM\Http\Controllers\BonusController::class, 'approve'])->name('approve');
        Route::post('/{bonus}/reject', [\App\Modules\HRM\Http\Controllers\BonusController::class, 'reject'])->name('reject');
        Route::delete('/{bonus}', [\App\Modules\HRM\Http\Controllers\BonusController::class, 'destroy'])->name('destroy');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/salary-register', [\App\Modules\HRM\Http\Controllers\PayrollReportController::class, 'salaryRegister'])->name('salary-register');
        Route::get('/export-excel', [\App\Modules\HRM\Http\Controllers\PayrollReportController::class, 'exportExcel'])->name('export-excel');
    });
    
    // Bank Transfers
    Route::prefix('bank-transfers')->name('bank-transfers.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\BankTransferController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Modules\HRM\Http\Controllers\BankTransferController::class, 'generate'])->name('generate');
    });
    
    // Email Payslips
    Route::post('/payroll/{payroll}/email', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'emailPayslip'])->name('payroll.email');
    Route::post('/payroll/bulk-email', [\App\Modules\HRM\Http\Controllers\PayrollController::class, 'bulkEmailPayslips'])->name('payroll.bulk-email');
    
    // Gratuity
    Route::prefix('gratuity')->name('gratuity.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\GratuityController::class, 'index'])->name('index');
        Route::get('/calculator', [\App\Modules\HRM\Http\Controllers\GratuityController::class, 'calculator'])->name('calculator');
        Route::post('/calculate', [\App\Modules\HRM\Http\Controllers\GratuityController::class, 'calculate'])->name('calculate');
        Route::post('/config', [\App\Modules\HRM\Http\Controllers\GratuityController::class, 'storeConfig'])->name('config.store');
        Route::post('/{gratuity}/approve', [\App\Modules\HRM\Http\Controllers\GratuityController::class, 'approve'])->name('approve');
        Route::post('/{gratuity}/pay', [\App\Modules\HRM\Http\Controllers\GratuityController::class, 'pay'])->name('pay');
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
    // Asset Management
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'store'])->name('store');
        Route::get('/{asset}/edit', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'edit'])->name('edit');
        Route::put('/{asset}', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'update'])->name('update');
        Route::delete('/{asset}', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'destroy'])->name('destroy');
        Route::get('/my-assets', [\App\Modules\HRM\Http\Controllers\AssetController::class, 'myAssets'])->name('my-assets');
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
        Route::put('/{shift}', [\App\Modules\HRM\Http\Controllers\ShiftController::class, 'update'])->name('update');
        Route::delete('/{shift}', [\App\Modules\HRM\Http\Controllers\ShiftController::class, 'destroy'])->name('destroy');
        Route::post('/assign', [\App\Modules\HRM\Http\Controllers\ShiftController::class, 'assignRoster'])->name('assign'); // We kept this for API usage if needed
    });
    
    Route::resource('rosters', \App\Modules\HRM\Http\Controllers\RosterController::class)->only(['index', 'store']);

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

    // Performance Management System (PMS)
    // KPIs/KRAs
    Route::prefix('kpis')->name('kpis.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\KpiController::class, 'index'])->name('index');
        Route::get('/{kpi}', [\App\Modules\HRM\Http\Controllers\KpiController::class, 'show'])->name('show');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\KpiController::class, 'store'])->name('store');
        Route::put('/{kpi}', [\App\Modules\HRM\Http\Controllers\KpiController::class, 'update'])->name('update');
        Route::delete('/{kpi}', [\App\Modules\HRM\Http\Controllers\KpiController::class, 'destroy'])->name('destroy');
    });

    // OKRs
    Route::prefix('okrs')->name('okrs.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\OkrController::class, 'index'])->name('index');
        Route::get('/{okr}', [\App\Modules\HRM\Http\Controllers\OkrController::class, 'show'])->name('show');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\OkrController::class, 'store'])->name('store');
        Route::put('/{okr}', [\App\Modules\HRM\Http\Controllers\OkrController::class, 'update'])->name('update');
        Route::post('/{okr}/progress', [\App\Modules\HRM\Http\Controllers\OkrController::class, 'updateProgress'])->name('update-progress');
        Route::delete('/{okr}', [\App\Modules\HRM\Http\Controllers\OkrController::class, 'destroy'])->name('destroy');
    });

    // Performance Goals
    Route::prefix('performance-goals')->name('performance-goals.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\PerformanceGoalController::class, 'index'])->name('index');
        Route::get('/my-goals', [\App\Modules\HRM\Http\Controllers\PerformanceGoalController::class, 'myGoals'])->name('my-goals');
        Route::get('/{performanceGoal}', [\App\Modules\HRM\Http\Controllers\PerformanceGoalController::class, 'show'])->name('show');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\PerformanceGoalController::class, 'store'])->name('store');
        Route::put('/{performanceGoal}', [\App\Modules\HRM\Http\Controllers\PerformanceGoalController::class, 'update'])->name('update');
        Route::put('/{performanceGoal}/progress', [\App\Modules\HRM\Http\Controllers\PerformanceGoalController::class, 'updateProgress'])->name('update-progress');
        Route::delete('/{performanceGoal}', [\App\Modules\HRM\Http\Controllers\PerformanceGoalController::class, 'destroy'])->name('destroy');
    });

    // 360° Appraisals
    Route::prefix('appraisals-360')->name('appraisals-360.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\Appraisal360Controller::class, 'index'])->name('index');
        Route::get('/my-appraisals', [\App\Modules\HRM\Http\Controllers\Appraisal360Controller::class, 'myAppraisals'])->name('my-appraisals');
        Route::get('/{appraisal360}', [\App\Modules\HRM\Http\Controllers\Appraisal360Controller::class, 'show'])->name('show');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\Appraisal360Controller::class, 'store'])->name('store');
        Route::put('/{appraisal360}', [\App\Modules\HRM\Http\Controllers\Appraisal360Controller::class, 'update'])->name('update');
        Route::post('/{appraisal360}/reviewers', [\App\Modules\HRM\Http\Controllers\Appraisal360Controller::class, 'addReviewers'])->name('add-reviewers');
        Route::post('/reviewers/{reviewer}/submit', [\App\Modules\HRM\Http\Controllers\Appraisal360Controller::class, 'submitReview'])->name('submit-review');
        Route::delete('/{appraisal360}', [\App\Modules\HRM\Http\Controllers\Appraisal360Controller::class, 'destroy'])->name('destroy');
    });

    // Competencies
    Route::prefix('competencies')->name('competencies.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\CompetencyController::class, 'index'])->name('index');
        Route::get('/{competency}', [\App\Modules\HRM\Http\Controllers\CompetencyController::class, 'show'])->name('show');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\CompetencyController::class, 'store'])->name('store');
        Route::put('/{competency}', [\App\Modules\HRM\Http\Controllers\CompetencyController::class, 'update'])->name('update');
        Route::delete('/{competency}', [\App\Modules\HRM\Http\Controllers\CompetencyController::class, 'destroy'])->name('destroy');
    });

    // Performance Improvement Plans (PIPs)
    Route::prefix('pips')->name('pips.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\PipController::class, 'index'])->name('index');
        Route::get('/my-pips', [\App\Modules\HRM\Http\Controllers\PipController::class, 'myPips'])->name('my-pips');
        Route::get('/{pip}', [\App\Modules\HRM\Http\Controllers\PipController::class, 'show'])->name('show');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\PipController::class, 'store'])->name('store');
        Route::put('/{pip}', [\App\Modules\HRM\Http\Controllers\PipController::class, 'update'])->name('update');
        Route::post('/{pip}/action-items', [\App\Modules\HRM\Http\Controllers\PipController::class, 'addActionItem'])->name('add-action-item');
        Route::put('/action-items/{actionItem}', [\App\Modules\HRM\Http\Controllers\PipController::class, 'updateActionItem'])->name('update-action-item');
        Route::post('/{pip}/reviews', [\App\Modules\HRM\Http\Controllers\PipController::class, 'addReview'])->name('add-review');
        Route::post('/{pip}/status', [\App\Modules\HRM\Http\Controllers\PipController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{pip}', [\App\Modules\HRM\Http\Controllers\PipController::class, 'destroy'])->name('destroy');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Modules\HRM\Http\Controllers\SettingsController::class, 'index'])->name('index');
        Route::get('/general', [\App\Modules\HRM\Http\Controllers\SettingsController::class, 'general'])->name('general');
        Route::post('/general', [\App\Modules\HRM\Http\Controllers\SettingsController::class, 'storeGeneral'])->name('general.store');
        
        Route::resource('approval-chains', \App\Modules\HRM\Http\Controllers\ApprovalChainController::class);
        Route::resource('leave-types', \App\Modules\HRM\Http\Controllers\LeaveTypeController::class);
        Route::resource('letter-templates', \App\Modules\HRM\Http\Controllers\LetterTemplateController::class);
        Route::resource('salary-components', \App\Modules\HRM\Http\Controllers\SalaryComponentController::class);
        Route::resource('salary-structures', \App\Modules\HRM\Http\Controllers\SalaryStructureController::class);
        Route::resource('tax-slabs', \App\Modules\HRM\Http\Controllers\TaxSlabController::class);
        Route::resource('holidays', \App\Modules\HRM\Http\Controllers\HolidayController::class);

        // Expense Settings
        Route::group(['prefix' => 'expenses', 'as' => 'expenses.'], function () {
            Route::get('/', [\App\Modules\HRM\Http\Controllers\ExpenseCategoryController::class, 'index'])->name('index');
            Route::post('/', [\App\Modules\HRM\Http\Controllers\ExpenseCategoryController::class, 'store'])->name('store');
            Route::put('/{expenseCategory}', [\App\Modules\HRM\Http\Controllers\ExpenseCategoryController::class, 'update'])->name('update');
            Route::delete('/{expenseCategory}', [\App\Modules\HRM\Http\Controllers\ExpenseCategoryController::class, 'destroy'])->name('destroy');
        });

        // Asset Settings
        Route::group(['prefix' => 'assets', 'as' => 'assets.'], function () {
            Route::get('/', [\App\Modules\HRM\Http\Controllers\AssetCategoryController::class, 'index'])->name('index');
            Route::post('/', [\App\Modules\HRM\Http\Controllers\AssetCategoryController::class, 'store'])->name('store');
            Route::put('/{assetCategory}', [\App\Modules\HRM\Http\Controllers\AssetCategoryController::class, 'update'])->name('update');
            Route::delete('/{assetCategory}', [\App\Modules\HRM\Http\Controllers\AssetCategoryController::class, 'destroy'])->name('destroy');
            });
    });

    // Training & Development
    Route::prefix('trainings')->name('trainings.')->group(function () {
        Route::get('/dashboard', [\App\Modules\HRM\Http\Controllers\TrainingDashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [\App\Modules\HRM\Http\Controllers\TrainingController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\HRM\Http\Controllers\TrainingController::class, 'store'])->name('store');
        Route::put('/{training}', [\App\Modules\HRM\Http\Controllers\TrainingController::class, 'update'])->name('update');
        Route::post('/sessions', [\App\Modules\HRM\Http\Controllers\TrainingController::class, 'storeSession'])->name('sessions.store');
        Route::get('/my-trainings', [\App\Modules\HRM\Http\Controllers\TrainingController::class, 'myTrainings'])->name('my-trainings');
        
        // Certificate Actions
        Route::get('/certificate/{participant}/download', [\App\Modules\HRM\Http\Controllers\CertificateController::class, 'download'])->name('certificate.download');
        Route::get('/certificate/{participant}/preview', [\App\Modules\HRM\Http\Controllers\CertificateController::class, 'preview'])->name('certificate.preview');
    });

    Route::get('certifications/{certification}/download', [\App\Modules\HRM\Http\Controllers\CertificateController::class, 'downloadExternal'])->name('certifications.download');
    Route::get('certifications/{certification}/preview', [\App\Modules\HRM\Http\Controllers\CertificateController::class, 'previewExternal'])->name('certifications.preview');
    Route::resource('certifications', \App\Modules\HRM\Http\Controllers\CertificationController::class)->only(['index', 'store', 'destroy']);
    
    // Compliance & Documents
    Route::resource('policies', \App\Modules\HRM\Http\Controllers\PolicyController::class)->only(['index', 'store', 'destroy']);
    
    Route::get('contracts/my-contracts', [\App\Modules\HRM\Http\Controllers\ContractController::class, 'myContracts'])->name('contracts.my');
    Route::post('contracts/{contract}/sign', [\App\Modules\HRM\Http\Controllers\ContractController::class, 'sign'])->name('contracts.sign');
    Route::resource('contracts', \App\Modules\HRM\Http\Controllers\ContractController::class)->only(['index', 'store', 'destroy']);
    
    Route::get('documents/expiry', [\App\Modules\HRM\Http\Controllers\EmployeeDocumentController::class, 'expiryReport'])->name('documents.expiry');

    Route::get('skills/matrix', [\App\Modules\HRM\Http\Controllers\SkillController::class, 'matrix'])->name('skills.matrix');
    Route::resource('skills', \App\Modules\HRM\Http\Controllers\SkillController::class)->only(['index', 'store', 'update', 'destroy']);

    // Workflows
    Route::resource('workflows', \App\Modules\HRM\Http\Controllers\WorkflowController::class);

    // Automation Settings
    Route::get('settings/automation', [\App\Modules\HRM\Http\Controllers\AutomationSettingController::class, 'index'])->name('settings.automation');
    Route::put('settings/automation', [\App\Modules\HRM\Http\Controllers\AutomationSettingController::class, 'update'])->name('settings.automation.update');
    Route::post('settings/automation/run-accrual', [\App\Modules\HRM\Http\Controllers\AutomationSettingController::class, 'runLeaveAccrual'])->name('settings.automation.run-accrual');
    Route::post('settings/automation/run-document-check', [\App\Modules\HRM\Http\Controllers\AutomationSettingController::class, 'runDocumentCheck'])->name('settings.automation.run-document-check');
    Route::post('settings/automation/run-escalation', [\App\Modules\HRM\Http\Controllers\AutomationSettingController::class, 'runApprovalEscalation'])->name('settings.automation.run-escalation');

    // Notifications
    Route::get('notifications', [\App\Modules\HRM\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/create', [\App\Modules\HRM\Http\Controllers\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('notifications', [\App\Modules\HRM\Http\Controllers\NotificationController::class, 'store'])->name('notifications.store');
    Route::get('notifications/{id}', [\App\Modules\HRM\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/{id}/read', [\App\Modules\HRM\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [\App\Modules\HRM\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('notifications/{id}/toggle-read', [\App\Modules\HRM\Http\Controllers\NotificationController::class, 'toggleRead'])->name('notifications.toggle-read');

    // Analytics & Reports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        // HR Dashboard
        Route::get('/hr', [\App\Modules\HRM\Http\Controllers\HRDashboardController::class, 'index'])->name('hr');
        Route::get('/hr/export-pdf', [\App\Modules\HRM\Http\Controllers\HRDashboardController::class, 'exportPDF'])->name('hr.export-pdf');
        Route::get('/hr/export-excel', [\App\Modules\HRM\Http\Controllers\HRDashboardController::class, 'exportExcel'])->name('hr.export-excel');
    });
});
