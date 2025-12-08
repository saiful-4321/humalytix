<?php

namespace App\Modules\Settings\Models;

use App\Modules\Main\Utilities\ActivityLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory, ActivityLogTrait;

    protected $table = 'company_settings';

    protected $fillable = [
        'company_name',
        'short_name',
        'address',
        'email',
        'phone',
        'meta_title',
        'meta_desc',
        'meta_tags',
        'logo_white',
        'logo_dark',
        'logo_white_small',
        'logo_dark_small',
        'favicon',
        'registration_active',
        'password_reset_active',
    ];
}
