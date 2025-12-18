<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingParticipant extends Model
{
    use HasFactory;

    protected $table = 'hrm_training_participants';

    protected $fillable = [
        'training_session_id',
        'employee_id',
        'status', // enrolled, attended, completed, no_show, failed
        'completion_date',
        'feedback',
        'score',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'score' => 'integer',
    ];

    // Relationships
    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
