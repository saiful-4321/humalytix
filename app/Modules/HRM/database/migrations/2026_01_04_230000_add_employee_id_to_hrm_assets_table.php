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
            if (!Schema::hasColumn('hrm_assets', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->after('status')->constrained('hrm_employees')->onDelete('set null');
                $table->index('employee_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_assets', function (Blueprint $table) {
             if (Schema::hasColumn('hrm_assets', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
             }
        });
    }
};
