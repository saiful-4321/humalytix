<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'finance_journals';

    protected $fillable = [
        'journal_number',
        'date',
        'reference',
        'reference_type',
        'reference_id',
        'description',
        'fiscal_year_id',
        'type',
        'status',
        'created_by',
        'updated_by',
        'posted_at',
        'posted_by',
    ];

    protected $casts = [
        'date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function entries()
    {
        return $this->hasMany(JournalEntry::class, 'journal_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->journal_number)) {
                $lastJournal = self::latest()->first();
                $nextId = $lastJournal ? $lastJournal->id + 1 : 1;
                $model->journal_number = 'JRN-' . date('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
