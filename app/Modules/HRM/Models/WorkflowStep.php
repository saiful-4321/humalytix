<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $table = 'hrm_workflow_steps';

    protected $fillable = [
        'workflow_id',
        'step_order',
        'step_name',
        'approver_type',
        'approver_id',
        'conditions',
        'auto_action',
    ];

    protected $casts = [
        'conditions' => 'array',
        'step_order' => 'integer',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}
