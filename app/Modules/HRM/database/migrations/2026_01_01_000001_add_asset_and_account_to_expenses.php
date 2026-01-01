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
        Schema::table('hrm_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id')->nullable()->after('expense_category_id');
            // Assuming hrm_assets table exists
            // $table->foreign('asset_id')->references('id')->on('hrm_assets')->onDelete('set null');
        });

        Schema::table('hrm_expense_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('description');
            // Assuming finance_chart_of_accounts table exists
            // $table->foreign('account_id')->references('id')->on('finance_chart_of_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_expenses', function (Blueprint $table) {
            $table->dropColumn('asset_id');
        });

        Schema::table('hrm_expense_categories', function (Blueprint $table) {
            $table->dropColumn('account_id');
        });
    }
};
