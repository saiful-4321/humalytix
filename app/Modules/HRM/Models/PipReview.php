<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipReview extends Model
{
    use HasFactory;

    protected $table = 'hrm_pip_reviews';

    protected $fillable = [
        'pip_id',
        'review_date',
        'progress_summary',
        'rating',
        'manager_comments',
        'employee_comments',
        'reviewed_by',
    ];

    protected $casts = [
        'review_date' => 'date',
        'rating' => 'integer',
    ];

    // Relationships
    public function pip(): BelongsTo
    {
        return $this->belongsTo(Pip::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }
}
