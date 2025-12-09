<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_attendance_logs';

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_time',
        'check_out_time',
        'check_in_method',
        'check_in_location',
        'check_in_ip',
        'check_in_photo',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_method',
        'check_out_location',
        'check_out_ip',
        'check_out_photo',
        'check_out_latitude',
        'check_out_longitude',
        'shift_id',
        'total_hours',
        'break_hours',
        'working_hours',
        'status',
        'remarks',
        'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'total_hours' => 'decimal:2',
        'break_hours' => 'decimal:2',
        'working_hours' => 'decimal:2',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakLog::class, 'attendance_log_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // Scopes
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
            ->whereMonth('date', $month);
    }

    public function scopePresent($query)
    {
        return $query->whereIn('status', ['present', 'late']);
    }

    // Methods
    public function calculateHours()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $this->total_hours = calculate_working_hours(
                $this->check_in_time,
                $this->check_out_time,
                0
            );
            $this->working_hours = $this->total_hours - ($this->break_hours ?? 0);
            $this->save();
        }
    }
}
