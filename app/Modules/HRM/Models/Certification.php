<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_certifications';

    protected $fillable = [
        'employee_id',
        'name',
        'issuing_organization',
        'credential_id',
        'credential_url',
        'issue_date',
        'expiry_date',
        'does_never_expire',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'does_never_expire' => 'boolean',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now())
              ->orWhere('does_never_expire', true);
        });
    }
    
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('does_never_expire', false)
                     ->where('expiry_date', '>', now())
                     ->where('expiry_date', '<=', now()->addDays($days));
    }
}
