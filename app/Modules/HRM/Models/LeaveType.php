<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_leave_types';

    protected $fillable = [
        'name',
        'code',
        'days_per_year',
        'is_paid',
        'is_active',
        'description',
        'approval_chain_id',
        // Policy Columns
        'is_unlimited',
        'accrual_rate',
        'accrual_frequency', // monthly, yearly, quarterly
        'carry_forward_limit',
        'allow_encashment',
        'encashment_limit',
        'require_approval',
        'requires_attachment',
        'created_by',
        'updated_by',
    ];

    public function approvalChain()
    {
        return $this->belongsTo(ApprovalChain::class);
    }

    protected $casts = [
        'days_per_year' => 'integer',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
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

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }
}
