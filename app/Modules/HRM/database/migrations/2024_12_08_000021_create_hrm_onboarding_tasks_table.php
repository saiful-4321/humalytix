<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_process_id')->constrained('hrm_onboarding_processes')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('priority')->default('medium'); // low, medium, high
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['onboarding_process_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_onboarding_tasks');
    }
};
