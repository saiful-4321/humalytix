<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowRun extends Model
{
    use HasFactory;

    protected $table = 'hrm_workflow_runs';

    protected $fillable = [
        'workflow_id',
        'workflowable_type',
        'workflowable_id',
        'current_step_id',
        'status',
        'started_at',
        'completed_at',
        'step_history',
        'notes',
    ];

    protected $casts = [
        'step_history' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the workflow this run belongs to
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the current step
     */
    public function currentStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    /**
     * Get the workflowable model (Leave, Expense, etc.)
     */
    public function workflowable()
    {
        return $this->morphTo();
    }

    /**
     * Add a step to the history
     */
    public function addStepToHistory($stepId, $action, $userId, $notes = null)
    {
        $history = $this->step_history ?? [];
        
        $history[] = [
            'step_id' => $stepId,
            'action' => $action, // 'approved', 'rejected', 'skipped'
            'user_id' => $userId,
            'notes' => $notes,
            'timestamp' => now()->toDateTimeString(),
        ];

        $this->update(['step_history' => $history]);
    }

    /**
     * Mark workflow as completed
     */
    public function complete($status = 'approved')
    {
        $this->update([
            'status' => $status,
            'completed_at' => now(),
        ]);
    }
}
