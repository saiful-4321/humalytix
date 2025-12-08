<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Settings\Http\Controllers\CompanySettingController;

Route::group([
    'module'     => 'Settings',
    'prefix'     => 'dashboard/settings',
    'middleware' => ['web', 'auth'],
    'as'         => 'dashboard.settings.'
], function() {
    Route::get('company', [CompanySettingController::class, 'index'])->name('company.index');
    Route::post('company', [CompanySettingController::class, 'update'])->name('company.update');

    Route::get('theme', [\App\Modules\Settings\Http\Controllers\ThemeSettingController::class, 'index'])->name('theme.index');
    Route::post('theme', [\App\Modules\Settings\Http\Controllers\ThemeSettingController::class, 'update'])->name('theme.update');
    Route::post('theme/reset', [\App\Modules\Settings\Http\Controllers\ThemeSettingController::class, 'reset'])->name('theme.reset');

    Route::get('schedule', [\App\Modules\Settings\Http\Controllers\ScheduleSettingController::class, 'index'])->name('schedule.index');
    Route::put('schedule', [\App\Modules\Settings\Http\Controllers\ScheduleSettingController::class, 'update'])->name('schedule.update');
});
