<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipActionItem extends Model
{
    use HasFactory;

    protected $table = 'hrm_pip_action_items';

    protected $fillable = [
        'pip_id',
        'action_required',
        'expected_outcome',
        'due_date',
        'status',
        'evidence',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relationships
    public function pip(): BelongsTo
    {
        return $this->belongsTo(Pip::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', ['pending', 'in_progress']);
    }

    // Methods
    public function isOverdue(): bool
    {
        return $this->due_date < now() && in_array($this->status, ['pending', 'in_progress']);
    }
}
