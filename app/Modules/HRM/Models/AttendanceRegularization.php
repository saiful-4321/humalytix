<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRegularization extends Model
{
    use HasFactory;

    protected $table = 'hrm_attendance_regularizations';

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'reason',
        'status',
        'approved_by',
        'remarks'
    ];

    protected $casts = [
        'date' => 'date',
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
