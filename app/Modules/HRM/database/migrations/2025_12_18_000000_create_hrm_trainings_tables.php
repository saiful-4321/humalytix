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
        // 1. Trainings (Programs/Courses)
        Schema::create('hrm_trainings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // TRN-001
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('trainer')->nullable(); // Internal or External Trainer Name
            $table->string('type')->default('internal'); // internal, external, online
            $table->integer('duration_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Training Sessions (Scheduled Instances)
        Schema::create('hrm_training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('hrm_trainings')->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('location')->nullable(); // Meeting Room A, Zoom Link, etc.
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled
            $table->integer('max_participants')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Training Participants (Enrollments)
        Schema::create('hrm_training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained('hrm_training_sessions')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->string('status')->default('enrolled'); // enrolled, attended, completed, no_show, failed
            $table->date('completion_date')->nullable();
            $table->text('feedback')->nullable(); // Employee feedback
            $table->integer('score')->nullable(); // Assessment score if applicable
            $table->timestamps();
            
            $table->unique(['training_session_id', 'employee_id']); // Prevent double enrollment
        });

        // 4. Certifications
        Schema::create('hrm_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->string('name'); // PMP, AWS Cloud Practitioner
            $table->string('issuing_organization'); // PMI, AWS, Google
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('does_never_expire')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_certifications');
        Schema::dropIfExists('hrm_training_participants');
        Schema::dropIfExists('hrm_training_sessions');
        Schema::dropIfExists('hrm_trainings');
    }
};
