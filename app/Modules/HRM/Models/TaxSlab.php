<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSlab extends Model
{
    protected $table = 'hrm_tax_slabs';

    protected $fillable = [
        'name',
        'min_income',
        'max_income',
        'tax_rate',
        'deduction_amount',
        'gender',
        'is_active',
    ];
}
