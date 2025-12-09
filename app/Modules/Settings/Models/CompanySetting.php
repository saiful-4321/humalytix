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
        'logo_white_height',
        'logo_white_width',
        'logo_dark',
        'logo_dark_height',
        'logo_dark_width',
        'logo_white_small',
        'logo_white_small_height',
        'logo_white_small_width',
        'logo_dark_small',
        'logo_dark_small_height',
        'logo_dark_small_width',
        'favicon',
        'registration_active',
        'password_reset_active',
    ];
}
