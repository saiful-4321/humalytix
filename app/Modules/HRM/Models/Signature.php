<?php

namespace App\Modules\HRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $table = 'hrm_signatures';

    protected $fillable = [
        'signable_type',
        'signable_id',
        'user_id',
        'signature_image',
        'ip_address',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function signable()
    {
        return $this->morphTo();
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
