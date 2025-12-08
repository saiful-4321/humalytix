<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect("login");
});


Route::get('/test', function () {
    // $y = App\Modules\Main\Jobs\SendSmsJob::dispatch([
    //     "msisdn"   => "8801821742285",
    //     "sms_body" => "Your login One-Time Password (OTP) is 111222. Please keep it confidential!",
    // ]);

    // dd($y);

    $x = App\Modules\Main\Jobs\SendEmailJob::dispatch([
        "to"       => "shohrab@quantbd.com",
        "subject"  => config('common.cms.short_title') . ' - Your Login OTP',
        "message"  => "Dear Client,<br/><br/>Your One-Time Password (OTP) for logging into <b>" . config('common.cms.short_title')  . "</b> is: <b>111222</b><br/>Please use this OTP to complete your login process. Note that this OTP is valid for a single use and expires after a short period of time.<br/><br/>If you did not request this OTP, please ignore this email.<br/><br/><br/>Thank you"
    ]);
    dd("e", $x);
});

