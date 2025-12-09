<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hrm_assets', function (Blueprint $table) {
            $table->renameColumn('asset_code', 'code');
            $table->renameColumn('category', 'type');
            $table->renameColumn('purchase_price', 'purchase_cost');
        });

        Schema::table('hrm_assets', function (Blueprint $table) {
            $table->string('condition')->nullable()->after('type'); // Add condition column
        });
    }

    public function down(): void
    {
        Schema::table('hrm_assets', function (Blueprint $table) {
            $table->dropColumn('condition');
            $table->renameColumn('purchase_cost', 'purchase_price');
            $table->renameColumn('type', 'category');
            $table->renameColumn('code', 'asset_code');
        });
    }
};
