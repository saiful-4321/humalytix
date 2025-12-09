<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Resignation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_resignations';

    protected $fillable = [
        'employee_id',
        'resignation_date',
        'notice_date',
        'last_working_day',
        'reason',
        'status', // pending, approved, rejected, withdrawn
        'handover_notes',
        'exit_interview_notes',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'resignation_date' => 'date',
        'notice_date' => 'date',
        'last_working_day' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function finalSettlement(): HasOne
    {
        return $this->hasOne(FinalSettlement::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
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
