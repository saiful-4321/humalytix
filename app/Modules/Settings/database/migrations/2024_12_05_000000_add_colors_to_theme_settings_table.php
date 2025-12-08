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
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->string('footer_color')->default('light')->after('sidebar_size');
            $table->string('sidebar_custom_color')->nullable()->after('sidebar_color');
            $table->string('topbar_custom_color')->nullable()->after('topbar_color');
            $table->string('footer_custom_color')->nullable()->after('footer_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropColumn(['footer_color', 'sidebar_custom_color', 'topbar_custom_color', 'footer_custom_color']);
        });
    }
};
