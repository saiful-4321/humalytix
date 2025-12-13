<?php

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkrKeyResult extends Model
{
    use HasFactory;

    protected $table = 'hrm_okr_key_results';

    protected $fillable = [
        'okr_id',
        'description',
        'measurement_unit',
        'target_value',
        'current_value',
        'progress',
        'weightage',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'progress' => 'integer',
        'weightage' => 'integer',
    ];

    // Relationships
    public function okr(): BelongsTo
    {
        return $this->belongsTo(Okr::class);
    }

    // Methods
    public function updateProgress()
    {
        if ($this->target_value > 0) {
            $progress = min(100, ($this->current_value / $this->target_value) * 100);
            $this->update(['progress' => round($progress)]);
            
            // Update parent OKR progress
            $this->okr->updateProgress();
        }
    }
}
