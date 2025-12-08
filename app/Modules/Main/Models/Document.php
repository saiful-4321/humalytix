<?php

namespace App\Modules\Main\Models;

use App\Models\User;
use App\Modules\Main\Models\Scopes\DocumentScope;
use App\Modules\Main\Utilities\ActivityLogTrait;
use App\Modules\Main\Utilities\ModelSignatureTrait;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use ModelSignatureTrait, ActivityLogTrait;

    protected $table = 'documents';
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new DocumentScope());
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id'); 
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by'); 
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by'); 
    }

}
