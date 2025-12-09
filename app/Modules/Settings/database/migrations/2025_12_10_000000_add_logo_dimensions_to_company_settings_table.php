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
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('logo_white_height')->nullable()->after('logo_white');
            $table->string('logo_white_width')->nullable()->after('logo_white_height');
            $table->string('logo_dark_height')->nullable()->after('logo_dark');
            $table->string('logo_dark_width')->nullable()->after('logo_dark_height');
            $table->string('logo_white_small_height')->nullable()->after('logo_white_small');
            $table->string('logo_white_small_width')->nullable()->after('logo_white_small_height');
            $table->string('logo_dark_small_height')->nullable()->after('logo_dark_small');
            $table->string('logo_dark_small_width')->nullable()->after('logo_dark_small_height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_white_height',
                'logo_white_width',
                'logo_dark_height',
                'logo_dark_width',
                'logo_white_small_height',
                'logo_white_small_width',
                'logo_dark_small_height',
                'logo_dark_small_width',
            ]);
        });
    }
};
