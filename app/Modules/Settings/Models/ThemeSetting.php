<?php

namespace App\Modules\Settings\Models;

use App\Modules\Main\Utilities\ActivityLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    use HasFactory, ActivityLogTrait;

    protected $table = 'theme_settings';

    protected $fillable = [
        'layout_type',
        'layout_width',
        'layout_position',
        'topbar_color',
        'topbar_custom_color',
        'sidebar_color',
        'sidebar_custom_color',
        'sidebar_size',
        'footer_color',
        'footer_custom_color',
        'footer_enabled',
        'layout_mode',
        'body_color',
        'body_custom_color',
    ];
}
