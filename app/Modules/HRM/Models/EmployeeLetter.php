<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_employee_letters';

    protected $fillable = [
        'employee_id',
        'template_id',
        'letter_number',
        'subject',
        'content',
        'issued_date',
        'issued_by',
        'status',
        'acknowledged_at',
        'file_path',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'acknowledged_at' => 'datetime',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(LetterTemplate::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'issued_by');
    }

    // Scopes
    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeAcknowledged($query)
    {
        return $query->where('status', 'acknowledged');
    }
}
