<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Finance\Http\Controllers\DashboardController;
use App\Modules\Finance\Http\Controllers\ChartOfAccountController;
use App\Modules\Finance\Http\Controllers\JournalController;
use App\Modules\Finance\Http\Controllers\ReportController;
use App\Modules\Finance\Http\Controllers\IntegrationController;

Route::middleware(['web', 'auth'])->prefix('dashboard/finance')->name('finance.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Chart of Accounts
    Route::get('accounts/export', [ChartOfAccountController::class, 'export'])->name('accounts.export');
    Route::resource('accounts', ChartOfAccountController::class);

    // Journals
    Route::resource('journals', JournalController::class);
    Route::post('journals/{id}/post', [JournalController::class, 'post'])->name('journals.post');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('ledger', [ReportController::class, 'ledger'])->name('ledger');
        Route::get('cashbook', [ReportController::class, 'cashbook'])->name('cashbook');
        Route::get('bankbook', [ReportController::class, 'bankbook'])->name('bankbook');
        Route::get('summary', [ReportController::class, 'summary'])->name('summary');
        Route::get('dishonoured', [ReportController::class, 'dishonoured'])->name('dishonoured');
        Route::get('retained-earnings', [ReportController::class, 'retainedEarnings'])->name('retained-earnings');
        Route::get('bank-transfer', [ReportController::class, 'bankTransfer'])->name('bank-transfer');
        Route::get('voucher-wise', [ReportController::class, 'voucherWise'])->name('voucher-wise');
        Route::get('export', [ReportController::class, 'export'])->name('export');
    });

    // Settings / Integration
    Route::get('settings/mapping', [IntegrationController::class, 'index'])->name('settings.mapping');
    Route::post('settings/mapping', [IntegrationController::class, 'update'])->name('settings.mapping.update');
});
