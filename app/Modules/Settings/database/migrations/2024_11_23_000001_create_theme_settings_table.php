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
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('layout_type')->default('vertical');
            $table->string('layout_width')->default('fluid');
            $table->string('layout_position')->default('fixed');
            $table->string('topbar_color')->default('light');
            $table->string('sidebar_color')->default('dark');
            $table->string('sidebar_size')->default('lg');
            $table->string('layout_mode')->default('light');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
