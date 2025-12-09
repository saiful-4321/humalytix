<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeBonus extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_employee_bonuses';

    protected $fillable = [
        'employee_id',
        'bonus_type_id',
        'bonus_name',
        'amount',
        'bonus_date',
        'bonus_month',
        'bonus_year',
        'status',
        'is_taxable',
        'remarks',
        'approved_by',
        'approved_at',
        'payroll_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bonus_date' => 'date',
        'is_taxable' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bonusType()
    {
        return $this->belongsTo(BonusType::class);
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
}
