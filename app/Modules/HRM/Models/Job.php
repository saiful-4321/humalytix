<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_jobs';

    protected $fillable = [
        'title',
        'code',
        'department_id',
        'branch_id',
        'employment_type',
        'experience_required',
        'salary_range_min',
        'salary_range_max',
        'description',
        'requirements',
        'responsibilities',
        'benefits',
        'vacancies',
        'posted_date',
        'closing_date',
        'status',
        'is_published',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'experience_required' => 'integer',
        'salary_range_min' => 'decimal:2',
        'salary_range_max' => 'decimal:2',
        'vacancies' => 'integer',
        'posted_date' => 'date',
        'closing_date' => 'date',
        'is_published' => 'boolean',
    ];

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->code)) {
                $job->code = generate_job_code();
            }
        });
    }

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
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
        return $query->where('status', 'active');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'active')
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('closing_date')
                  ->orWhere('closing_date', '>=', now());
            });
    }

    // Methods
    public function isOpen(): bool
    {
        return $this->status === 'active' && 
               $this->is_published && 
               (!$this->closing_date || $this->closing_date->isFuture());
    }
}
