<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_branches';

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'is_head_office',
        'is_active',
        'latitude',
        'longitude',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_head_office' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function geofenceLocations(): HasMany
    {
        return $this->hasMany(GeofenceLocation::class);
    }

    public function biometricDevices(): HasMany
    {
        return $this->hasMany(BiometricDevice::class);
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

    public function scopeHeadOffice($query)
    {
        return $query->where('is_head_office', true);
    }

    // Methods
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
