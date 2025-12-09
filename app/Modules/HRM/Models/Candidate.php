<?php

namespace App\Modules\HRM\Models;

use App\Modules\HRM\Enums\CandidateStageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_candidates';

    protected $fillable = [
        'candidate_code',
        'job_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'current_location',
        'expected_salary',
        'notice_period',
        'total_experience',
        'resume_path',
        'cover_letter',
        'source',
        'referred_by',
        'stage',
        'status',
        'applied_date',
        'rating',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'applied_date' => 'date',
        'expected_salary' => 'decimal:2',
        'notice_period' => 'integer',
        'total_experience' => 'integer',
        'rating' => 'integer',
    ];

    protected $appends = ['full_name'];

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($candidate) {
            if (empty($candidate->candidate_code)) {
                $candidate->candidate_code = generate_candidate_code();
            }
        });
    }

    // Relationships
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'referred_by');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function offerLetter()
    {
        return $this->hasOne(OfferLetter::class)->latest();
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
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getStageBadgeAttribute(): string
    {
        try {
            $enum = CandidateStageEnum::from($this->stage);
            return '<span class="badge bg-' . $enum->color() . '">' . $enum->label() . '</span>';
        } catch (\Exception $e) {
            return '<span class="badge bg-secondary">' . ucfirst($this->stage) . '</span>';
        }
    }
}
