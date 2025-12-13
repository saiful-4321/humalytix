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
        Schema::table('hrm_resignations', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_resignations', 'notice_date')) {
                $table->date('notice_date')->nullable()->after('resignation_date');
            }
            if (Schema::hasColumn('hrm_resignations', 'last_working_date') && !Schema::hasColumn('hrm_resignations', 'last_working_day')) {
                $table->renameColumn('last_working_date', 'last_working_day');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_resignations', function (Blueprint $table) {
            $table->dropColumn('notice_date');
            $table->renameColumn('last_working_day', 'last_working_date');
        });
    }
};
