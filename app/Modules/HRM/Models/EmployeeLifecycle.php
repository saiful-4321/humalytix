<?php

namespace App\Modules\HRM\Models;

use App\Modules\HRM\Enums\LifecycleStageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLifecycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_employee_lifecycles';

    protected $fillable = [
        'employee_id',
        'stage',
        'status',
        'started_at',
        'completed_at',
        'notes',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
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
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Accessors
    public function getStageBadgeAttribute(): string
    {
        try {
            $enum = LifecycleStageEnum::from($this->stage);
            return '<span class="badge bg-' . $enum->color() . '">' . $enum->label() . '</span>';
        } catch (\Exception $e) {
            return '<span class="badge bg-secondary">' . ucfirst($this->stage) . '</span>';
        }
    }
}
