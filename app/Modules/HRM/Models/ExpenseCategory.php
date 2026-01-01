<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_expense_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Modules\Finance\Models\ChartOfAccount::class, 'account_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
