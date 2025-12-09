<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeGratuity extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_employee_gratuity';

    protected $fillable = [
        'employee_id',
        'gratuity_config_id',
        'calculation_date',
        'service_years',
        'last_basic_salary',
        'calculated_amount',
        'approved_amount',
        'status',
        'approved_by',
        'approved_at',
        'payment_date',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'calculation_date' => 'date',
        'service_years' => 'decimal:2',
        'last_basic_salary' => 'decimal:2',
        'calculated_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function config()
    {
        return $this->belongsTo(GratuityConfig::class, 'gratuity_config_id');
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
