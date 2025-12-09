<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    protected $table = 'hrm_payroll_items';

    protected $fillable = [
        'payroll_id',
        'salary_component_id',
        'component_name',
        'type',
        'amount',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function component()
    {
        return $this->belongsTo(SalaryComponent::class, 'salary_component_id');
    }
}
