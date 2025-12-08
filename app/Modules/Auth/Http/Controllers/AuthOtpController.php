<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\AuthOtpRequest;
use App\Modules\Auth\Services\AuthOtpService;
use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Enums\UserTokenTypeEnum;
use App\Modules\Main\Jobs\CredentialSendMailJob;
use App\Modules\Main\Services\EkycProgressbarService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthOtpController extends Controller
{

    private $view = "Auth::pages.auth.";
    private $authOtpService;

    public function __construct(AuthOtpService $authOtpService)
    {
        $this->authOtpService = $authOtpService;
    }


    public function otp(Request $request)
    {
        try {
  
            if (Auth::check()) {
                return redirect()->route("dashboard.home");
            }

            $otp  = $this->authOtpService->otp($request);
            if (!$otp["status"]) {
                return redirect()->route("register")->withInput()->with("error", $otp["message"]);
            } 
            
            $pageName = "OTP Verification"; 
            return view($this->view . "otp", compact(
                "pageName",
                "otp"
            ));

        } catch (Exception $e) {
            Log::error("AuthOtpController@otp - Error - " . $e->getMessage() . ", Line - " . $e->getLine());
            return redirect()->back()->withInput()->with("error", "Someting went wrong!");
        } 
    }

    public function auth(AuthOtpRequest $request)
    {
        try {   

            if (Auth::check()) {
                return redirect()->route("dashboard.home");
            }
            $isValid = $this->authOtpService->checkValidOtp([
                "mobile" => msisdn($request->mobile),
                "email"  => $request->email,
                "otp"    => implode($request->otp)
            ]); 
  
            if (!$isValid) {
                return redirect()->back()->withInput()->with("error", "Invalid OTP!"); 
            }
 
            // get user info
            $user = $this->authOtpService->getUserByRememberToken($request->remember_token);

            if (!$user) {
                return redirect()->back()->withInput()->with("error", "Verification failed! please try again later.");
            }

            // send email & password via mail 
            $email    = $user->email;
            $password = getRandomString(8);

            $user->email    = $email;
            $user->password = Hash::make($password);
            $user->status   = UserStatusEnum::Verified;
            $user->api_token = getApiToken();
            $user->save();
            $user->createToken(UserTokenTypeEnum::API);

            // dispatch mail
            CredentialSendMailJob::dispatch($user, [
                "email"       => $email, 
                "password"    => $password
            ]);

            // save form progress
            EkycProgressbarService::save($user->id, 'otp_verification');

            Log::info("AuthOtpController@auth - login credentials sent to your both email & mobile: " .($user->mobile ?? null) . " ip: ". $request->ip());

            if (config('app.debug')) {
                Log::debug("New User Credentials: Email: {$email} and Password: {$password}");
            }
            
            return redirect()->route("otp.success")->with("success", "Dear {$user->name},\r\nYour account has been successfully created.\r\nPlease check your email for details.\r\nPlease reset your password on first login.\r\nKeep them confidential.");
         

        } catch(Exception $e) {
            Log::error("AuthOtpController@auth - Error - " . $e->getMessage() . ", Line - " . $e->getLine());
            return redirect()->back()->withInput()->with("error", "Someting went wrong!");
        } 
    }

    public function success(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route("dashboard.home");
        }

        $pageName = "Congratulations!"; 
        return view($this->view . "otp-success", compact(
            "pageName"
        ));
        
    }
 
}
