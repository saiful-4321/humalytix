<?php

namespace App\Modules\Main\Services;

use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Enums\EkycProgressbarStatusEnum;
use App\Modules\Main\Models\EkycProgressbar;
use Carbon\Carbon;

/**
 * HOW TO USE
 * Initial Use
 * -> EkycProgressbarService::save($user->id);
 * Update a Step of the Form 
 * -> EkycProgressbarService::save($user->id, 'field_name');
 * Complete all steps
 * -> EkycProgressbarService::save($user->id, '', 'Completed');
 */

class EkycProgressbarService
{
    public static function save($userId, $field = "", $status = "Pending")
    {
        $bar = EkycProgressbar::where('user_id', $userId)->firstOrNew();
        
        $bar->user_id = $userId;
        $bar->basic_registration = ActiveInactiveEnum::Active;
        $bar->status = (!empty($status) ? $status : EkycProgressbarStatusEnum::Pending);

        if (!empty($field) && !$bar->{$field}) {
            $bar->{$field} = ActiveInactiveEnum::Active;
        }
 
        $progressStatus = self::status($userId);
        $bar->status = $progressStatus->status ?? $status;

        // Check if the record already exists
        if ($bar->exists) {
            // Update the record using query builder
            $update = $bar->toArray();
            $update["created_at"] = Carbon::parse($bar->created_at)->format("Y-m-d H:i:s");
            $update["updated_at"] = Carbon::now()->format("Y-m-d H:i:s");
            $affected = EkycProgressbar::where('user_id', $userId)->update($update);
        } else {
            // Save the new record
            $affected = $bar->save();
        }

        return $affected !== false; // Return true if update or save was successful
    }

    public static function status($userId)
    {
        $item = EkycProgressbar::where("user_id", $userId)->first();

        $result = [
            "fields" => [
                "basic_registration"       => 1,
                // "otp_verification"         => 1,
                "bo_authorize_information" => 1,
                "bo_account_holder"        => $item->bo_account_holder ?? 0,
                "bo_bank_information"      => $item->bo_bank_information ?? 0,
                "bo_nominee_information"   => $item->bo_nominee_information ?? 0,
                "bo_documents"             => $item->bo_documents ?? 0
            ],
            "total"     => 0,
            "filled"    => 0,
            "progress"  => 0,
            "step"      => 0,
            "next"      => null,
        ];
        
        $next = "bo_documents";
        foreach ($result['fields'] as $field => $value) {
            if ($value === 0) {
                $next = $field;
                break;
            }
        }

        $nextField = "";
        $nextStep  = 0;
        if (!empty($next)) {
            $nextField = ucwords(trim(str_replace(["_", "-"], " ", str_replace(["bo"], "", $next))));
            // for OTP name
            $nextField = str_replace('Otp ', 'OTP ', $nextField);

            $nextStep  = array_search($next, array_keys($result['fields']));
            // ignore two steps
            if ($nextStep <= 2) {
                $nextStep = 0;
            } else {
                $nextStep = $nextStep - 2;
            }
        }

        $total  = count($result['fields']);
        $filled = array_sum(array_values($result['fields']));
        $result["total"]  = $total;
        $result["filled"] = $filled;
        $result["next"]   = (($total == $filled) ? "Completed" : "Add {$nextField}"); 
        $result["status"] = (($total == $filled) ? EkycProgressbarStatusEnum::Completed : (($filled <= 2) ? EkycProgressbarStatusEnum::Pending : EkycProgressbarStatusEnum::Inprogress)); 
        $result["step"]   = $nextStep;
        
        if ($result["total"] > 0) {
            $filledPercentage = ($result['filled'] / $result['total']) * 100;
            $result['progress'] = round($filledPercentage) ;
        }

        return (object) $result;
    }
}
