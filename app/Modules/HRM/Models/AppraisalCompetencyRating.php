<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppraisalCompetencyRating extends Model
{
    use HasFactory;

    protected $table = 'hrm_appraisal_competency_ratings';

    protected $fillable = [
        'appraisal_reviewer_id',
        'competency_id',
        'rating',
        'comments',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Relationships
    public function appraisalReviewer(): BelongsTo
    {
        return $this->belongsTo(AppraisalReviewer::class);
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }
}
