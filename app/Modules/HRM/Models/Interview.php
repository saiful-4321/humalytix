<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_interviews';

    protected $fillable = [
        'candidate_id',
        'job_id',
        'interview_type',
        'scheduled_at',
        'duration',
        'location',
        'meeting_link',
        'status',
        'feedback',
        'rating',
        'recommendation',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration' => 'integer',
        'rating' => 'integer',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function interviewers()
    {
        return $this->belongsToMany(Employee::class, 'hrm_interview_interviewers', 'interview_id', 'employee_id');
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
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now()->toDateString());
    }
}
