<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_asset_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'depreciation_rate',
        'useful_life_years',
        'depreciation_expense_account_id',
        'accumulated_depreciation_account_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'depreciation_rate' => 'decimal:2',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'asset_category_id');
    }

    public function depreciationAccount()
    {
        return $this->belongsTo(\App\Modules\Finance\Models\ChartOfAccount::class, 'depreciation_expense_account_id');
    }

    public function accumulatedAccount()
    {
        return $this->belongsTo(\App\Modules\Finance\Models\ChartOfAccount::class, 'accumulated_depreciation_account_id');
    }
}
