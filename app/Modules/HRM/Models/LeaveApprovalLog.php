<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApprovalLog extends Model
{
    use HasFactory;

    protected $table = 'hrm_leave_approval_logs';

    protected $fillable = ['leave_id', 'approver_id', 'level', 'status', 'comments'];

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id');
    }
}
