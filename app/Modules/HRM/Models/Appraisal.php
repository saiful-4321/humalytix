<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appraisal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_appraisals';

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period',
        'review_date',
        'performance_score',
        'strengths',
        'weaknesses',
        'goals',
        'comments',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'review_date' => 'date',
        'performance_score' => 'integer',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
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
