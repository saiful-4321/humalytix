<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseClaim extends Model
{
    use HasFactory;

    protected $table = 'hrm_expense_claims';

    protected $fillable = [
        'employee_id',
        'date',
        'title',
        'category',
        'amount',
        'description',
        'attachment',
        'status',
        'approved_by',
        'remarks'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
