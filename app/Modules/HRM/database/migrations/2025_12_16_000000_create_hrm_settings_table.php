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
        Schema::create('hrm_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index(); // e.g., 'leave', 'payroll'
            $table->string('name')->unique(); // e.g., 'weekly_holiday'
            $table->json('payload')->nullable(); // e.g., ['Friday', 'Saturday']
            $table->boolean('is_system')->default(false); // If true, cannot be deleted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_settings');
    }
};
