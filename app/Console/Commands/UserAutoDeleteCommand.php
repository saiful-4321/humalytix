<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Enums\PaymentStatusEnum;
use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Services\UserService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UserAutoDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user-auto-delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $batchId = $now->format("YmdHis");

        $this->_log("UserAutoDeleteCommand@handle - START : {$batchId} - User auto delete command", "info");
        
        $users = User::selectRaw("id,name,status,payment_status,bo_status,updated_at")
            ->with(["ekyc:user_id,status,updated_at"])
            ->whereNotIn("payment_status", [PaymentStatusEnum::Processing, PaymentStatusEnum::Paid])
            ->where("bo_status", ActiveInactiveEnum::Inactive)
            ->get(); 

        $deleteUsers = [];
        foreach($users as $item) {
            $userUpdate = !empty($item->updated_at) ? Carbon::parse($item->updated_at) : null;
            $ekycUpdate = !empty($item->ekyc->updated_at) ? Carbon::parse($item->ekyc->updated_at) : null;
        
            $isUserUpdateRecent = $userUpdate ? $userUpdate->greaterThanOrEqualTo($now->subMonth()) : false;
            $isEkycUpdateRecent = $ekycUpdate ? $ekycUpdate->greaterThanOrEqualTo($now->subMonth()) : false;
        
            // Check if either $userUpdate or $ekycUpdate is within the last month
            if ($isUserUpdateRecent || $isEkycUpdateRecent) {
                $user = [
                    "user_id"        => $item->id,
                    "name"           => $item->name,
                    "status"         => $item->status,
                    "payment_status" => $item->payment_status,
                    "bo_status"      => $item->bo_status,
                    "updated_at"     => Carbon::parse($item->updated_at)->format("Y-m-d"),
                ];

                $delete = (new UserService)->getById($item->id);
                if ($delete) {
                    $deleteUsers[] = $user;
                }
            }
        
            // Reset $now to the current date as subMonth() mutates the Carbon instance
            $now = Carbon::now();
        }

        if (!$deleteUsers) {
            $this->_log("UserAutoDeleteCommand@handle - END : {$batchId} -  No eligible user found!"); 
            return;
        }
        $this->_log("UserAutoDeleteCommand@handle - END : {$batchId} - total " . count($users) . " users found and " .count($deleteUsers). " users deleted!  Users: " . json_encode($users)); 
    }

    private function _log(string $log, $type = 'info')
    { 
        $this->info($log);
        Log::$type($log);
    }
}
