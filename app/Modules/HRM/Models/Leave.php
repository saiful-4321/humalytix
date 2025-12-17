<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_leaves';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'approved_by', // Deprecated but kept for backward compatibility if needed, or mapped to final approver
        'rejected_by',
        'approved_at',
        'rejection_reason',
        'attachment',
        'approval_chain_id',
        'current_level',
        'is_completed',
        'created_by',
        'updated_by',
        'half_day_session',
        'is_half_day',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'days' => 'float',
        'is_completed' => 'boolean',
        'is_half_day' => 'boolean',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
    
    public function approvalChain(): BelongsTo
    {
        return $this->belongsTo(ApprovalChain::class);
    }

    public function approvalLogs()
    {
        return $this->hasMany(LeaveApprovalLog::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
