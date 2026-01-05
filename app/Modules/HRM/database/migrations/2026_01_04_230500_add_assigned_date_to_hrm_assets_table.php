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
        Schema::table('hrm_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_assets', 'assigned_date')) {
                $table->date('assigned_date')->nullable()->after('employee_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_assets', function (Blueprint $table) {
             if (Schema::hasColumn('hrm_assets', 'assigned_date')) {
                $table->dropColumn('assigned_date');
             }
        });
    }
};
