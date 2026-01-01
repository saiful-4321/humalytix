<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $table = 'finance_account_types';

    protected $fillable = [
        'name',
        'code_prefix',
        'normal_balance',
    ];

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'type_id');
    }
}
