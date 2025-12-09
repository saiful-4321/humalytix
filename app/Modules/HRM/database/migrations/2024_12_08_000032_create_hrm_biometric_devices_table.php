<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('device_id')->unique();
            $table->string('ip_address');
            $table->integer('port')->default(4370);
            $table->string('location')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('hrm_branches')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->json('settings')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_biometric_devices');
    }
};
