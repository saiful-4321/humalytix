<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalReviewer extends Model
{
    use HasFactory;

    protected $table = 'hrm_appraisal_reviewers';

    protected $fillable = [
        'appraisal_id',
        'reviewer_id',
        'reviewer_type',
        'status',
        'rating',
        'feedback',
        'strengths',
        'areas_for_improvement',
        'completed_at',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(Appraisal360::class, 'appraisal_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function competencyRatings(): HasMany
    {
        return $this->hasMany(AppraisalCompetencyRating::class, 'appraisal_reviewer_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('reviewer_type', $type);
    }

    // Methods
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update parent appraisal score
        $this->appraisal->updateOverallScore();
    }

    public function calculateAverageCompetencyRating()
    {
        if ($this->competencyRatings->isEmpty()) {
            return null;
        }

        return round($this->competencyRatings->avg('rating'), 1);
    }
}
