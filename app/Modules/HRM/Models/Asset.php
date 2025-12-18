<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\HRM\Models\AssetCategory;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_assets';

    protected $fillable = [
        'asset_category_id', // Replaces type
        'name',
        'code',
        'serial_number',
        'purchase_date',
        'purchase_cost',
        'salvage_value', // New
        'condition', // new, good, fair, poor
        'status', // available, assigned, maintenance, lost, retired
        'location', // New
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

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

    // Accessors
    public function getCurrentValueAttribute()
    {
        if (!$this->purchase_date || !$this->purchase_cost || !$this->category) {
            return $this->purchase_cost;
        }

        $ageInYears = $this->purchase_date->diffInDays(now()) / 365;
        $salvageValue = $this->salvage_value ?? 0;
        $cost = $this->purchase_cost;
        
        $annualDepreciation = 0;

        if ($this->category->depreciation_rate > 0) {
            // Rate based (Straight Line % on Cost)
            // Or usually it implies Declining Balance if it's just a rate? 
            // Let's assume Straight Line % on (Cost - Salvage) for simplicity if implied, 
            // standard straight line is 1/Life. 
            // If rate is provided, let's use: (Cost - Salvage) * (Rate / 100)
            $annualDepreciation = ($cost - $salvageValue) * ($this->category->depreciation_rate / 100);
        } elseif ($this->category->useful_life_years > 0) {
            // Life based Straight Line
            $annualDepreciation = ($cost - $salvageValue) / $this->category->useful_life_years;
        }

        $totalDepreciation = $annualDepreciation * $ageInYears;
        $currentValue = $cost - $totalDepreciation;

        return max($currentValue, $salvageValue);
    }
}
