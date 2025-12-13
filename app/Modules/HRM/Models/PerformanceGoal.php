<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceGoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_performance_goals';

    protected $fillable = [
        'employee_id',
        'kpi_id',
        'title',
        'description',
        'start_date',
        'due_date',
        'priority',
        'status',
        'progress',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'progress' => 'integer',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['not_started', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', ['not_started', 'in_progress']);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    // Methods
    public function isOverdue(): bool
    {
        return $this->due_date < now() && in_array($this->status, ['not_started', 'in_progress']);
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'not_started' => 'secondary',
            'in_progress' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        $label = ucfirst(str_replace('_', ' ', $this->status));
        
        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    public function getPriorityBadgeAttribute(): string
    {
        $colors = [
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'orange',
            'critical' => 'danger',
        ];

        $color = $colors[$this->priority] ?? 'secondary';
        
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->priority) . '</span>';
    }
}
