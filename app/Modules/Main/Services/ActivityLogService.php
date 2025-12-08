<?php

namespace App\Modules\Main\Services;

use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class ActivityLogService
{
    public function getAll($request, $paginate = 20)
    {
        $query = Activity::where(function($query) use ($request) {
            if ($request->filled("log_name")) {
                $query->where("log_name", $request->log_name);
            }
            if ($request->filled("description")) {
                $query->where("description", "like", "%{$request->description}%");
            } 
            if ($request->filled("subject_type")) {
                $query->where("subject_type", $request->subject_type);
            }
            if ($request->filled("subject_id")) {
                $query->where("subject_id", $request->subject_id);
            }
            if ($request->filled("causer_id")) {
                $query->where("causer_id", $request->causer_id);
            }
 
            if ($request->filled("date_from")) {
                $query->whereDate("created_at", '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
            }
            if ($request->filled("to_date")) {
                $query->whereDate("created_at", '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
            } 
        })
        ->orderBy("id", "desc");

        if ($paginate == false) {
            return $query->get();
        }

        return $query->paginate($request->pagination ?? $paginate);
    }

}