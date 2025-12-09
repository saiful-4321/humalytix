<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalChainLevel extends Model
{
    use HasFactory;

    protected $table = 'hrm_approval_chain_levels';

    protected $fillable = ['approval_chain_id', 'level', 'approver_type', 'approver_value'];

    public function chain()
    {
        return $this->belongsTo(ApprovalChain::class, 'approval_chain_id');
    }
}
