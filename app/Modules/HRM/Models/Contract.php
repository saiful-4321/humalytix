<?php

namespace App\Modules\HRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_contracts';

    protected $fillable = [
        'employee_id',
        'title',
        'file_path',
        'start_date',
        'end_date',
        'type',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function signatures()
    {
        return $this->morphMany(Signature::class, 'signable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
