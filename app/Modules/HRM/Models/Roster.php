<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Roster extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_rosters';

    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'week_number',
        'is_off_day',
        'status',
        'published_by',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'week_number' => 'integer',
        'is_off_day' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'published_by');
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
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByWeek($query, $weekNumber)
    {
        return $query->where('week_number', $weekNumber);
    }
}
