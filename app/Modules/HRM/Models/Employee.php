<?php

namespace App\Modules\HRM\Models;

use App\Modules\HRM\Enums\EmployeeStatusEnum;
use App\Modules\HRM\Enums\EmploymentTypeEnum;
use App\Modules\HRM\Enums\GenderEnum;
use App\Modules\HRM\Enums\MaritalStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_employees';

    protected $fillable = [
        'employee_code',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'blood_group',
        'marital_status',
        'nationality',
        'religion',
        'nid_number',
        'passport_number',
        'tax_id',
        'driving_license',
        'present_address',
        'permanent_address',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'department_id',
        'branch_id',
        'business_unit_id',
        'designation',
        'employment_type',
        'joining_date',
        'confirmation_date',
        'probation_end_date',
        'reporting_to',
        'basic_salary',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'status',
        'remarks',
        'photo',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'confirmation_date' => 'date',
        'probation_end_date' => 'date',
        'basic_salary' => 'decimal:2',
    ];

    protected $appends = ['full_name'];

    // Boot method to auto-generate employee code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_code)) {
                $employee->employee_code = generate_employee_code();
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reporting_to');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'reporting_to');
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function employmentHistories(): HasMany
    {
        return $this->hasMany(EmploymentHistory::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class);
    }

    public function lifecycles(): HasMany
    {
        return $this->hasMany(EmployeeLifecycle::class);
    }

    public function letters(): HasMany
    {
        return $this->hasMany(EmployeeLetter::class);
    }

    public function onboardingProcesses(): HasMany
    {
        return $this->hasMany(OnboardingProcess::class);
    }

    public function resignation()
    {
        return $this->hasOne(Resignation::class)->latest();
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function rosters(): HasMany
    {
        return $this->hasMany(Roster::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function salary()
    {
        return $this->hasOne(EmployeeSalary::class)->orderBy('effective_date', 'desc');
    }

    public function salaryHistory()
    {
        return $this->hasMany(EmployeeSalary::class)->orderBy('effective_date', 'desc');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', EmployeeStatusEnum::ACTIVE->value);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('employee_code', 'like', "%{$search}%")
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getTenureAttribute(): ?string
    {
        if (!$this->joining_date) {
            return null;
        }

        $diff = $this->joining_date->diff(now());
        $years = $diff->y;
        $months = $diff->m;

        if ($years > 0) {
            return "{$years} year" . ($years > 1 ? 's' : '') . 
                   ($months > 0 ? " {$months} month" . ($months > 1 ? 's' : '') : '');
        }

        return "{$months} month" . ($months > 1 ? 's' : '');
    }

    public function getStatusBadgeAttribute(): string
    {
        try {
            $enum = EmployeeStatusEnum::from($this->status);
            return '<span class="badge bg-' . $enum->color() . '">' . $enum->label() . '</span>';
        } catch (\Exception $e) {
            return '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>';
        }
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === EmployeeStatusEnum::ACTIVE->value;
    }

    public function isOnProbation(): bool
    {
        return $this->status === EmployeeStatusEnum::PROBATION->value;
    }

    public function calculateTotalExperience(): float
    {
        $previousExperience = $this->employmentHistories()
            ->get()
            ->sum(function ($history) {
                $start = \Carbon\Carbon::parse($history->start_date);
                $end = $history->end_date ? \Carbon\Carbon::parse($history->end_date) : now();
                return $start->diffInMonths($end) / 12;
            });

        $currentExperience = $this->joining_date ? 
            \Carbon\Carbon::parse($this->joining_date)->diffInMonths(now()) / 12 : 0;

        return round($previousExperience + $currentExperience, 1);
    }
}
