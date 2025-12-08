<?php 

namespace App\Modules\Main\Utilities;

use App\Modules\Main\Enums\ActivityLogEnum;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait ActivityLogTrait
{
    use LogsActivity;

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $ignoreChangedAttributes = ['password', 'remember_token', 'email_verified_at', 'created_at', 'updated_at', 'deleted_at'];

    public function getActivitylogOptions() : LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs();
    }

    protected static function bootActivityLogTrait()
    {
        // parent::boot(); // Not needed in trait boot method

        // get user agent data
        $agent = [
            'ip' => request()->ip(),
            'user-agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ];
 
        // Update event
        static::updated(function ($model) use($agent) {
            $oldAttributes = $model->getOriginal();
            $newAttributes = $model->getAttributes();

            $changedData = [];

            foreach ($newAttributes as $key => $value) {
                if (isset($oldAttributes[$key]) && $oldAttributes[$key] !== $value && !in_array($key, self::$ignoreChangedAttributes)) {
                    $changedData[$key] = [
                        'old' => $oldAttributes[$key],
                        'new' => $value,
                    ];
                }
            }

            if (!empty($changedData)) { 
                $tableName = $model->getTable();
                activity()
                    ->useLog(ActivityLogEnum::UPDATED)
                    ->performedOn($model)
                    ->withProperties([
                        "agent" => $agent,
                        "data"  => $changedData
                    ])
                    ->log("A record has been modified in {$tableName} table!"); 
            }
        });

        // Created event
        static::created(function ($model) use($agent) { 
            $tableName = $model->getTable();
            $attributes = collect($model->getAttributes())->except(self::$ignoreChangedAttributes)->toArray();
            activity()
                ->useLog(ActivityLogEnum::CREATED)
                ->performedOn($model)
                ->withProperties([
                    "agent" => $agent,
                    "data"  => $attributes
                ])
                ->log("A record has been created in {$tableName} table!");
        });
         
        // Delete event
        static::deleted(function ($model) use($agent) {
            $tableName = $model->getTable();
            activity()
                ->useLog(ActivityLogEnum::DELETED)
                ->performedOn($model) 
                ->withProperties([
                    "agent" => $agent
                ])
                ->log("A record has been deleted in {$tableName} table!");
        });

    }
}