<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Asset Integration
        Schema::table('hrm_asset_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('depreciation_expense_account_id')->nullable();
            $table->unsignedBigInteger('accumulated_depreciation_account_id')->nullable();
            // Foreign keys if possible (finance tables in same DB)
            // $table->foreign('depreciation_expense_account_id')->references('id')->on('finance_chart_of_accounts');
        });

        // 2. Voucher Types
        Schema::table('finance_journals', function (Blueprint $table) {
            $table->enum('type', ['journal', 'payment', 'receipt', 'contra', 'depreciation', 'payroll'])->default('journal')->after('status');
        });

        // 3. Payroll Granular Mapping
        Schema::table('finance_payroll_mappings', function (Blueprint $table) {
             $table->unsignedBigInteger('salary_component_id')->nullable()->after('component_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_payroll_mappings', function (Blueprint $table) {
             $table->dropColumn('salary_component_id');
        });

        Schema::table('finance_journals', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('hrm_asset_categories', function (Blueprint $table) {
            $table->dropColumn(['depreciation_expense_account_id', 'accumulated_depreciation_account_id']);
        });
    }
};
