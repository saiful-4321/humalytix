<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Leave Allocations
        Schema::create('hrm_leave_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained('hrm_leave_types')->onDelete('cascade');
            $table->year('year');
            $table->float('allocated_days')->default(0);
            $table->float('used_days')->default(0);
            $table->float('carried_over_days')->default(0); // From previous year
            $table->timestamps();

            $table->unique(['employee_id', 'leave_type_id', 'year']);
        });

        // 2. Approval Chains (The Workflow Definition)
        Schema::create('hrm_approval_chains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 3. Approval Chain Levels (The Steps)
        Schema::create('hrm_approval_chain_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_chain_id')->constrained('hrm_approval_chains')->onDelete('cascade');
            $table->integer('level'); // 1, 2, 3...
            
            // 'reporting_manager', 'specific_user', 'designation', 'department_head'
            $table->string('approver_type'); 
            
            // If specific_user -> user_id, if designation -> designation_name, if reporting_manager -> null
            $table->string('approver_value')->nullable(); 
            
            $table->timestamps();
        });

        // 4. Update Leave Types to link to a chain
        Schema::table('hrm_leave_types', function (Blueprint $table) {
            $table->foreignId('approval_chain_id')->nullable()->after('description')->constrained('hrm_approval_chains')->onDelete('set null');
        });

        // 5. Update Leaves to track progress
        Schema::table('hrm_leaves', function (Blueprint $table) {
            $table->foreignId('approval_chain_id')->nullable()->after('leave_type_id')->constrained('hrm_approval_chains')->onDelete('set null');
            $table->integer('current_level')->default(1)->after('approval_chain_id'); // Which level is pending?
            $table->boolean('is_completed')->default(false)->after('current_level');
        });

        // 6. Approval Logs (Audit Trail)
        Schema::create('hrm_leave_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_id')->constrained('hrm_leaves')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users'); // Who acted?
            $table->integer('level'); // Which level was this?
            $table->string('status'); // approved, rejected
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_leave_approval_logs');
        
        Schema::table('hrm_leaves', function (Blueprint $table) {
            $table->dropForeign(['approval_chain_id']);
            $table->dropColumn(['approval_chain_id', 'current_level', 'is_completed']);
        });

        Schema::table('hrm_leave_types', function (Blueprint $table) {
            $table->dropForeign(['approval_chain_id']);
            $table->dropColumn('approval_chain_id');
        });

        Schema::dropIfExists('hrm_approval_chain_levels');
        Schema::dropIfExists('hrm_approval_chains');
        Schema::dropIfExists('hrm_leave_allocations');
    }
};
