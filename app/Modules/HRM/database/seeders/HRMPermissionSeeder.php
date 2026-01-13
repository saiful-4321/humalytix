<?php

namespace App\Modules\HRM\database\seeders;

use Illuminate\Database\Seeder;
use App\Modules\Main\Models\Module;
use App\Modules\Main\Models\Permission;
use Spatie\Permission\Models\Role;

class HRMPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Feature-based Modules (One module per feature)
        $modules = [
            // Dashboards
            'HRM Dashboard' => ['status' => 1],
            
            // Core HR Features
            'Employees' => ['status' => 1],
            'Departments' => ['status' => 1],
            'Branches' => ['status' => 1],
            'Business Units' => ['status' => 1],
            'Document Types' => ['status' => 1],
            
            // Time & Attendance Features
            'Attendance' => ['status' => 1],
            'Shifts' => ['status' => 1],
            'Rosters' => ['status' => 1],
            'Holidays' => ['status' => 1],
            
            // Leave Management Features
            'Leaves' => ['status' => 1],
            'Leave Types' => ['status' => 1],
            'Leave Policies' => ['status' => 1],
            'Leave Allocations' => ['status' => 1],
            'Leave Approval Chains' => ['status' => 1],
            
            // Payroll Features
            'Payroll' => ['status' => 1],
            'Bank Transfers' => ['status' => 1],
            'Bonuses' => ['status' => 1],
            'Overtime' => ['status' => 1],
            'Loans' => ['status' => 1],
            'Gratuity' => ['status' => 1],
            'Salary Components' => ['status' => 1],
            
            // Expenses & Assets
            'Expenses' => ['status' => 1],
            'Expense Categories' => ['status' => 1],
            'Assets' => ['status' => 1],
            'Asset Categories' => ['status' => 1],
            
            // Performance Features
            'KPIs' => ['status' => 1],
            'OKRs' => ['status' => 1],
            'Goals' => ['status' => 1],
            'Appraisals' => ['status' => 1],
            'Competencies' => ['status' => 1],
            'PIPs' => ['status' => 1],
            
            // Training Features
            'Trainings' => ['status' => 1],
            'Training Sessions' => ['status' => 1],
            'Skills' => ['status' => 1],
            'Certifications' => ['status' => 1],
            
            // Recruitment Features
            'Jobs' => ['status' => 1],
            'Candidates' => ['status' => 1],
            'Interviews' => ['status' => 1],
            'Offers' => ['status' => 1],
            'Letters' => ['status' => 1],
            'Letter Templates' => ['status' => 1],
            'Onboarding' => ['status' => 1],
            
            // Compliance Features
            'Policies' => ['status' => 1],
            'Contracts' => ['status' => 1],
            'Documents' => ['status' => 1],
            'Resignations' => ['status' => 1],
            'Clearances' => ['status' => 1],
            'Settlements' => ['status' => 1],
            
            // Time Tracking
            'Projects' => ['status' => 1],
            'Time Entries' => ['status' => 1],
            'Timesheets' => ['status' => 1],
            
            // Self Service
            'Employee Self Service (ESS)' => ['status' => 1],
            'Manager Self Service (MSS)' => ['status' => 1],
            
            // Workflows & Automation
            'Workflows' => ['status' => 1],
            'Automation' => ['status' => 1],
            'Notifications' => ['status' => 1],
            
            // Reports & Analytics
            'HRM Reports' => ['status' => 1],
            'HRM Analytics' => ['status' => 1],
            
            // Settings
            'HRM Settings' => ['status' => 1],
        ];

        $createdModules = [];
        foreach ($modules as $moduleName => $moduleData) {
            $createdModules[$moduleName] = Module::firstOrCreate(
                ['name' => $moduleName],
                $moduleData
            );
        }

        // Define ALL permissions by feature (Complete CRUD + Special Actions)
        $permissionsByModule = [
            // ==================== DASHBOARDS ====================
            'HRM Dashboard' => [
                ['name' => 'hrm.dashboard', 'display_name' => 'View HRM Dashboard'],
                ['name' => 'hrm.trainings.dashboard', 'display_name' => 'View Training Dashboard'],
                ['name' => 'hrm.leaves.dashboard', 'display_name' => 'View Leave Dashboard'],
            ],
            
            // ==================== EMPLOYEES ====================
            'Employees' => [
                ['name' => 'hrm.employees.view', 'display_name' => 'View Employees'],
                ['name' => 'hrm.employees.create', 'display_name' => 'Create Employee'],
                ['name' => 'hrm.employees.edit', 'display_name' => 'Edit Employee'],
                ['name' => 'hrm.employees.delete', 'display_name' => 'Delete Employee'],
                ['name' => 'hrm.employees.export', 'display_name' => 'Export Employees'],
                ['name' => 'hrm.employees.import', 'display_name' => 'Import Employees'],
                ['name' => 'hrm.employees.print', 'display_name' => 'Print Employee Data'],
                ['name' => 'hrm.employees.assign-user', 'display_name' => 'Assign User Account'],
                ['name' => 'hrm.employees.view-salary', 'display_name' => 'View Salary Information'],
                ['name' => 'hrm.employees.edit-salary', 'display_name' => 'Edit Salary Information'],
                ['name' => 'hrm.employees.promote', 'display_name' => 'Promote Employee'],
                ['name' => 'hrm.employees.transfer', 'display_name' => 'Transfer Employee'],
                ['name' => 'hrm.employees.terminate', 'display_name' => 'Terminate Employee'],
            ],
            
            // ==================== DEPARTMENTS ====================
            'Departments' => [
                ['name' => 'hrm.departments.view', 'display_name' => 'View Departments'],
                ['name' => 'hrm.departments.create', 'display_name' => 'Create Department'],
                ['name' => 'hrm.departments.edit', 'display_name' => 'Edit Department'],
                ['name' => 'hrm.departments.delete', 'display_name' => 'Delete Department'],
            ],
            
            // ==================== BRANCHES ====================
            'Branches' => [
                ['name' => 'hrm.branches.view', 'display_name' => 'View Branches'],
                ['name' => 'hrm.branches.create', 'display_name' => 'Create Branch'],
                ['name' => 'hrm.branches.edit', 'display_name' => 'Edit Branch'],
                ['name' => 'hrm.branches.delete', 'display_name' => 'Delete Branch'],
            ],
            
            // ==================== BUSINESS UNITS ====================
            'Business Units' => [
                ['name' => 'hrm.business-units.view', 'display_name' => 'View Business Units'],
                ['name' => 'hrm.business-units.create', 'display_name' => 'Create Business Unit'],
                ['name' => 'hrm.business-units.edit', 'display_name' => 'Edit Business Unit'],
                ['name' => 'hrm.business-units.delete', 'display_name' => 'Delete Business Unit'],
            ],
            
            // ==================== DOCUMENT TYPES ====================
            'Document Types' => [
                ['name' => 'hrm.document-types.view', 'display_name' => 'View Document Types'],
                ['name' => 'hrm.document-types.create', 'display_name' => 'Create Document Type'],
                ['name' => 'hrm.document-types.edit', 'display_name' => 'Edit Document Type'],
                ['name' => 'hrm.document-types.delete', 'display_name' => 'Delete Document Type'],
            ],
            
            // ==================== ATTENDANCE ====================
            'Attendance' => [
                ['name' => 'hrm.attendance.view', 'display_name' => 'View Attendance'],
                ['name' => 'hrm.attendance.create', 'display_name' => 'Create Attendance'],
                ['name' => 'hrm.attendance.edit', 'display_name' => 'Edit Attendance'],
                ['name' => 'hrm.attendance.delete', 'display_name' => 'Delete Attendance'],
                ['name' => 'hrm.attendance.approve', 'display_name' => 'Approve Attendance'],
                ['name' => 'hrm.attendance.export', 'display_name' => 'Export Attendance'],
                ['name' => 'hrm.attendance.checkin', 'display_name' => 'Check-in/out (Self)'],
            ],
            
            // ==================== SHIFTS ====================
            'Shifts' => [
                ['name' => 'hrm.shifts.view', 'display_name' => 'View Shifts'],
                ['name' => 'hrm.shifts.create', 'display_name' => 'Create Shift'],
                ['name' => 'hrm.shifts.edit', 'display_name' => 'Edit Shift'],
                ['name' => 'hrm.shifts.delete', 'display_name' => 'Delete Shift'],
            ],
            
            // ==================== ROSTERS ====================
            'Rosters' => [
                ['name' => 'hrm.rosters.view', 'display_name' => 'View Rosters'],
                ['name' => 'hrm.rosters.create', 'display_name' => 'Create Roster'],
                ['name' => 'hrm.rosters.edit', 'display_name' => 'Edit Roster'],
                ['name' => 'hrm.rosters.delete', 'display_name' => 'Delete Roster'],
                ['name' => 'hrm.rosters.publish', 'display_name' => 'Publish Roster'],
            ],
            
            // ==================== HOLIDAYS ====================
            'Holidays' => [
                ['name' => 'hrm.holidays.view', 'display_name' => 'View Holidays'],
                ['name' => 'hrm.holidays.create', 'display_name' => 'Create Holiday'],
                ['name' => 'hrm.holidays.edit', 'display_name' => 'Edit Holiday'],
                ['name' => 'hrm.holidays.delete', 'display_name' => 'Delete Holiday'],
            ],
            
            // ==================== LEAVES ====================
            'Leaves' => [
                ['name' => 'hrm.leaves.view', 'display_name' => 'View Leaves'],
                ['name' => 'hrm.leaves.create', 'display_name' => 'Apply Leave'],
                ['name' => 'hrm.leaves.edit', 'display_name' => 'Edit Leave'],
                ['name' => 'hrm.leaves.delete', 'display_name' => 'Delete Leave'],
                ['name' => 'hrm.leaves.approve', 'display_name' => 'Approve Leave'],
                ['name' => 'hrm.leaves.reject', 'display_name' => 'Reject Leave'],
                ['name' => 'hrm.leaves.cancel', 'display_name' => 'Cancel Leave'],
                ['name' => 'hrm.leaves.export', 'display_name' => 'Export Leaves'],
            ],
            
            // ==================== LEAVE TYPES ====================
            'Leave Types' => [
                ['name' => 'hrm.leave-types.view', 'display_name' => 'View Leave Types'],
                ['name' => 'hrm.leave-types.create', 'display_name' => 'Create Leave Type'],
                ['name' => 'hrm.leave-types.edit', 'display_name' => 'Edit Leave Type'],
                ['name' => 'hrm.leave-types.delete', 'display_name' => 'Delete Leave Type'],
            ],
            
            // ==================== LEAVE POLICIES ====================
            'Leave Policies' => [
                ['name' => 'hrm.leave-policies.view', 'display_name' => 'View Leave Policies'],
                ['name' => 'hrm.leave-policies.create', 'display_name' => 'Create Leave Policy'],
                ['name' => 'hrm.leave-policies.edit', 'display_name' => 'Edit Leave Policy'],
                ['name' => 'hrm.leave-policies.delete', 'display_name' => 'Delete Leave Policy'],
            ],
            
            // ==================== LEAVE ALLOCATIONS ====================
            'Leave Allocations' => [
                ['name' => 'hrm.leave-allocations.view', 'display_name' => 'View Leave Allocations'],
                ['name' => 'hrm.leave-allocations.create', 'display_name' => 'Create Leave Allocation'],
                ['name' => 'hrm.leave-allocations.edit', 'display_name' => 'Edit Leave Allocation'],
                ['name' => 'hrm.leave-allocations.delete', 'display_name' => 'Delete Leave Allocation'],
            ],
            
            // ==================== LEAVE APPROVAL CHAINS ====================
            'Leave Approval Chains' => [
                ['name' => 'hrm.leave-approval-chains.view', 'display_name' => 'View Approval Chains'],
                ['name' => 'hrm.leave-approval-chains.create', 'display_name' => 'Create Approval Chain'],
                ['name' => 'hrm.leave-approval-chains.edit', 'display_name' => 'Edit Approval Chain'],
                ['name' => 'hrm.leave-approval-chains.delete', 'display_name' => 'Delete Approval Chain'],
            ],
            
            // ==================== PAYROLL ====================
            'Payroll' => [
                ['name' => 'hrm.payroll.view', 'display_name' => 'View Payroll'],
                ['name' => 'hrm.payroll.create', 'display_name' => 'Create Payroll'],
                ['name' => 'hrm.payroll.edit', 'display_name' => 'Edit Payroll'],
                ['name' => 'hrm.payroll.delete', 'display_name' => 'Delete Payroll'],
                ['name' => 'hrm.payroll.process', 'display_name' => 'Process Payroll'],
                ['name' => 'hrm.payroll.approve', 'display_name' => 'Approve Payroll'],
                ['name' => 'hrm.payroll.export', 'display_name' => 'Export Payroll'],
                ['name' => 'hrm.payroll.print', 'display_name' => 'Print Payslips'],
                ['name' => 'hrm.payroll.send', 'display_name' => 'Send Payslips'],
            ],
            
            // ==================== BANK TRANSFERS ====================
            'Bank Transfers' => [
                ['name' => 'hrm.bank-transfers.view', 'display_name' => 'View Bank Transfers'],
                ['name' => 'hrm.bank-transfers.create', 'display_name' => 'Create Bank Transfer'],
                ['name' => 'hrm.bank-transfers.approve', 'display_name' => 'Approve Bank Transfer'],
                ['name' => 'hrm.bank-transfers.export', 'display_name' => 'Export Bank Transfers'],
            ],
            
            // ==================== BONUSES ====================
            'Bonuses' => [
                ['name' => 'hrm.bonuses.view', 'display_name' => 'View Bonuses'],
                ['name' => 'hrm.bonuses.create', 'display_name' => 'Create Bonus'],
                ['name' => 'hrm.bonuses.edit', 'display_name' => 'Edit Bonus'],
                ['name' => 'hrm.bonuses.delete', 'display_name' => 'Delete Bonus'],
                ['name' => 'hrm.bonuses.approve', 'display_name' => 'Approve Bonus'],
            ],
            
            // ==================== OVERTIME ====================
            'Overtime' => [
                ['name' => 'hrm.overtime.view', 'display_name' => 'View Overtime'],
                ['name' => 'hrm.overtime.create', 'display_name' => 'Create Overtime'],
                ['name' => 'hrm.overtime.edit', 'display_name' => 'Edit Overtime'],
                ['name' => 'hrm.overtime.delete', 'display_name' => 'Delete Overtime'],
                ['name' => 'hrm.overtime.approve', 'display_name' => 'Approve Overtime'],
            ],
            
            // ==================== LOANS ====================
            'Loans' => [
                ['name' => 'hrm.loans.view', 'display_name' => 'View Loans'],
                ['name' => 'hrm.loans.create', 'display_name' => 'Create Loan'],
                ['name' => 'hrm.loans.edit', 'display_name' => 'Edit Loan'],
                ['name' => 'hrm.loans.delete', 'display_name' => 'Delete Loan'],
                ['name' => 'hrm.loans.approve', 'display_name' => 'Approve Loan'],
            ],
            
            // ==================== GRATUITY ====================
            'Gratuity' => [
                ['name' => 'hrm.gratuity.view', 'display_name' => 'View Gratuity'],
                ['name' => 'hrm.gratuity.calculate', 'display_name' => 'Calculate Gratuity'],
                ['name' => 'hrm.gratuity.approve', 'display_name' => 'Approve Gratuity'],
            ],
            
            // ==================== SALARY COMPONENTS ====================
            'Salary Components' => [
                ['name' => 'hrm.salary-components.view', 'display_name' => 'View Salary Components'],
                ['name' => 'hrm.salary-components.create', 'display_name' => 'Create Salary Component'],
                ['name' => 'hrm.salary-components.edit', 'display_name' => 'Edit Salary Component'],
                ['name' => 'hrm.salary-components.delete', 'display_name' => 'Delete Salary Component'],
            ],
            
            // ==================== EXPENSES ====================
            'Expenses' => [
                ['name' => 'hrm.expenses.view', 'display_name' => 'View Expenses'],
                ['name' => 'hrm.expenses.create', 'display_name' => 'Create Expense'],
                ['name' => 'hrm.expenses.edit', 'display_name' => 'Edit Expense'],
                ['name' => 'hrm.expenses.delete', 'display_name' => 'Delete Expense'],
                ['name' => 'hrm.expenses.approve', 'display_name' => 'Approve Expense'],
                ['name' => 'hrm.expenses.reject', 'display_name' => 'Reject Expense'],
                ['name' => 'hrm.expenses.export', 'display_name' => 'Export Expenses'],
            ],
            
            // ==================== EXPENSE CATEGORIES ====================
            'Expense Categories' => [
                ['name' => 'hrm.expense-categories.view', 'display_name' => 'View Expense Categories'],
                ['name' => 'hrm.expense-categories.create', 'display_name' => 'Create Expense Category'],
                ['name' => 'hrm.expense-categories.edit', 'display_name' => 'Edit Expense Category'],
                ['name' => 'hrm.expense-categories.delete', 'display_name' => 'Delete Expense Category'],
            ],
            
            // ==================== ASSETS ====================
            'Assets' => [
                ['name' => 'hrm.assets.view', 'display_name' => 'View Assets'],
                ['name' => 'hrm.assets.create', 'display_name' => 'Create Asset'],
                ['name' => 'hrm.assets.edit', 'display_name' => 'Edit Asset'],
                ['name' => 'hrm.assets.delete', 'display_name' => 'Delete Asset'],
                ['name' => 'hrm.assets.assign', 'display_name' => 'Assign Asset'],
                ['name' => 'hrm.assets.return', 'display_name' => 'Return Asset'],
            ],
            
            // ==================== ASSET CATEGORIES ====================
            'Asset Categories' => [
                ['name' => 'hrm.asset-categories.view', 'display_name' => 'View Asset Categories'],
                ['name' => 'hrm.asset-categories.create', 'display_name' => 'Create Asset Category'],
                ['name' => 'hrm.asset-categories.edit', 'display_name' => 'Edit Asset Category'],
                ['name' => 'hrm.asset-categories.delete', 'display_name' => 'Delete Asset Category'],
            ],
            
            // ==================== KPIs ====================
            'KPIs' => [
                ['name' => 'hrm.kpis.view', 'display_name' => 'View KPIs'],
                ['name' => 'hrm.kpis.create', 'display_name' => 'Create KPI'],
                ['name' => 'hrm.kpis.edit', 'display_name' => 'Edit KPI'],
                ['name' => 'hrm.kpis.delete', 'display_name' => 'Delete KPI'],
            ],
            
            // ==================== OKRs ====================
            'OKRs' => [
                ['name' => 'hrm.okrs.view', 'display_name' => 'View OKRs'],
                ['name' => 'hrm.okrs.create', 'display_name' => 'Create OKR'],
                ['name' => 'hrm.okrs.edit', 'display_name' => 'Edit OKR'],
                ['name' => 'hrm.okrs.delete', 'display_name' => 'Delete OKR'],
            ],
            
            // ==================== GOALS ====================
            'Goals' => [
                ['name' => 'hrm.goals.view', 'display_name' => 'View Goals'],
                ['name' => 'hrm.goals.create', 'display_name' => 'Create Goal'],
                ['name' => 'hrm.goals.edit', 'display_name' => 'Edit Goal'],
                ['name' => 'hrm.goals.delete', 'display_name' => 'Delete Goal'],
            ],
            
            // ==================== APPRAISALS ====================
            'Appraisals' => [
                ['name' => 'hrm.appraisals.view', 'display_name' => 'View Appraisals'],
                ['name' => 'hrm.appraisals.create', 'display_name' => 'Create Appraisal'],
                ['name' => 'hrm.appraisals.edit', 'display_name' => 'Edit Appraisal'],
                ['name' => 'hrm.appraisals.delete', 'display_name' => 'Delete Appraisal'],
                ['name' => 'hrm.appraisals.submit', 'display_name' => 'Submit Appraisal'],
                ['name' => 'hrm.appraisals.review', 'display_name' => 'Review Appraisal'],
                ['name' => 'hrm.appraisals.approve', 'display_name' => 'Approve Appraisal'],
            ],
            
            // ==================== COMPETENCIES ====================
            'Competencies' => [
                ['name' => 'hrm.competencies.view', 'display_name' => 'View Competencies'],
                ['name' => 'hrm.competencies.create', 'display_name' => 'Create Competency'],
                ['name' => 'hrm.competencies.edit', 'display_name' => 'Edit Competency'],
                ['name' => 'hrm.competencies.delete', 'display_name' => 'Delete Competency'],
            ],
            
            // ==================== PIPs ====================
            'PIPs' => [
                ['name' => 'hrm.pips.view', 'display_name' => 'View PIPs'],
                ['name' => 'hrm.pips.create', 'display_name' => 'Create PIP'],
                ['name' => 'hrm.pips.edit', 'display_name' => 'Edit PIP'],
                ['name' => 'hrm.pips.delete', 'display_name' => 'Delete PIP'],
            ],
            
            // ==================== TRAININGS ====================
            'Trainings' => [
                ['name' => 'hrm.trainings.view', 'display_name' => 'View Trainings'],
                ['name' => 'hrm.trainings.create', 'display_name' => 'Create Training'],
                ['name' => 'hrm.trainings.edit', 'display_name' => 'Edit Training'],
                ['name' => 'hrm.trainings.delete', 'display_name' => 'Delete Training'],
                ['name' => 'hrm.trainings.enroll', 'display_name' => 'Enroll in Training'],
                ['name' => 'hrm.trainings.approve', 'display_name' => 'Approve Training'],
            ],
            
            // ==================== TRAINING SESSIONS ====================
            'Training Sessions' => [
                ['name' => 'hrm.training-sessions.view', 'display_name' => 'View Training Sessions'],
                ['name' => 'hrm.training-sessions.create', 'display_name' => 'Create Training Session'],
                ['name' => 'hrm.training-sessions.edit', 'display_name' => 'Edit Training Session'],
                ['name' => 'hrm.training-sessions.delete', 'display_name' => 'Delete Training Session'],
            ],
            
            // ==================== SKILLS ====================
            'Skills' => [
                ['name' => 'hrm.skills.view', 'display_name' => 'View Skills'],
                ['name' => 'hrm.skills.create', 'display_name' => 'Create Skill'],
                ['name' => 'hrm.skills.edit', 'display_name' => 'Edit Skill'],
                ['name' => 'hrm.skills.delete', 'display_name' => 'Delete Skill'],
                ['name' => 'hrm.skills.matrix', 'display_name' => 'View Skill Matrix'],
            ],
            
            // ==================== CERTIFICATIONS ====================
            'Certifications' => [
                ['name' => 'hrm.certifications.view', 'display_name' => 'View Certifications'],
                ['name' => 'hrm.certifications.create', 'display_name' => 'Create Certification'],
                ['name' => 'hrm.certifications.edit', 'display_name' => 'Edit Certification'],
                ['name' => 'hrm.certifications.delete', 'display_name' => 'Delete Certification'],
                ['name' => 'hrm.certifications.verify', 'display_name' => 'Verify Certification'],
            ],
            
            // ==================== JOBS ====================
            'Jobs' => [
                ['name' => 'hrm.jobs.view', 'display_name' => 'View Jobs'],
                ['name' => 'hrm.jobs.create', 'display_name' => 'Create Job'],
                ['name' => 'hrm.jobs.edit', 'display_name' => 'Edit Job'],
                ['name' => 'hrm.jobs.delete', 'display_name' => 'Delete Job'],
                ['name' => 'hrm.jobs.publish', 'display_name' => 'Publish Job'],
            ],
            
            // ==================== CANDIDATES ====================
            'Candidates' => [
                ['name' => 'hrm.candidates.view', 'display_name' => 'View Candidates'],
                ['name' => 'hrm.candidates.create', 'display_name' => 'Create Candidate'],
                ['name' => 'hrm.candidates.edit', 'display_name' => 'Edit Candidate'],
                ['name' => 'hrm.candidates.delete', 'display_name' => 'Delete Candidate'],
            ],
            
            // ==================== INTERVIEWS ====================
            'Interviews' => [
                ['name' => 'hrm.interviews.view', 'display_name' => 'View Interviews'],
                ['name' => 'hrm.interviews.schedule', 'display_name' => 'Schedule Interview'],
                ['name' => 'hrm.interviews.edit', 'display_name' => 'Edit Interview'],
                ['name' => 'hrm.interviews.cancel', 'display_name' => 'Cancel Interview'],
                ['name' => 'hrm.interviews.feedback', 'display_name' => 'Submit Feedback'],
            ],
            
            // ==================== OFFERS ====================
            'Offers' => [
                ['name' => 'hrm.offers.view', 'display_name' => 'View Offers'],
                ['name' => 'hrm.offers.create', 'display_name' => 'Create Offer'],
                ['name' => 'hrm.offers.approve', 'display_name' => 'Approve Offer'],
                ['name' => 'hrm.offers.send', 'display_name' => 'Send Offer'],
            ],
            
            // ==================== LETTERS ====================
            'Letters' => [
                ['name' => 'hrm.letters.view', 'display_name' => 'View Letters'],
                ['name' => 'hrm.letters.generate', 'display_name' => 'Generate Letter'],
            ],
            
            // ==================== LETTER TEMPLATES ====================
            'Letter Templates' => [
                ['name' => 'hrm.letter-templates.view', 'display_name' => 'View Letter Templates'],
                ['name' => 'hrm.letter-templates.create', 'display_name' => 'Create Letter Template'],
                ['name' => 'hrm.letter-templates.edit', 'display_name' => 'Edit Letter Template'],
                ['name' => 'hrm.letter-templates.delete', 'display_name' => 'Delete Letter Template'],
            ],
            
            // ==================== ONBOARDING ====================
            'Onboarding' => [
                ['name' => 'hrm.onboarding.view', 'display_name' => 'View Onboarding'],
                ['name' => 'hrm.onboarding.create', 'display_name' => 'Create Onboarding'],
                ['name' => 'hrm.onboarding.edit', 'display_name' => 'Edit Onboarding'],
                ['name' => 'hrm.onboarding.complete', 'display_name' => 'Complete Onboarding'],
            ],
            
            // ==================== POLICIES ====================
            'Policies' => [
                ['name' => 'hrm.policies.view', 'display_name' => 'View Policies'],
                ['name' => 'hrm.policies.create', 'display_name' => 'Create Policy'],
                ['name' => 'hrm.policies.edit', 'display_name' => 'Edit Policy'],
                ['name' => 'hrm.policies.delete', 'display_name' => 'Delete Policy'],
                ['name' => 'hrm.policies.publish', 'display_name' => 'Publish Policy'],
            ],
            
            // ==================== CONTRACTS ====================
            'Contracts' => [
                ['name' => 'hrm.contracts.view', 'display_name' => 'View Contracts'],
                ['name' => 'hrm.contracts.create', 'display_name' => 'Create Contract'],
                ['name' => 'hrm.contracts.edit', 'display_name' => 'Edit Contract'],
                ['name' => 'hrm.contracts.delete', 'display_name' => 'Delete Contract'],
                ['name' => 'hrm.contracts.sign', 'display_name' => 'Sign Contract'],
            ],
            
            // ==================== DOCUMENTS ====================
            'Documents' => [
                ['name' => 'hrm.documents.view', 'display_name' => 'View Documents'],
                ['name' => 'hrm.documents.upload', 'display_name' => 'Upload Documents'],
                ['name' => 'hrm.documents.download', 'display_name' => 'Download Documents'],
                ['name' => 'hrm.documents.verify', 'display_name' => 'Verify Documents'],
                ['name' => 'hrm.documents.delete', 'display_name' => 'Delete Documents'],
            ],
            
            // ==================== RESIGNATIONS ====================
            'Resignations' => [
                ['name' => 'hrm.resignations.view', 'display_name' => 'View Resignations'],
                ['name' => 'hrm.resignations.submit', 'display_name' => 'Submit Resignation'],
                ['name' => 'hrm.resignations.approve', 'display_name' => 'Approve Resignation'],
                ['name' => 'hrm.resignations.process', 'display_name' => 'Process Resignation'],
            ],
            
            // ==================== CLEARANCES ====================
            'Clearances' => [
                ['name' => 'hrm.clearances.view', 'display_name' => 'View Clearances'],
                ['name' => 'hrm.clearances.create', 'display_name' => 'Create Clearance'],
                ['name' => 'hrm.clearances.approve', 'display_name' => 'Approve Clearance'],
            ],
            
            // ==================== SETTLEMENTS ====================
            'Settlements' => [
                ['name' => 'hrm.settlements.view', 'display_name' => 'View Settlements'],
                ['name' => 'hrm.settlements.calculate', 'display_name' => 'Calculate Settlement'],
                ['name' => 'hrm.settlements.approve', 'display_name' => 'Approve Settlement'],
            ],
            
            // ==================== PROJECTS ====================
            'Projects' => [
                ['name' => 'hrm.projects.view', 'display_name' => 'View Projects'],
                ['name' => 'hrm.projects.create', 'display_name' => 'Create Project'],
                ['name' => 'hrm.projects.edit', 'display_name' => 'Edit Project'],
                ['name' => 'hrm.projects.delete', 'display_name' => 'Delete Project'],
            ],
            
            // ==================== TIME ENTRIES ====================
            'Time Entries' => [
                ['name' => 'hrm.time-entries.view', 'display_name' => 'View Time Entries'],
                ['name' => 'hrm.time-entries.create', 'display_name' => 'Create Time Entry'],
                ['name' => 'hrm.time-entries.edit', 'display_name' => 'Edit Time Entry'],
                ['name' => 'hrm.time-entries.delete', 'display_name' => 'Delete Time Entry'],
            ],
            
            // ==================== TIMESHEETS ====================
            'Timesheets' => [
                ['name' => 'hrm.timesheets.view', 'display_name' => 'View Timesheets'],
                ['name' => 'hrm.timesheets.approve', 'display_name' => 'Approve Timesheets'],
            ],
            
            // ==================== EMPLOYEE SELF SERVICE ====================
            'Employee Self Service (ESS)' => [
                ['name' => 'hrm.ess.dashboard', 'display_name' => 'ESS Dashboard'],
                ['name' => 'hrm.ess.profile.view', 'display_name' => 'View Own Profile'],
                ['name' => 'hrm.ess.profile.edit', 'display_name' => 'Edit Own Profile'],
                ['name' => 'hrm.ess.attendance.view', 'display_name' => 'View Own Attendance'],
                ['name' => 'hrm.ess.leaves.view', 'display_name' => 'View Own Leaves'],
                ['name' => 'hrm.ess.payslips.view', 'display_name' => 'View Own Payslips'],
                ['name' => 'hrm.ess.payslips.download', 'display_name' => 'Download Own Payslips'],
                ['name' => 'hrm.ess.assets.view', 'display_name' => 'View Own Assets'],
                ['name' => 'hrm.ess.expenses.view', 'display_name' => 'View Own Expenses'],
                ['name' => 'hrm.ess.goals.view', 'display_name' => 'View Own Goals'],
                ['name' => 'hrm.ess.trainings.view', 'display_name' => 'View Own Trainings'],
                ['name' => 'hrm.ess.certificates.request', 'display_name' => 'Request Certificates'],
                ['name' => 'hrm.ess.certificates.download', 'display_name' => 'Download Certificates'],
                ['name' => 'hrm.ess.holidays.view', 'display_name' => 'View Holidays'],
            ],
            
            // ==================== MANAGER SELF SERVICE ====================
            'Manager Self Service (MSS)' => [
                ['name' => 'hrm.mss.dashboard', 'display_name' => 'MSS Dashboard'],
                ['name' => 'hrm.mss.team.view', 'display_name' => 'View Team Members'],
                ['name' => 'hrm.mss.approvals.view', 'display_name' => 'View Team Approvals'],
                ['name' => 'hrm.mss.roster.manage', 'display_name' => 'Manage Team Roster'],
                ['name' => 'hrm.mss.performance.view', 'display_name' => 'View Team Performance'],
                ['name' => 'hrm.mss.reports.view', 'display_name' => 'View Team Reports'],
            ],
            
            // ==================== WORKFLOWS ====================
            'Workflows' => [
                ['name' => 'hrm.workflows.view', 'display_name' => 'View Workflows'],
                ['name' => 'hrm.workflows.create', 'display_name' => 'Create Workflow'],
                ['name' => 'hrm.workflows.edit', 'display_name' => 'Edit Workflow'],
                ['name' => 'hrm.workflows.delete', 'display_name' => 'Delete Workflow'],
            ],
            
            // ==================== AUTOMATION ====================
            'Automation' => [
                ['name' => 'hrm.automation.view', 'display_name' => 'View Automation'],
                ['name' => 'hrm.automation.manage', 'display_name' => 'Manage Automation'],
            ],
            
            // ==================== NOTIFICATIONS ====================
            'Notifications' => [
                ['name' => 'hrm.notifications.send', 'display_name' => 'Send Notifications'],
            ],
            
            // ==================== REPORTS ====================
            'HRM Reports' => [
                ['name' => 'hrm.reports.view', 'display_name' => 'View Reports'],
                ['name' => 'hrm.reports.export', 'display_name' => 'Export Reports'],
                ['name' => 'hrm.reports.payroll', 'display_name' => 'View Payroll Reports'],
                ['name' => 'hrm.reports.attendance', 'display_name' => 'View Attendance Reports'],
                ['name' => 'hrm.reports.leave', 'display_name' => 'View Leave Reports'],
                ['name' => 'hrm.reports.performance', 'display_name' => 'View Performance Reports'],
            ],
            
            // ==================== ANALYTICS ====================
            'HRM Analytics' => [
                ['name' => 'hrm.analytics.view', 'display_name' => 'View Analytics'],
                ['name' => 'hrm.analytics.export', 'display_name' => 'Export Analytics'],
            ],
            
            // ==================== SETTINGS ====================
            'HRM Settings' => [
                ['name' => 'hrm.settings.view', 'display_name' => 'View Settings'],
                ['name' => 'hrm.settings.manage', 'display_name' => 'Manage Settings'],
            ],
        ];

        // Create permissions
        $allPermissions = [];
        foreach ($permissionsByModule as $moduleName => $permissions) {
            $module = $createdModules[$moduleName];
            
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission['name']],
                    [
                        'module_id' => $module->id,
                        'guard_name' => 'web',
                    ]
                );
                $allPermissions[] = $permission['name'];
            }
        }

        // Assign all to Super Admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
        }

        $this->command->info('✅ Complete HRM permissions seeded!');
        $this->command->info('📊 Total permissions: ' . count($allPermissions));
        $this->command->info('📁 Total feature modules: ' . count($createdModules));
        $this->command->info('');
        $this->command->info('✨ All permissions organized by feature for easy management!');
    }
}
