<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_assets';

    protected $fillable = [
        'name',
        'code',
        'type', // laptop, mobile, vehicle, license, etc.
        'serial_number',
        'purchase_date',
        'purchase_cost',
        'condition', // new, good, fair, poor
        'status', // available, assigned, maintenance, lost
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    // Relationships
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }
    
    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class)->whereNull('return_date')->latest();
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
