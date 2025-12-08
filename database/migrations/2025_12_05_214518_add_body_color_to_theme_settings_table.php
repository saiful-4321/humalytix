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
            $table->string('body_color')->default('default')->after('layout_mode');
            $table->string('body_custom_color')->nullable()->after('body_color');
        });
    }

    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropColumn(['body_color', 'body_custom_color']);
        });
    }
};
