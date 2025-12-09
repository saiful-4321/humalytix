<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    protected $table = 'hrm_loan_installments';

    protected $fillable = [
        'loan_id',
        'installment_number',
        'principal_amount',
        'interest_amount',
        'installment_amount',
        'due_date',
        'paid_date',
        'paid_amount',
        'status',
        'payroll_id',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(EmployeeLoan::class, 'loan_id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'pending')
                  ->where('due_date', '<', now());
            });
    }
}
