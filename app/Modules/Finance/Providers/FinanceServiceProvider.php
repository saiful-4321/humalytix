<?php

namespace App\Modules\Finance\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class FinanceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $moduleName = 'Finance';
        
        // Load Routes
        if (File::exists(app_path('Modules/'.$moduleName.'/routes/web.php'))) {
            $this->loadRoutesFrom(app_path('Modules/'.$moduleName.'/routes/web.php'));
        }

        // Load Views
        if (File::isDirectory(app_path('Modules/'.$moduleName.'/resources/views'))) {
            $this->loadViewsFrom(app_path('Modules/'.$moduleName.'/resources/views'), $moduleName);
        }

        // Load Migrations
        if (File::isDirectory(app_path('Modules/'.$moduleName.'/database/migrations'))) {
            $this->loadMigrationsFrom(app_path('Modules/'.$moduleName.'/database/migrations'));
        }

        // Register Observers
        \App\Modules\HRM\Models\Payroll::observe(\App\Modules\Finance\Observers\PayrollObserver::class);
        \App\Modules\HRM\Models\Expense::observe(\App\Modules\Finance\Observers\ExpenseObserver::class);
    }
}
