<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hrm_leave_types', function (Blueprint $table) {
            $table->boolean('is_unlimited')->default(false)->after('days_per_year');
            $table->decimal('accrual_rate', 5, 2)->default(0)->comment('Days per month');
            $table->boolean('allow_encashment')->default(false);
            $table->boolean('allow_carry_forward')->default(false);
            $table->integer('max_carry_forward_days')->default(0);
            $table->boolean('allow_negative_balance')->default(false);
            $table->integer('max_negative_balance')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('hrm_leave_types', function (Blueprint $table) {
            $table->dropColumn([
                'is_unlimited', 
                'accrual_rate', 
                'allow_encashment', 
                'allow_carry_forward',
                'max_carry_forward_days',
                'allow_negative_balance',
                'max_negative_balance'
            ]);
        });
    }
};
