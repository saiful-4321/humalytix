<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_offer_letters';

    protected $fillable = [
        'candidate_id',
        'template_id',
        'designation',
        'department_id',
        'branch_id',
        'salary',
        'joining_date',
        'offer_date',
        'expiry_date',
        'content',
        'status',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'file_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'joining_date' => 'date',
        'offer_date' => 'date',
        'expiry_date' => 'date',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(LetterTemplate::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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
    public function scopePending($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    // Methods
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
