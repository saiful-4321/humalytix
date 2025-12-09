<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOvertime extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_employee_overtime';

    protected $fillable = [
        'employee_id',
        'ot_policy_id',
        'ot_date',
        'start_time',
        'end_time',
        'total_minutes',
        'total_hours',
        'ot_type',
        'multiplier',
        'hourly_rate',
        'ot_amount',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
        'payroll_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ot_date' => 'date',
        'total_hours' => 'decimal:2',
        'multiplier' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'ot_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function policy()
    {
        return $this->belongsTo(OTPolicy::class, 'ot_policy_id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
