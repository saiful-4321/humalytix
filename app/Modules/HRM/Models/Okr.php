<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Okr extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_okrs';

    protected $fillable = [
        'title',
        'description',
        'level',
        'department_id',
        'employee_id',
        'start_date',
        'end_date',
        'quarter',
        'year',
        'status',
        'progress',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'year' => 'integer',
        'progress' => 'integer',
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function keyResults(): HasMany
    {
        return $this->hasMany(OkrKeyResult::class);
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
        return $query->where('status', 'active');
    }

    public function scopeCompany($query)
    {
        return $query->where('level', 'company');
    }

    public function scopeDepartment($query)
    {
        return $query->where('level', 'department');
    }

    public function scopeIndividual($query)
    {
        return $query->where('level', 'individual');
    }

    public function scopeByQuarter($query, $quarter, $year)
    {
        return $query->where('quarter', $quarter)->where('year', $year);
    }

    // Methods
    public function calculateProgress()
    {
        if ($this->keyResults->isEmpty()) {
            return 0;
        }

        $totalWeightage = $this->keyResults->sum('weightage');
        if ($totalWeightage == 0) {
            return 0;
        }

        $weightedProgress = $this->keyResults->sum(function ($kr) {
            return ($kr->progress * $kr->weightage) / 100;
        });

        return round($weightedProgress);
    }

    public function updateProgress()
    {
        $this->update(['progress' => $this->calculateProgress()]);
    }
}
