<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryStructure extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_salary_structures';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function components()
    {
        return $this->belongsToMany(SalaryComponent::class, 'hrm_salary_structure_components')
                    ->withPivot('calculation_type', 'amount', 'percentage')
                    ->withTimestamps();
    }
}
