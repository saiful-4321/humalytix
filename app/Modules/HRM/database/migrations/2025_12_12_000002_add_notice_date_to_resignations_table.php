<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hrm_resignations', function (Blueprint $table) {
            $table->date('notice_date')->nullable()->after('resignation_date');
        });
    }

    public function down(): void
    {
        Schema::table('hrm_resignations', function (Blueprint $table) {
            $table->dropColumn('notice_date');
        });
    }
};
