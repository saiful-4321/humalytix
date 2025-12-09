<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalSettlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_final_settlements';

    protected $fillable = [
        'resignation_id',
        'employee_id',
        'settlement_date',
        'basic_salary_due',
        'leave_encashment',
        'gratuity',
        'other_allowances',
        'deductions', // asset damage, loan, etc
        'net_payable',
        'status', // pending, processed, paid
        'remarks',
        'processed_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'basic_salary_due' => 'decimal:2',
        'leave_encashment' => 'decimal:2',
        'gratuity' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_payable' => 'decimal:2',
    ];

    // Relationships
    public function resignation(): BelongsTo
    {
        return $this->belongsTo(Resignation::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
