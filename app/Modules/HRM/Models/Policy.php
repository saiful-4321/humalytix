<?php

namespace App\Modules\HRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_policies';

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'version',
        'effective_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
