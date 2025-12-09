<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionScheme extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_commission_schemes';

    protected $fillable = [
        'name',
        'description',
        'commission_type',
        'rate',
        'min_target',
        'max_target',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'min_target' => 'decimal:2',
        'max_target' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function commissions()
    {
        return $this->hasMany(EmployeeCommission::class, 'scheme_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
