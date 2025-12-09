<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_employment_histories';

    protected $fillable = [
        'employee_id',
        'company_name',
        'designation',
        'start_date',
        'end_date',
        'responsibilities',
        'reason_for_leaving',
        'reference_name',
        'reference_contact',
        'reference_email',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Accessors
    public function getDurationAttribute(): string
    {
        $start = $this->start_date;
        $end = $this->end_date ?? now();

        $diff = $start->diff($end);
        $years = $diff->y;
        $months = $diff->m;

        if ($years > 0) {
            return "{$years} year" . ($years > 1 ? 's' : '') . 
                   ($months > 0 ? " {$months} month" . ($months > 1 ? 's' : '') : '');
        }

        return "{$months} month" . ($months > 1 ? 's' : '');
    }

    public function getDurationInYearsAttribute(): float
    {
        $start = $this->start_date;
        $end = $this->end_date ?? now();
        return round($start->diffInMonths($end) / 12, 1);
    }
}
