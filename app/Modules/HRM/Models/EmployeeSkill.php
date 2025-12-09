<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSkill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_employee_skills';

    protected $fillable = [
        'employee_id',
        'skill_id',
        'proficiency_level',
        'years_of_experience',
        'last_used_date',
        'certification_url',
        'notes',
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
        'last_used_date' => 'date',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    // Scopes
    public function scopeByProficiency($query, $level)
    {
        return $query->where('proficiency_level', $level);
    }

    public function scopeExpert($query)
    {
        return $query->where('proficiency_level', 'expert');
    }

    // Accessors
    public function getProficiencyBadgeAttribute(): string
    {
        $colors = [
            'beginner' => 'secondary',
            'intermediate' => 'info',
            'advanced' => 'primary',
            'expert' => 'success',
        ];

        $color = $colors[$this->proficiency_level] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->proficiency_level) . '</span>';
    }
}
