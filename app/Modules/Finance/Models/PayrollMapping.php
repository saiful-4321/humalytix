<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollMapping extends Model
{
    use HasFactory;

    protected $table = 'finance_payroll_mappings';

    protected $fillable = [
        'component_name',
        'component_slug',
        'salary_component_id',
        'debit_account_id',
        'credit_account_id',
        'is_active',
    ];

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }
}
