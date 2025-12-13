<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competency extends Model
{
    use HasFactory;

    protected $table = 'hrm_competencies';

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function ratings(): HasMany
    {
        return $this->hasMany(AppraisalCompetencyRating::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCore($query)
    {
        return $query->where('type', 'core');
    }

    public function scopeFunctional($query)
    {
        return $query->where('type', 'functional');
    }

    public function scopeLeadership($query)
    {
        return $query->where('type', 'leadership');
    }
}
