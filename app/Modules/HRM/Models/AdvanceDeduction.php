<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AdvanceDeduction extends Model
{
    protected $table = 'hrm_advance_deductions';

    protected $fillable = [
        'advance_id',
        'amount',
        'deduction_amount',
        'deduction_date',
        'payroll_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deduction_date' => 'date',
    ];

    public function advance()
    {
        return $this->belongsTo(EmployeeAdvance::class, 'advance_id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
