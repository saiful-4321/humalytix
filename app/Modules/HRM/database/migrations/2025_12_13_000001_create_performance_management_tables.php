<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KPIs/KRAs Table
        Schema::create('hrm_kpis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['kpi', 'kra'])->default('kpi');
            $table->foreignId('department_id')->nullable()->constrained('hrm_departments')->cascadeOnDelete();
            $table->string('measurement_unit')->nullable(); // %, Numbers, Currency, etc
            $table->decimal('target_value', 10, 2)->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'annually'])->default('monthly');
            $table->integer('weightage')->default(0); // Percentage weight
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // OKRs Table
        Schema::create('hrm_okrs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('level', ['company', 'department', 'individual'])->default('individual');
            $table->foreignId('department_id')->nullable()->constrained('hrm_departments')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('hrm_employees')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4'])->nullable();
            $table->integer('year');
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->integer('progress')->default(0); // 0-100
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // OKR Key Results
        Schema::create('hrm_okr_key_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_id')->constrained('hrm_okrs')->cascadeOnDelete();
            $table->string('description');
            $table->string('measurement_unit')->nullable();
            $table->decimal('target_value', 10, 2);
            $table->decimal('current_value', 10, 2)->default(0);
            $table->integer('progress')->default(0); // 0-100
            $table->integer('weightage')->default(25); // Total should be 100
            $table->timestamps();
        });

        // Performance Goals
        Schema::create('hrm_performance_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('kpi_id')->nullable()->constrained('hrm_kpis')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('due_date');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'cancelled'])->default('not_started');
            $table->integer('progress')->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 360 Degree Appraisals
        Schema::create('hrm_360_appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->string('appraisal_name');
            $table->string('review_period'); // e.g., "Q1 2024", "Annual 2024"
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->integer('overall_score')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Appraisal Reviewers (for 360°)
        Schema::create('hrm_appraisal_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained('hrm_360_appraisals')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->enum('reviewer_type', ['self', 'manager', 'peer', 'subordinate', 'customer'])->default('peer');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->integer('rating')->nullable(); // 1-10
            $table->text('feedback')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Competency Framework
        Schema::create('hrm_competencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['core', 'functional', 'leadership'])->default('core');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Appraisal Competency Ratings
        Schema::create('hrm_appraisal_competency_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_reviewer_id')->constrained('hrm_appraisal_reviewers')->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained('hrm_competencies')->cascadeOnDelete();
            $table->integer('rating'); // 1-5 or 1-10
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        // Performance Improvement Plans
        Schema::create('hrm_pips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->string('title');
            $table->text('reason');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('review_date')->nullable();
            $table->enum('status', ['active', 'successful', 'unsuccessful', 'extended', 'cancelled'])->default('active');
            $table->text('success_criteria')->nullable();
            $table->text('outcomes')->nullable();
            $table->text('final_review')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // PIP Action Items
        Schema::create('hrm_pip_action_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pip_id')->constrained('hrm_pips')->cascadeOnDelete();
            $table->text('action_required');
            $table->string('expected_outcome')->nullable();
            $table->date('due_date');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('evidence')->nullable();
            $table->timestamps();
        });

        // PIP Progress Reviews
        Schema::create('hrm_pip_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pip_id')->constrained('hrm_pips')->cascadeOnDelete();
            $table->date('review_date');
            $table->text('progress_summary');
            $table->integer('rating')->nullable(); // 1-10
            $table->text('manager_comments')->nullable();
            $table->text('employee_comments')->nullable();
            $table->foreignId('reviewed_by')->constrained('hrm_employees');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_pip_reviews');
        Schema::dropIfExists('hrm_pip_action_items');
        Schema::dropIfExists('hrm_pips');
        Schema::dropIfExists('hrm_appraisal_competency_ratings');
        Schema::dropIfExists('hrm_competencies');
        Schema::dropIfExists('hrm_appraisal_reviewers');
        Schema::dropIfExists('hrm_360_appraisals');
        Schema::dropIfExists('hrm_performance_goals');
        Schema::dropIfExists('hrm_okr_key_results');
        Schema::dropIfExists('hrm_okrs');
        Schema::dropIfExists('hrm_kpis');
    }
};
