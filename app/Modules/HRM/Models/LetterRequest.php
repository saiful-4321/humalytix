<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LetterRequest extends Model
{
    use HasFactory;

    protected $table = 'hrm_letter_requests';

    protected $fillable = [
        'employee_id',
        'type',
        'reason',
        'status',
        'approved_by',
        'remarks',
        'letter_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function letter()
    {
        return $this->belongsTo(EmployeeLetter::class, 'letter_id');
    }
}
