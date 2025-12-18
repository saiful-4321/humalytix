<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Training extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_trainings';

    protected $fillable = [
        'code',
        'title',
        'description',
        'trainer',
        'type', // internal, external, online
        'duration_hours',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_hours' => 'integer',
    ];

    // Relationships
    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
