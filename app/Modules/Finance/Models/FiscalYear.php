<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'finance_fiscal_years';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'is_current',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    // Helper to get current fiscal year
    public static function current()
    {
        return self::where('is_current', true)->first();
    }
}
