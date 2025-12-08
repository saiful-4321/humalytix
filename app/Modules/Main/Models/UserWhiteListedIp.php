<?php

namespace App\Modules\Main\Models;

use App\Modules\Main\Utilities\ActivityLogTrait;
use Illuminate\Database\Eloquent\Model;

class UserWhiteListedIp extends Model
{
    use ActivityLogTrait;
    protected static $logAttributes = ['*'];
    public $timestamps = true;
    protected $table = 'user_white_listed_ips';
    protected $guarded = [];
}
