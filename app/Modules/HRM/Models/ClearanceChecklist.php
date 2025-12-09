<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClearanceChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_clearance_checklists';

    protected $fillable = [
        'resignation_id',
        'employee_id',
        'department_name', // IT, Admin, HR, Finance
        'checked_by',
        'status', // pending, cleared, uncleared
        'remarks',
        'cleared_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cleared_at' => 'datetime',
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

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'checked_by');
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
