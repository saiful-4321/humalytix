<?php

namespace App\Modules\Main\Models;

use App\Modules\Main\Utilities\ActivityLogTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{ 
    use ActivityLogTrait; 
    protected $fillable = ["*"];
    protected $table = 'user_session'; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function pretend_user()
    {
        return $this->belongsTo(User::class,'pretend_user_id');
    }
}
