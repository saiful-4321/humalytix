<?php

namespace App\Modules\Main\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

use App\Modules\Main\Utilities\ActivityLogTrait;

class Permission extends SpatiePermission
{
    use ActivityLogTrait;

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
