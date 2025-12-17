<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hrm_leave_types', function (Blueprint $table) {
            $table->string('accrual_frequency')->default('yearly')->after('accrual_rate'); // monthly, yearly
            $table->integer('encashment_limit')->default(0)->after('allow_encashment');
            $table->boolean('require_approval')->default(true)->after('encashment_limit');
            $table->boolean('requires_attachment')->default(false)->after('require_approval');
        });
    }

    public function down(): void
    {
        Schema::table('hrm_leave_types', function (Blueprint $table) {
            $table->dropColumn([
                'accrual_frequency',
                'encashment_limit',
                'require_approval',
                'requires_attachment'
            ]);
        });
    }
};
