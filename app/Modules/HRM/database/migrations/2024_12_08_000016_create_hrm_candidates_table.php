<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_code')->unique();
            $table->foreignId('job_id')->constrained('hrm_jobs')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('current_location')->nullable();
            $table->decimal('expected_salary', 15, 2)->nullable();
            $table->integer('notice_period')->nullable(); // in days
            $table->integer('total_experience')->nullable(); // in years
            $table->string('resume_path')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('source')->nullable(); // website, referral, linkedin, job_portal, walk_in
            $table->foreignId('referred_by')->nullable()->constrained('hrm_employees')->onDelete('set null');
            $table->string('stage')->default('applied'); // applied, screening, interview, offered, hired, rejected, withdrawn
            $table->string('status')->default('active'); // active, inactive
            $table->date('applied_date');
            $table->integer('rating')->nullable(); // 1-5
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_id', 'stage']);
            $table->index('email');
            $table->index('stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_candidates');
    }
};
