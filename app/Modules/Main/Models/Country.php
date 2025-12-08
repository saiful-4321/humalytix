<?php

namespace App\Modules\Main\Models;

use App\Modules\Main\Utilities\ActivityLogTrait;
use App\Modules\Main\Utilities\ModelSignatureTrait;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use ModelSignatureTrait, ActivityLogTrait;

    protected $table = 'countries';
    protected $primaryKey = 'id'; // If 'id' is not the primary key

    // Get a list of active countries with ID as key and name as value
    public static function activeList()
    {
        return self::where('status', 'Active')->pluck('name', 'id')->toArray();
    }

    // Get a list of active countries with name as key and name as value
    public static function nameOnlyList()
    {
        return self::where('status', 'Active')->pluck('name', 'name')->toArray();
    }

    // Get ISO code by country name
    public static function getIsoByName($name, $length = 3)
    {
        $isoField = 'iso' . $length;
        return self::where('status', 'Active')->where("name", $name)->value($isoField);
    }

    // Get country name by ISO code
    public static function getNameByIso($iso)
    {
        return self::where('status', 'Active')
            ->where(function($query) use ($iso) {
                $query->where('iso2', $iso)
                    ->orWhere('iso3', $iso);
            })
            ->value('name');
    }
 
    // Get a list of active countries with name as key and ISO code as value
    public static function isoList($length = 2)
    {
        $isoField = 'iso' . $length;
        return self::where('status', 'Active')->pluck('name', $isoField)->toArray();
    }

}

