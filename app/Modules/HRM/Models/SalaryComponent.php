<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryComponent extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_salary_components';

    protected $fillable = [
        'name',
        'type', // earning, deduction
        'is_taxable',
        'calculation_type', // fixed, percentage, formula
        'default_amount',
        'default_percentage',
        'percentage_basis_id',
        'is_active',
    ];

    public function percentageBasis()
    {
        return $this->belongsTo(SalaryComponent::class, 'percentage_basis_id');
    }
}
