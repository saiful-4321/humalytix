<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appraisal360 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_360_appraisals';

    protected $fillable = [
        'employee_id',
        'appraisal_name',
        'review_period',
        'start_date',
        'end_date',
        'status',
        'overall_score',
        'summary',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'overall_score' => 'integer',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(AppraisalReviewer::class, 'appraisal_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // Methods
    public function calculateOverallScore()
    {
        $completedReviews = $this->reviewers()->where('status', 'completed')->get();
        
        if ($completedReviews->isEmpty()) {
            return null;
        }

        return round($completedReviews->avg('rating'));
    }

    public function updateOverallScore()
    {
        $this->update(['overall_score' => $this->calculateOverallScore()]);
    }

    public function getCompletionPercentage(): int
    {
        $total = $this->reviewers->count();
        if ($total == 0) {
            return 0;
        }

        $completed = $this->reviewers()->where('status', 'completed')->count();
        return round(($completed / $total) * 100);
    }

    public function getSelfReview()
    {
        return $this->reviewers()->where('reviewer_type', 'self')->first();
    }

    public function getManagerReview()
    {
        return $this->reviewers()->where('reviewer_type', 'manager')->first();
    }

    public function getPeerReviews()
    {
        return $this->reviewers()->where('reviewer_type', 'peer')->get();
    }
}
