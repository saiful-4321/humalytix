<?php

namespace App\Modules\HRM\Services;

use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\EmployeeSkill;
use App\Modules\HRM\Models\FamilyMember;
use App\Modules\HRM\Models\EmploymentHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeService
{
    /**
     * Generate unique employee code
     */
    public function generateEmployeeCode(): string
    {
        return generate_employee_code();
    }

    /**
     * Create employee with related data
     */
    public function createEmployee(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            // Handle photo upload
            if (isset($data['photo']) && $data['photo']) {
                $data['photo'] = $this->uploadPhoto($data['photo']);
            }

            // Create employee
            $employee = Employee::create($data);

            // Create family members if provided
            if (isset($data['family_members']) && is_array($data['family_members'])) {
                foreach ($data['family_members'] as $member) {
                    $employee->familyMembers()->create($member);
                }
            }

            // Add employment history if provided
            if (isset($data['employment_histories']) && is_array($data['employment_histories'])) {
                foreach ($data['employment_histories'] as $history) {
                    $employee->employmentHistories()->create($history);
                }
            }

            // Add skills if provided
            if (isset($data['skills']) && is_array($data['skills'])) {
                foreach ($data['skills'] as $skill) {
                    $employee->skills()->create($skill);
                }
            }

            return $employee->load(['department', 'branch', 'reportingManager']);
        });
    }

    /**
     * Update employee
     */
    public function updateEmployee(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            // Handle photo upload
            if (isset($data['photo']) && $data['photo']) {
                // Delete old photo
                if ($employee->photo) {
                    Storage::disk('public')->delete($employee->photo);
                }
                $data['photo'] = $this->uploadPhoto($data['photo']);
            }

            $employee->update($data);

            return $employee->load(['department', 'branch', 'reportingManager']);
        });
    }

    /**
     * Delete employee
     */
    public function deleteEmployee(Employee $employee): bool
    {
        return DB::transaction(function () use ($employee) {
            // Delete photo
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            return $employee->delete();
        });
    }

    /**
     * Calculate total experience
     */
    public function calculateTotalExperience(Employee $employee): float
    {
        return $employee->calculateTotalExperience();
    }

    /**
     * Get employee hierarchy (reporting structure)
     */
    public function getEmployeeHierarchy(Employee $employee): array
    {
        $hierarchy = [];
        $current = $employee;

        // Get upward hierarchy
        while ($current->reportingManager) {
            $hierarchy[] = [
                'id' => $current->reportingManager->id,
                'name' => $current->reportingManager->full_name,
                'designation' => $current->reportingManager->designation,
                'level' => 'manager',
            ];
            $current = $current->reportingManager;
        }

        // Reverse to show top-down
        $hierarchy = array_reverse($hierarchy);

        // Add current employee
        $hierarchy[] = [
            'id' => $employee->id,
            'name' => $employee->full_name,
            'designation' => $employee->designation,
            'level' => 'current',
        ];

        // Add subordinates
        foreach ($employee->subordinates as $subordinate) {
            $hierarchy[] = [
                'id' => $subordinate->id,
                'name' => $subordinate->full_name,
                'designation' => $subordinate->designation,
                'level' => 'subordinate',
            ];
        }

        return $hierarchy;
    }

    /**
     * Link employee to user account
     */
    public function linkToUser(Employee $employee, int $userId): Employee
    {
        $employee->update(['user_id' => $userId]);
        return $employee;
    }

    /**
     * Get employees by department
     */
    public function getByDepartment(int $departmentId)
    {
        return Employee::where('department_id', $departmentId)
            ->with(['department', 'branch'])
            ->get();
    }

    /**
     * Get employees by branch
     */
    public function getByBranch(int $branchId)
    {
        return Employee::where('branch_id', $branchId)
            ->with(['department', 'branch'])
            ->get();
    }

    /**
     * Search employees
     */
    public function search(string $query)
    {
        return Employee::search($query)
            ->with(['department', 'branch'])
            ->get();
    }

    /**
     * Get active employees count
     */
    public function getActiveCount(): int
    {
        return Employee::active()->count();
    }

    /**
     * Get employees on probation
     */
    public function getOnProbation()
    {
        return Employee::where('status', 'probation')
            ->with(['department', 'branch'])
            ->get();
    }

    /**
     * Upload employee photo
     */
    private function uploadPhoto($photo): string
    {
        return $photo->store('employees/photos', 'public');
    }

    /**
     * Bulk import employees
     */
    public function bulkImport(array $employees): array
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($employees as $index => $employeeData) {
            try {
                DB::transaction(function () use ($employeeData) {
                    Employee::create($employeeData);
                });
                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Export employees
     */
    public function exportEmployees(array $filters = [])
    {
        $query = Employee::with(['department', 'branch', 'reportingManager']);

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }
}
