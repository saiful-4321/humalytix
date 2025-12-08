<?php

namespace App\Modules\Main\Models;

use Spatie\Permission\Models\Role as SpatieRole;

use App\Modules\Main\Utilities\ActivityLogTrait;

class Role extends SpatieRole
{
    use ActivityLogTrait;
}
