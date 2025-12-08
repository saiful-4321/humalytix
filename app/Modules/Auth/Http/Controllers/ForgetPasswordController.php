<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Services\AuthOtpService;
use App\Modules\Main\Enums\UserStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Modules\Settings\Services\CompanyService;

class ForgetPasswordController extends Controller
{
    private $view = "Auth::pages.forget-password.";
    private $authOtpService;
    private $companyService;

    public function __construct(AuthOtpService $authOtpService, CompanyService $companyService)
    {
        $this->authOtpService = $authOtpService;
        $this->companyService = $companyService;
    }

    // forgot password - get email
    public function form(Request $request)
    {
        $company = $this->companyService->get();
        if (!($company['password_reset_active'] ?? true)) {
            return redirect()->route('login')->with('error', 'Password reset is currently disabled.');
        }

        return view($this->view . "form", [
            "pageName" => "Forgot Password?",
        ]);
    }

    // forgot password - send & get otp
    public function otp(Request $request)
    {
        $company = $this->companyService->get();
        if (!($company['password_reset_active'] ?? true)) {
            return redirect()->route('login')->with('error', 'Password reset is currently disabled.');
        }

        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        Log::notice("ForgetPasswordController@otp - Request: " . json_encode($request->all()));

        $user = User::where("email", $request->email)->first();
        if (!$user) {
            Log::warning("ForgetPasswordController@otp - User not found - Request: " . json_encode($request->all()));
            return back()->withInput()->with("error", "User not found!");
        }
        if ($user->hasRole("User")) {
            Log::warning("ForgetPasswordController@otp - User is not authorized - Request: " . json_encode($request->all()));
            return back()->withInput()->with("error", "User is not authorized!");
        }

        if ($user->status == UserStatusEnum::Pending) {
            Log::warning("ForgetPasswordController@otp - User is {$user->status} - Request: " . json_encode($request->all()));
            return back()->withInput()->with("error", "Please contact with author!");
        }

        $request['email'] = $user->email;
        $request['mobile'] = $user->mobile;
        $request['remember_token'] = $user->remember_token;
        $otp  = $this->authOtpService->otp($request, false);
        if (!$otp["status"]) {
            Log::warning("ForgetPasswordController@otp - {$otp['message']} - Request: " . json_encode($request->all()));
            return  back()->withInput()->with("error", $otp["message"]);
        }

        return view($this->view . "otp", [
            "pageName" => "Forgot Password",
            "subTitle" => "Verify OTP to Reset Password.",
            "otp" => $otp
        ]);
    }

    // forgot password - verify otp & get new password
    public function verify(Request $request)
    {
        $company = $this->companyService->get();
        if (!($company['password_reset_active'] ?? true)) {
            return redirect()->route('login')->with('error', 'Password reset is currently disabled.');
        }

        Log::notice("ForgetPasswordController@verify - Request: " . json_encode($request->all()));

        $isValid = $this->authOtpService->checkValidOtp([
            "mobile" => msisdn($request->mobile),
            "email"  => $request->email,
            "otp"    => implode($request->otp)
        ]);

        if (!$isValid) {
            Log::warning("ForgetPasswordController@verify - Invalid OTP! - Request: " . json_encode($request->all()));
            return redirect()->back()->withInput()->with("error", "Invalid OTP!");
        }
        
        // get user info
        $user = $this->authOtpService->getUserByRememberToken($request->remember_token);
        if (!$user) {
            Log::warning("ForgetPasswordController@verify - Verification failed - Request: " . json_encode($request->all()));
            return redirect()->back()->withInput()->with("error", "Verification failed! please try again later.");
        }
        if ($user->hasRole("User")) {
            Log::warning("ForgetPasswordController@verify - User is not authorized - Request: " . json_encode($request->all()));
            return back()->withInput()->with("error", "User is not authorized!");
        }

        return redirect()->route('forgot-password.reset', [
            'remember_token' => $request->remember_token,
            'mobile'         => $request->mobile,
            'email'          => $request->email,
            'otp'            => implode($request->otp),
        ]);

    }

    // forgot password - reset password
    public function reset(Request $request)
    {
        $company = $this->companyService->get();
        if (!($company['password_reset_active'] ?? true)) {
            return redirect()->route('login')->with('error', 'Password reset is currently disabled.');
        }
        // get user info
        $user = $this->authOtpService->getUserByRememberToken($request->remember_token);
        if (!$user) {
            Log::warning("ForgetPasswordController@reset - Invalid User - Request: " . json_encode($request->except('_token', 'password', 'password_confirmation')));
            return redirect()->route('forgot-password')->withInput()->with("error", "Invalid User! please try again.");
        }
        if ($user->hasRole("User")) {
            Log::warning("ForgetPasswordController@reset - User is not authorized - Request: " . json_encode($request->all()));
            return back()->withInput()->with("error", "User is not authorized!");
        }

        // check otp
        $otp = $this->authOtpService->checkLastOtp($request->mobile, $request->otp);
        if (!$otp) {
            Log::warning("ForgetPasswordController@reset - Invalid OTP - Request: " . json_encode($request->except('_token', 'password', 'password_confirmation')));
            return redirect()->route('forgot-password')->withInput()->with("error", "Invalid OTP! please try again.");
        }

        if ($request->method() == 'POST') {
            $request->validate([
                'password'    => 'required|string|confirmed|min:5',
                'password_confirmation' => 'required|string|min:5',
            ]);

            $user->password = Hash::make($request->password);
            $user->remember_token = getApiToken();
            if ($user->save()) {
                Log::notice("ForgetPasswordController@reset - Password has been reset successfully - Request: " . json_encode($request->except('_token', 'password', 'password_confirmation')));
                return redirect()->route('login')->withInput()->with("success", "Password has been reset successfully!");
            }
        }
        
        return view($this->view . "reset", [
            "pageName"       => "Reset Password"
        ]);
    }
}
