<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Upgrade 'days' to decimal for Half-Day support
        Schema::table('hrm_leaves', function (Blueprint $table) {
            $table->decimal('days', 5, 2)->change(); // Support 0.5, 1.5 etc.
            $table->boolean('is_half_day')->default(false)->after('days');
            $table->string('half_day_session')->nullable()->after('is_half_day'); // 'first_half', 'second_half'
        });

        // 2. Add Industry Level Policy Flags
        Schema::table('hrm_leave_types', function (Blueprint $table) {
            $table->boolean('probation_restricted')->default(false)->after('require_approval'); // If true, cannot take in probation
            $table->boolean('apply_sandwich_rule')->default(false)->after('probation_restricted'); // If leave is Fri-Sun, Sat is counted
            $table->integer('max_consecutive_days')->default(0)->after('apply_sandwich_rule'); // Max days allowed at once
        });
    }

    public function down(): void
    {
        Schema::table('hrm_leaves', function (Blueprint $table) {
            $table->integer('days')->change();
            $table->dropColumn(['is_half_day', 'half_day_session']);
        });

        Schema::table('hrm_leave_types', function (Blueprint $table) {
            $table->dropColumn(['probation_restricted', 'apply_sandwich_rule', 'max_consecutive_days']);
        });
    }
};
