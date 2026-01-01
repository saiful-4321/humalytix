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
        Schema::create('hrm_workflow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('hrm_workflows')->onDelete('cascade');
            $table->morphs('workflowable'); // polymorphic relation to Leave, Expense, etc.
            $table->foreignId('current_step_id')->nullable()->constrained('hrm_workflow_steps')->onDelete('set null');
            $table->enum('status', ['pending', 'in_progress', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('step_history')->nullable(); // Track step progression
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // morphs() already creates index for workflowable_type and workflowable_id
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_workflow_runs');
    }
};
