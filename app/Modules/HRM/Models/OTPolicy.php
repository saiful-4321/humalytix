<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OTPolicy extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_ot_policies';

    protected $fillable = [
        'name',
        'description',
        'calculation_basis',
        'multiplier',
        'weekend_multiplier',
        'holiday_multiplier',
        'night_shift_multiplier',
        'night_shift_start',
        'night_shift_end',
        'min_ot_minutes',
        'max_ot_hours_per_day',
        'max_ot_hours_per_month',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'multiplier' => 'decimal:2',
        'weekend_multiplier' => 'decimal:2',
        'holiday_multiplier' => 'decimal:2',
        'night_shift_multiplier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function overtimeRecords()
    {
        return $this->hasMany(EmployeeOvertime::class, 'ot_policy_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
