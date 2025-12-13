<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pip extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_pips';

    protected $fillable = [
        'employee_id',
        'manager_id',
        'title',
        'reason',
        'start_date',
        'end_date',
        'review_date',
        'status',
        'success_criteria',
        'outcomes',
        'final_review',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'review_date' => 'date',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function actionItems(): HasMany
    {
        return $this->hasMany(PipActionItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PipReview::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())
            ->where('status', 'active');
    }

    // Methods
    public function getCompletionPercentage(): int
    {
        $total = $this->actionItems->count();
        if ($total == 0) {
            return 0;
        }

        $completed = $this->actionItems()->where('status', 'completed')->count();
        return round(($completed / $total) * 100);
    }

    public function isOverdue(): bool
    {
        return $this->end_date < now() && $this->status === 'active';
    }

    public function getDaysRemaining(): int
    {
        if ($this->end_date < now()) {
            return 0;
        }

        return now()->diffInDays($this->end_date);
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'active' => 'primary',
            'successful' => 'success',
            'unsuccessful' => 'danger',
            'extended' => 'warning',
            'cancelled' => 'secondary',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }
}
