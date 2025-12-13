<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kpi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_kpis';

    protected $fillable = [
        'name',
        'description',
        'type',
        'department_id',
        'measurement_unit',
        'target_value',
        'frequency',
        'weightage',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'weightage' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function performanceGoals(): HasMany
    {
        return $this->hasMany(PerformanceGoal::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKpi($query)
    {
        return $query->where('type', 'kpi');
    }

    public function scopeKra($query)
    {
        return $this->where('type', 'kra');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
