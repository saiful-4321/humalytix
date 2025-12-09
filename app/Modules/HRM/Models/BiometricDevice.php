<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricDevice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_biometric_devices';

    protected $fillable = [
        'name',
        'device_id',
        'ip_address',
        'port',
        'location',
        'branch_id',
        'is_active',
        'last_sync_at',
        'settings',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'port' => 'integer',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
        'settings' => 'array',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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
