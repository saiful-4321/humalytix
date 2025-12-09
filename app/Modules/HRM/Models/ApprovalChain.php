<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalChain extends Model
{
    use HasFactory;

    protected $table = 'hrm_approval_chains';

    protected $fillable = ['name', 'description', 'is_default', 'created_by'];

    public function levels()
    {
        return $this->hasMany(ApprovalChainLevel::class)->orderBy('level');
    }
}
