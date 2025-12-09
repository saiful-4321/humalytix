<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLoan extends Model
{
    use SoftDeletes;

    protected $table = 'hrm_employee_loans';

    protected $fillable = [
        'employee_id',
        'loan_type_id',
        'loan_number',
        'loan_amount',
        'interest_rate',
        'interest_type',
        'tenure_months',
        'monthly_installment',
        'total_payable',
        'total_paid',
        'outstanding_balance',
        'disbursement_date',
        'first_installment_date',
        'status',
        'purpose',
        'remarks',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'disbursement_date' => 'date',
        'first_installment_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            if (empty($loan->loan_number)) {
                $loan->loan_number = 'LOAN-' . date('Y') . '-' . str_pad(static::count() + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function installments()
    {
        return $this->hasMany(LoanInstallment::class, 'loan_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Calculate installments
    public function generateInstallments()
    {
        $installments = [];
        $dueDate = $this->first_installment_date;

        for ($i = 1; $i <= $this->tenure_months; $i++) {
            $principalAmount = $this->monthly_installment;
            $interestAmount = 0;

            if ($this->interest_rate > 0) {
                if ($this->interest_type === 'flat') {
                    $totalInterest = ($this->loan_amount * $this->interest_rate * $this->tenure_months) / 100;
                    $interestAmount = $totalInterest / $this->tenure_months;
                } else { // reducing
                    $balance = $this->loan_amount - ($this->monthly_installment * ($i - 1));
                    $interestAmount = ($balance * $this->interest_rate) / 100;
                }
            }

            $installments[] = [
                'loan_id' => $this->id,
                'installment_number' => $i,
                'principal_amount' => $principalAmount,
                'interest_amount' => $interestAmount,
                'installment_amount' => $principalAmount + $interestAmount,
                'due_date' => $dueDate,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $dueDate = $dueDate->copy()->addMonth();
        }

        LoanInstallment::insert($installments);
    }
}
