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
        Schema::create('hrm_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('module_type'); // leave, expense, appraisal, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('trigger_event')->default('created'); // created, updated
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hrm_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('hrm_workflows')->onDelete('cascade');
            $table->integer('step_order'); // 1, 2, 3
            $table->string('step_name');
            $table->string('approver_type'); // role, specific_user, reporting_manager, department_head
            $table->foreignId('approver_id')->nullable(); // ID of role/user if applicable
            $table->json('conditions')->nullable(); // {"min_amount": 5000}
            $table->string('auto_action')->nullable(); // approve, reject, notify_only
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_workflow_steps');
        Schema::dropIfExists('hrm_workflows');
    }
};
