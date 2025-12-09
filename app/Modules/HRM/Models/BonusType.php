<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusType extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_bonus_types';

    protected $fillable = [
        'name',
        'description',
        'calculation_type',
        'default_amount',
        'default_percentage',
        'frequency',
        'eligibility_criteria',
        'is_taxable',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'default_percentage' => 'decimal:2',
        'eligibility_criteria' => 'array',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function bonuses()
    {
        return $this->hasMany(EmployeeBonus::class, 'bonus_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
