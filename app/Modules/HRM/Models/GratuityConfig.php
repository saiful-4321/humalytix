<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class GratuityConfig extends Model
{
    protected $table = 'hrm_gratuity_config';

    protected $fillable = [
        'name',
        'description',
        'min_service_years',
        'multiplier',
        'calculation_formula',
        'formula_description',
        'max_gratuity_amount',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'min_service_years' => 'decimal:2',
        'multiplier' => 'decimal:2',
        'max_gratuity_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function gratuities()
    {
        return $this->hasMany(EmployeeGratuity::class, 'gratuity_config_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
