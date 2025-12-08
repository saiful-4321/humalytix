<?php

namespace App\Modules\Main\Models;

use App\Modules\Main\Utilities\ActivityLogTrait;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use ActivityLogTrait;
    protected $table = 'permission_modules';
    protected $guarded = ['id'];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id', 'id');
    }

}
