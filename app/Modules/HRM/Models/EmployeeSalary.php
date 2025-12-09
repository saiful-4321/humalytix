<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalary extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_employee_salaries';

    protected $fillable = [
        'employee_id',
        'salary_structure_id',
        'gross_salary', // Base for structure-less or gross-based structures
        'basic_salary', // Explicit basic
        'effective_date',
        'is_active',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'gross_salary' => 'decimal:2',
        'basic_salary' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function structure()
    {
        return $this->belongsTo(SalaryStructure::class, 'salary_structure_id');
    }
}
