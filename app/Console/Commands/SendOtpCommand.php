<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Auth\Models\Otp;
use App\Modules\Main\Enums\OtpStatusEnum;
use App\Modules\Main\Jobs\SendEmailJob;
use App\Modules\Main\Jobs\SendSmsJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendOtpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:otp-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $limit = 500;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->_log("SendOtpCommand@handle - START: OTP command", "info");
 
        $otps = Otp::where("status", OtpStatusEnum::Read) // load all read data
            ->where("expired_time", '>', Carbon::now()->subMinutes(config('common.otp.expired_in_minutes')))
            ->limit($this->limit)
            ->get();  
    
        if (!$otps) {
            $this->_log("SendOtpCommand@handle - END: no OTP found!"); 
            return;
        }

        $this->_log("SendOtpCommand@handle - PROCESS: total " . $otps->count() . " found !"); 

        foreach($otps as $otp) {
            if (strtoupper($otp->type) == "SMS") {

                SendSmsJob::dispatch([
                    "msisdn"   => $otp->to,
                    "sms_body" => "Your login One-Time Password (OTP) is {$otp->otp}. Please keep it confidential!",
                ]);

                $otp->status = OtpStatusEnum::Sent;
                $otp->save();
                // $this->_log("SendOtpCommand@handle - PROCESS: OTP {$otp->type} sent to {$otp->to}", "debug");

            } else if (strtoupper($otp->type) == "EMAIL") {

                SendEmailJob::dispatch([
                    "to"      => $otp->to,
                    "subject" => config('common.cms.short_title') . " - One-Time Password (OTP)",
                    "message" => "Dear Concern,\r\nYour login One-Time Password (OTP) is {$otp->otp}.\r\nPlease keep it confidential!",
                ]);

                $otp->status = OtpStatusEnum::Sent;
                $otp->save();
                // $this->_log("SendOtpCommand@handle - PROCESS: OTP {$otp->type} sent to {$otp->to}", "debug");

            } else {
                $otp->status = OtpStatusEnum::Failed;
                $otp->save();

                $this->_log("SendOtpCommand@handle - END: Unknown Type {$otp->type} found for OTP id: {$otp->id}");
            }
        }

        $this->_log('SendOtpCommand@handle - END: OTP command');
    }

    private function _log(string $log, $type = 'info')
    { 
        $this->info($log);
        Log::$type($log);
    }
}
