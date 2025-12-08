<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

use App\Modules\Main\Utilities\ActivityLogTrait;

class Otp extends Model
{
    use ActivityLogTrait;
}
