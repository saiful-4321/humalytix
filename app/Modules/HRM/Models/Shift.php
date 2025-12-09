<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_shifts';

    protected $fillable = [
        'name',
        'code',
        'start_time',
        'end_time',
        'grace_period_minutes',
        'half_day_hours',
        'full_day_hours',
        'break_duration_minutes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'grace_period_minutes' => 'integer',
        'half_day_hours' => 'integer',
        'full_day_hours' => 'integer',
        'break_duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function rosters()
    {
        return $this->hasMany(Roster::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
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
        return $query->where('is_active', true);
    }
}
