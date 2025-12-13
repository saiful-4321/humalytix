# ESS Technical Documentation

## Architecture
The ESS Portal is built as a separate logical section within the HRM module.
- **Controller**: `App\Modules\HRM\Http\Controllers\EmployeeServiceController`
- **Prefix**: `hrm/ess/*`
- **Route Name Prefix**: `hrm.ess.*`

## Models
Two new models were introduced for ESS features:
1. `AttendanceRegularization` (`hrm_attendance_regularizations`)
   - Tracks requests to correct attendance records.
   - Fields: `employee_id`, `date`, `check_in/out`, `reason`, `status`.
2. `LetterRequest` (`hrm_letter_requests`)
   - Tracks requests for generated letters.
   - Fields: `employee_id`, `type`, `reason`, `status`, `letter_id` (linked `hrm_employee_letters` upon approval).

## Views
Located in `app/Modules/HRM/resources/views/pages/ess/`:
- `dashboard.blade.php`: Main landing page.
- `profile.blade.php`: Profile view/edit.
- `attendance.blade.php`: Attendance logs + Regularization modal.
- `payslips.blade.php`: Payroll list.
- `assets.blade.php`: Asset list.
- `holidays.blade.php`: Holiday list.
- `salary-certificate.blade.php`: Letter request list + modal.

## Role & Permission
- The "Self Service" sidebar menu is available to all logged-in users (currently placed outside the Admin-only permission block in Sidebar).
- `EmployeeServiceController` relies on `metrics` (Auth) and expects `auth()->user()->employee` to be present.

## Relationships
- `EmployeeServiceController` aggregates data from `Leave`, `Attendance`, `Payroll`, `Asset`, `Holiday`, and `PerformanceGoal` (via redirect).
- **Payslips**: Reuses `PayrollController::downloadPDF` route.
- **Goals**: Redirects to `PerformanceGoalController::myGoals`.
- **Leaves**: Redirects to `LeaveController::myLeaves`.
