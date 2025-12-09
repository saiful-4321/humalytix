<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hrm_asset_assignments', function (Blueprint $table) {
            $table->renameColumn('condition_at_assignment', 'assigned_condition');
            $table->renameColumn('condition_at_return', 'return_condition');
        });
    }

    public function down(): void
    {
        Schema::table('hrm_asset_assignments', function (Blueprint $table) {
            $table->renameColumn('assigned_condition', 'condition_at_assignment');
            $table->renameColumn('return_condition', 'condition_at_return');
        });
    }
};
