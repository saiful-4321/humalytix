<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class BreakLog extends Model
{
    protected $table = 'hrm_breaks';

    protected $fillable = [
        'attendance_log_id',
        'break_start',
        'break_end',
        'duration_minutes',
        'break_type',
        'notes',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
    ];

    // Relationships
    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class);
    }

    // Methods
    public function calculateDuration()
    {
        if ($this->break_start && $this->break_end) {
            $start = \Carbon\Carbon::parse($this->break_start);
            $end = \Carbon\Carbon::parse($this->break_end);
            $this->duration_minutes = $end->diffInMinutes($start);
            $this->save();
        }
    }
}
