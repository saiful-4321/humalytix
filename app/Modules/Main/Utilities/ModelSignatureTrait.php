<?php

namespace App\Modules\Main\Utilities;


use Illuminate\Support\Facades\Auth;

trait ModelSignatureTrait
{

    protected static function bootModelSignatureTrait()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id() ?? null;
        });
    
        static::updating(function ($model) {
            $model->updated_by = Auth::id() ?? null;
        });
    }
}
