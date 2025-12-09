<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanType extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_loan_types';

    protected $fillable = [
        'name',
        'description',
        'max_amount',
        'interest_rate',
        'interest_type',
        'max_tenure_months',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function loans()
    {
        return $this->hasMany(EmployeeLoan::class, 'loan_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
