<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Models\Otp;
use App\Modules\Main\Enums\OtpStatusEnum;
use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Jobs\SendEmailJob;
use App\Modules\Main\Jobs\SendSmsJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthOtpService
{
    public function otp($request, $isLoginOtp = true)
    {
        $existsUser = User::where('email', $request->email)
            ->orWhere("remember_token", $request->remember_token)
            ->first();
        if ($isLoginOtp && ($existsUser && $existsUser->status !== UserStatusEnum::Pending)) {
            return [
                "status"      => false,
                "message"     => "The user has already been OTP verified!",
                "expired_time" => Carbon::now()->format("Y-m-d H:i:s"),
                "mobile"       => msisdn($request->mobile),
                "email"        => $request->email
            ];
        }

        $count = Otp::where(function ($query) use ($request) {
                $query->whereIn("to", msisdns($request->mobile))
                    ->orWhere("to", $request->email);
            })
            ->where("status", OtpStatusEnum::Sent)
            ->whereDate("created_at", "=", Carbon::now()->format("Y-m-d"))
            ->count();

        if ($count >= config("common.otp.maximum_per_user")) {
            return [
                "status"      => false,
                "message"     => "Sorry, you have reached the maximum number of OTP requests for today.\r\nWe have already sent a total of {$count} OTP(s) to your account.\r\nFor security purposes, we limit the number of OTP requests to ensure the safety of your account.\r\nPlease try again tomorrow or contact our support team if you require immediate assistance.\r\nThank you for your understanding.",
                "expired_time" => Carbon::now()->format("Y-m-d H:i:s"),
                "mobile"       => msisdn($request->mobile),
                "email"        => $request->email
            ];
        }

        $services = config("common.otp.service_enable");
        $exists = Otp::where(function ($query) use ($request) {
                $query->whereIn("to", msisdns($request->mobile))
                    ->orWhere("to", $request->email);
            })
            ->whereIn("status", [OtpStatusEnum::Pending, OtpStatusEnum::Read])
            ->where("expired_time", ">=", Carbon::now()->format("Y-m-d H:i:s"))
            ->first();
         
        if ($exists) {
            return [
                "status"       => true,
                "message"      => "An One-Time Password (OTP) already sent to you via " . implode(" and ", $services),
                "expired_time" => $exists->expired_time,
                "mobile"       => msisdn($request->mobile),
                "email"        => $request->email
            ];
        }

        // COMMON VARIABLES
        $expiredAt     = Carbon::now()->addMinutes(config("common.otp.expired_in_minutes"))->format("Y-m-d H:i:s");
        $otp           = mt_rand(111111, 999999);
        
        // insert into otps table
        $otpData = new Otp;
        $otpData->type         = "SMS";
        $otpData->to           = msisdn($request->mobile);
        $otpData->otp          = $otp;
        $otpData->status       = OtpStatusEnum::Pending;
        $otpData->expired_time = $expiredAt;
        $otpData->created_at   = Carbon::now()->format("Y-m-d H:i:s");
        $otpData->ip_address   = $request->ip() ?? null;
        $otpData->save();

        Log::debug("AuthOtpService@otp - We just sent you a OTP ($otp) via {$request->mobile} and {$request->email}");

        // send sms
        if (in_array("SMS", $services)) {
            SendSmsJob::dispatch([
                "msisdn"   => msisdn($request->mobile),
                "sms_body" => "The Verification One-Time Password (OTP) is {$otp}. Please keep it confidential!",
            ]);
        }

        // send mail
        if (in_array("EMAIL", $services)) {
            SendEmailJob::dispatch([
                "to"       => $request->email,
                "subject"  => config('common.cms.short_title') . ' - Your Verification OTP',
                "message"  => "Dear Client,<br/><br/>Your One-Time Password (OTP) for logging into <b>" . config('common.cms.short_title')  . "</b> is: <b>{$otpData->otp}</b><br/>Please use this OTP to complete your login process. Note that this OTP is valid for a single use and expires after a short period of time.<br/><br/>If you did not request this OTP, please ignore this email.<br/><br/><br/>Thank you", 
            ]);
        }

        // return response time
        return [
            "status"       => true,
            "vtoken"       => $request->input("vtoken"),
            "message"      => "We just sent you a secure One-Time Password (OTP) via SMS {$request->mobile} and Email {$request->email}",
            "expired_time" => $expiredAt,
            "mobile"       => msisdn($request->mobile),
            "email"        => $request->email
        ];
    }

    public function getUserByRememberToken($token)
    {
        return User::where("remember_token", $token)->first();
    }

    public function checkValidOtp($data = [])
    {
        $expiredAt = Carbon::now()->format("Y-m-d H:i:s");
        $query = Otp::where(function ($query) use ($data) {
                $query->whereIn("to", msisdns($data['mobile']))
                    ->orWhere("to", $data['email']);
            })
            ->where("otp", $data["otp"])
            ->whereIn("status", [OtpStatusEnum::Pending, OtpStatusEnum::Read])
            ->where("expired_time", ">=", $expiredAt);

        $exists = $query->exists();
        
        if (!$exists) {
            return false;
        }

        $query->update([
            'status' => OtpStatusEnum::Sent
        ]);

        return true;
    }

    public function getUserId($id = null, $prefix = 'UFT', $lenth = 6)
    {
        $user = User::when(!empty($id), function($query) use($id) {
                $query->where("id", $id);
            })
            ->orderByDesc("id")
            ->first();

        $sl = ($user->id ?? 1);
        return $prefix . str_pad($sl, $lenth, '0', STR_PAD_LEFT);
    }

    public function checkLastOtp($mobile, $otp)
    {
        return Otp::whereIn("to", msisdns($mobile))
            ->where('otp', $otp)
            ->where("expired_time", ">=", Carbon::now()->subMinutes(30)->format("Y-m-d H:i:s"))
            ->whereNotIn("status", [OtpStatusEnum::Pending, OtpStatusEnum::Read, OtpStatusEnum::Failed])
            ->exists();
    }

}